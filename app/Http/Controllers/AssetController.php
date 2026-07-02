<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkPrintAssetRequest;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\ActivityLog;
use App\Models\Asset;
use chillerlan\QRCode\QRCode as ChillerlanQRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Style\Language;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    public function index()
    {
        return view('assets.index', [
            'assets' => Asset::latest()->paginate(10),
        ]);
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('assets.create');
    }

    public function store(StoreAssetRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $data['qr_code_path'] = '';
        $data['qr_target_url'] = '';
        $photoPath = null;

        try {
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('assets/photos', 'public');
                $data['photo_path'] = $photoPath;
            }

            $asset = DB::transaction(function () use ($data) {
                $asset = Asset::create($data);
                $targetUrl = route('assets.public.show', $asset);
                $qrPath = 'assets/qrcodes/'.$asset->asset_code.'.svg';
                $qrSvg = QrCode::format('svg')->size(280)->margin(1)->generate($targetUrl);

                Storage::disk('public')->put($qrPath, $qrSvg);

                $asset->update([
                    'qr_code_path' => $qrPath,
                    'qr_target_url' => $targetUrl,
                ]);

                return $asset->refresh();
            });
        } catch (\Throwable $exception) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            throw $exception;
        }

        $this->logActivity($request, 'create_asset', $asset, 'Menambahkan aset baru.');

        return redirect()->route('assets.show', $asset)->with('status', 'Aset berhasil ditambahkan. QR siap didownload atau diexport ke Word.');
    }

    public function show(Asset $asset)
    {
        $qrMarkup = null;

        if ($asset->qr_code_path && Storage::disk('public')->exists($asset->qr_code_path)) {
            $qrMarkup = Storage::disk('public')->get($asset->qr_code_path);
        }

        return view('assets.show', compact('asset', 'qrMarkup'));
    }

    public function publicShow(Asset $asset)
    {
        return view('assets.public-show', compact('asset'));
    }

    public function publicLookup(Asset $asset)
    {
        $photoUrl = $asset->photo_path ? url('storage/'.$asset->photo_path) : null;

        return response()->json([
            'asset_code' => $asset->asset_code,
            'register_number' => $asset->register_number,
            'name' => $asset->name,
            'category' => $asset->category,
            'brand' => $asset->brand,
            'year_acquired' => $asset->year_acquired,
            'location' => $asset->location,
            'person_in_charge' => $asset->person_in_charge,
            'is_in_use' => $asset->is_in_use,
            'condition' => $asset->condition,
            'description' => $asset->description,
            'photo_url' => $photoUrl,
            'detail_url' => route('assets.public.show', $asset),
        ]);
    }

    public function edit(Asset $asset)
    {
        $this->ensureAdmin();

        return view('assets.edit', compact('asset'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $data = $request->validated();
        $oldCondition = $asset->condition;
        $originalQrCodePath = $asset->qr_code_path;
        $originalQrTargetUrl = $asset->qr_target_url;
        $oldPhotoPath = $asset->photo_path;
        $newPhotoPath = null;

        try {
            if ($request->hasFile('photo')) {
                $newPhotoPath = $request->file('photo')->store('assets/photos', 'public');
                $data['photo_path'] = $newPhotoPath;
            }

            $data['updated_by'] = Auth::id();
            $data['qr_code_path'] = $originalQrCodePath;
            $data['qr_target_url'] = $originalQrTargetUrl;

            DB::transaction(function () use ($asset, $data) {
                $asset->update($data);
            });
        } catch (\Throwable $exception) {
            if ($newPhotoPath) {
                Storage::disk('public')->delete($newPhotoPath);
            }

            throw $exception;
        }

        if ($newPhotoPath && $oldPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        $this->logActivity($request, 'update_asset', $asset, 'Memperbarui data aset.', [
            'old_condition' => $oldCondition,
            'new_condition' => $asset->condition,
        ]);

        return redirect()->route('assets.show', $asset)->with('status', 'Data aset berhasil diperbarui. QR lama tetap dipakai dan tidak dibuat ulang.');
    }

    public function destroy(Request $request, Asset $asset)
    {
        $this->ensureAdmin();

        $label = $asset->asset_code.' - '.$asset->name;
        $photoPath = $asset->photo_path;
        $qrCodePath = $asset->qr_code_path;

        DB::transaction(function () use ($asset) {
            $asset->delete();
        });

        if ($photoPath) {
            Storage::disk('public')->delete($photoPath);
        }

        if ($qrCodePath) {
            Storage::disk('public')->delete($qrCodePath);
        }

        $this->logActivity($request, 'delete_asset', null, 'Menghapus aset.', [
            'asset' => $label,
        ]);

        return redirect()->route('assets.index')->with('status', 'Aset berhasil dihapus.');
    }

    public function exportWord(Request $request, Asset $asset)
    {
        $this->ensureAdmin();

        $asset->update([
            'last_printed_at' => now(),
        ]);

        $filename = $asset->asset_code.'-qr-asset.docx';

        $this->logActivity($request, 'export_word_asset', $asset, 'Export QR aset ke Word.', [
            'filename' => $filename,
            'export_type' => 'single',
        ]);

        return $this->downloadWordDocument(collect([$asset]), $filename);
    }

    public function bulkExportWord(BulkPrintAssetRequest $request)
    {
        $selectedIds = collect($request->validated('asset_ids'))->map(fn ($id) => (int) $id)->values();
        $assets = Asset::whereIn('id', $selectedIds)->get()->sortBy(fn ($asset) => $selectedIds->search($asset->id))->values();

        abort_if($assets->isEmpty(), 422, 'Aset yang dipilih untuk export tidak ditemukan.');

        Asset::whereIn('id', $selectedIds)->update([
            'last_printed_at' => now(),
        ]);

        $filename = 'qr-aset-bmd.docx';

        $this->logActivity($request, 'export_word_assets_bulk', null, 'Export QR beberapa aset ke Word.', [
            'asset' => $this->buildAssetLogSummary($assets),
            'filename' => $filename,
            'export_type' => 'bulk',
            'total_assets' => $assets->count(),
            'assets' => $assets->map(fn (Asset $asset) => [
                'id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'name' => $asset->name,
                'person_in_charge' => $asset->person_in_charge,
            ])->all(),
        ]);

        return $this->downloadWordDocument($assets, $filename);
    }

    public function selection(Request $request): JsonResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $search = trim((string) ($validated['q'] ?? ''));
        $assets = Asset::query()
            ->select(['id', 'asset_code', 'register_number', 'name', 'location', 'last_printed_at'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('asset_code', 'like', $search.'%')
                        ->orWhere('register_number', 'like', $search.'%')
                        ->orWhere('name', 'like', '%'.$search.'%')
                        ->orWhere('location', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(20)
            ->through(function (Asset $asset) {
                return [
                    'id' => $asset->id,
                    'asset_code' => $asset->asset_code,
                    'register_number' => $asset->register_number,
                    'name' => $asset->name,
                    'location' => $asset->location,
                    'last_printed_at' => $asset->last_printed_at?->format('d M Y H:i'),
                ];
            });

        return response()->json($assets);
    }

    public function download(Asset $asset)
    {
        $this->ensureAdmin();

        abort_unless($asset->qr_code_path && Storage::disk('public')->exists($asset->qr_code_path), 404, 'File QR tidak ditemukan.');

        return Storage::disk('public')->download($asset->qr_code_path, $asset->asset_code.'-qrcode.svg');
    }

    private function ensureAdmin(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
    }

    private function logActivity(Request $request, string $action, ?Asset $asset, string $description, array $properties = []): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $asset ? Asset::class : null,
            'subject_id' => $asset?->id,
            'subject_label' => $asset ? $asset->asset_code.' - '.$asset->name : ($properties['asset'] ?? null),
            'description' => $description,
            'properties' => $properties ?: null,
            'ip_address' => $request->ip(),
        ]);
    }

    private function downloadWordDocument($assets, string $filename)
    {
        $phpWord = new PhpWord();
        $phpWord->getSettings()->setThemeFontLang(new Language('id-ID'));
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $sectionStyle = [
            'marginTop' => 700,
            'marginBottom' => 700,
            'marginLeft' => 900,
            'marginRight' => 900,
        ];

        $tempFiles = [];
        $docxPath = storage_path('app/private/exports/'.uniqid('qr-word-', true).'.docx');

        if (! is_dir(dirname($docxPath))) {
            mkdir(dirname($docxPath), 0777, true);
        }

        foreach ($assets as $index => $asset) {
            $section = $phpWord->addSection($sectionStyle);

            if ($index > 0) {
                $section->addPageBreak();
            }

            $section->addText('QR Aset BMD', ['bold' => true, 'size' => 18], ['alignment' => Jc::CENTER, 'spaceAfter' => 120]);
            $section->addText($asset->name, ['bold' => true, 'size' => 16], ['alignment' => Jc::CENTER, 'spaceAfter' => 120]);
            $section->addText($asset->asset_code.' - '.$asset->location, ['size' => 11, 'color' => '4B5563'], ['alignment' => Jc::CENTER, 'spaceAfter' => 240]);

            $qrPath = storage_path('app/private/exports/'.uniqid($asset->asset_code.'-qr-', true).'.png');
            $qrOptions = new QROptions([
                'outputInterface' => QRGdImagePNG::class,
                'scale' => 10,
                'outputBase64' => false,
            ]);

            $qrTargetUrl = $asset->qr_target_url ?: route('assets.public.show', $asset);
            $qrPng = (new ChillerlanQRCode($qrOptions))->render($qrTargetUrl);

            if (str_starts_with($qrPng, 'data:image/png;base64,')) {
                $qrPng = base64_decode(substr($qrPng, 22)) ?: '';
            }

            if ($qrPng === '') {
                throw new \RuntimeException('QR PNG tidak berhasil dibuat untuk export Word.');
            }

            file_put_contents($qrPath, $qrPng);

            $tempFiles[] = $qrPath;

            $section->addImage($qrPath, [
                'width' => 170,
                'height' => 170,
                'alignment' => Jc::CENTER,
            ]);

            $section->addTextBreak(1);

            $table = $section->addTable([
                'alignment' => JcTable::CENTER,
                'borderSize' => 6,
                'borderColor' => 'B7D7F6',
                'cellMarginTop' => 100,
                'cellMarginBottom' => 100,
                'cellMarginLeft' => 160,
                'cellMarginRight' => 160,
            ]);

            foreach ($this->buildWordRows($asset) as [$label, $value]) {
                $table->addRow();
                $table->addCell(3600, ['bgColor' => 'EAF6FF'])->addText($label, ['bold' => true, 'size' => 11]);
                $table->addCell(6900)->addText($value, ['size' => 11]);
            }
        }

        IOFactory::createWriter($phpWord, 'Word2007')->save($docxPath);

        foreach ($tempFiles as $tempFile) {
            if (is_file($tempFile)) {
                unlink($tempFile);
            }
        }

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    private function buildWordRows(Asset $asset): array
    {
        return [
            ['Nama Barang', $asset->name ?: '-'],
            ['Kode Barang', $asset->asset_code ?: '-'],
            ['Nomor Register', $asset->register_number ?: '-'],
            ['Merk / Type', $asset->brand ?: '-'],
            ['Tahun Perolehan', $asset->year_acquired ? (string) $asset->year_acquired : '-'],
            ['Kondisi', $asset->condition ? ucfirst($asset->condition) : '-'],
            ['Penanggung Jawab', $asset->person_in_charge ?: '-'],
            ['Lokasi Barang', $asset->location ?: '-'],
            ['Keterangan', $asset->description ?: '-'],
        ];
    }

    private function buildAssetLogSummary($assets): string
    {
        $labels = $assets
            ->map(fn (Asset $asset) => $asset->asset_code.' - '.$asset->name)
            ->take(3)
            ->implode(', ');

        if ($assets->count() <= 3) {
            return $labels;
        }

        return $labels.', dan '.($assets->count() - 3).' aset lainnya';
    }
}
