@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between" data-page-heading>
        <div>
            <p class="text-sm uppercase tracking-[0.3em] text-cyan-700">Detail Aset</p>
            <h2 class="mt-2 text-3xl font-semibold">{{ $asset->name }}</h2>
            <p class="mt-2 text-slate-500">{{ $asset->asset_code }} - {{ $asset->location }}</p>
            <p class="mt-3 inline-flex rounded-full bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700">QR tetap sama meski data barang diperbarui.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('assets.index') }}" class="rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Kembali</a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('assets.download', $asset) }}" class="rounded-2xl border border-cyan-200 bg-cyan-50 px-5 py-3 text-sm font-semibold text-cyan-700">Download QR</a>
                <a href="{{ route('assets.export.word', $asset) }}" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Export ke Word</a>
            @endif
        </div>
    </div>

    <div class="mt-5 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]" data-page-grid>
        <section class="rounded-3xl bg-white p-6 shadow-sm" data-page-card>
            <dl class="grid gap-4 md:grid-cols-2">
                <div>
                    <dt class="text-sm text-slate-500">Kode Barang</dt>
                    <dd class="mt-1 font-semibold">{{ $asset->asset_code }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">Nomor Register</dt>
                    <dd class="mt-1 font-semibold">{{ $asset->register_number ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">Merk / Type</dt>
                    <dd class="mt-1 font-semibold">{{ $asset->brand ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">Tahun</dt>
                    <dd class="mt-1 font-semibold">{{ $asset->year_acquired ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">Lokasi</dt>
                    <dd class="mt-1 font-semibold">{{ $asset->location }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">Penanggung Jawab</dt>
                    <dd class="mt-1 font-semibold">{{ $asset->person_in_charge ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">Kondisi</dt>
                    <dd class="mt-1 font-semibold capitalize">{{ $asset->condition }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm text-slate-500">Keterangan</dt>
                    <dd class="mt-1 leading-6">{{ $asset->description ?: '-' }}</dd>
                </div>
            </dl>
        </section>

        <section class="space-y-6">
            <div class="rounded-3xl bg-white p-6 shadow-sm" data-page-card>
                <p class="text-sm text-slate-500">QR Code Aset</p>
                <div class="mt-4 flex justify-center rounded-3xl bg-slate-50 p-4">
                    @if ($qrMarkup)
                        <div class="h-48 w-48 [&_svg]:h-full [&_svg]:w-full">
                            {!! $qrMarkup !!}
                        </div>
                    @else
                        <div class="flex h-48 w-48 items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-white px-4 text-center text-sm leading-6 text-slate-500">
                            File QR belum tersedia atau tidak ditemukan.
                        </div>
                    @endif
                </div>
                <p class="mt-4 text-sm leading-6 text-slate-500">Kode QR ini tetap dipakai. Jika nama, lokasi, kondisi, atau keterangan diubah, hasil scan akan menampilkan data terbaru tanpa membuat QR baru.</p>
            </div>
        </section>
    </div>
@endsection
