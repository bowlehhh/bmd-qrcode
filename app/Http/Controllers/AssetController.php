<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkPrintAssetRequest;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\ActivityLog;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        return redirect()->route('assets.print', $asset)->with('status', 'Aset berhasil ditambahkan. Barcode QR siap dicetak.');
    }

    public function show(Asset $asset)
    {
        return view('assets.show', compact('asset'));
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

    public function print(Asset $asset)
    {
        $this->ensureAdmin();

        $asset->update([
            'last_printed_at' => now(),
        ]);

        return view('assets.print', compact('asset'));
    }

    public function bulkPrint(BulkPrintAssetRequest $request)
    {
        $selectedIds = collect($request->validated('asset_ids'))->map(fn ($id) => (int) $id)->values();
        $assets = Asset::whereIn('id', $selectedIds)->get()->sortBy(fn ($asset) => $selectedIds->search($asset->id))->values();

        Asset::whereIn('id', $selectedIds)->update([
            'last_printed_at' => now(),
        ]);

        return view('assets.print-bulk', [
            'assetGroups' => $assets->chunk(4),
        ]);
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
}
