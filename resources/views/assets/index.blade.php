@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between" data-page-heading>
        <div>
            <p class="text-sm uppercase tracking-[0.3em] text-cyan-700">Data Aset</p>
            <h2 class="mt-2 text-2xl font-semibold sm:text-3xl">Daftar Aset BMD</h2>
        </div>
        @if (auth()->user()->isAdmin())
            <div class="grid gap-3 sm:flex sm:flex-wrap">
                <button type="button" data-open-print-modal class="self-start rounded-2xl border border-cyan-200 bg-cyan-50 px-5 py-3 text-sm font-semibold text-cyan-700">Export QR ke Word</button>
                <a href="{{ route('assets.create') }}" class="self-start rounded-2xl bg-slate-950 px-5 py-3 text-center text-sm font-semibold text-white">Tambah Aset Baru</a>
            </div>
        @endif
    </div>

    <div class="mt-6 space-y-4 md:hidden">
        @forelse ($assets as $asset)
            <article class="rounded-3xl bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-xs uppercase tracking-[0.24em] text-cyan-700">Kode Aset</p>
                        <h3 class="mt-2 break-words text-base font-semibold text-slate-950">{{ $asset->asset_code }}</h3>
                        <p class="mt-1 break-words text-sm leading-6 text-slate-500">{{ $asset->name }}</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold capitalize text-slate-700">{{ $asset->condition }}</span>
                </div>

                <div class="mt-4 grid gap-3 text-sm">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Kategori</p>
                        <p class="mt-2 break-words font-medium text-slate-800">{{ $asset->category }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Lokasi</p>
                        <p class="mt-2 break-words font-medium text-slate-800">{{ $asset->location }}</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    <a href="{{ route('assets.show', $asset) }}" class="rounded-2xl bg-slate-100 px-4 py-3 text-center text-sm font-semibold text-slate-700">Detail</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('assets.export.word', $asset) }}" class="rounded-2xl bg-cyan-50 px-4 py-3 text-center text-sm font-semibold text-cyan-700">Export Word</a>
                        <a href="{{ route('assets.edit', $asset) }}" class="rounded-2xl bg-amber-50 px-4 py-3 text-center text-sm font-semibold text-amber-700">Ubah</a>
                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Hapus aset ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-2xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">Hapus</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-3xl bg-white px-6 py-10 text-center text-slate-500 shadow-sm">
                Belum ada aset yang tersimpan.
            </div>
        @endforelse
    </div>

    <div class="mt-5 hidden overflow-hidden rounded-3xl bg-white shadow-sm md:block" data-page-card>
        <div class="overflow-x-auto">
            <table class="min-w-full table-fixed text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="w-[14%] px-5 py-3.5">Kode</th>
                        <th class="w-[18%] px-5 py-3.5">Nama</th>
                        <th class="w-[18%] px-5 py-3.5">Kategori</th>
                        <th class="w-[18%] px-5 py-3.5">Lokasi</th>
                        <th class="w-[10%] px-5 py-3.5">Kondisi</th>
                        <th class="w-[22%] px-5 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($assets as $asset)
                        <tr>
                            <td class="px-5 py-3.5 align-top font-semibold break-words whitespace-normal">{{ $asset->asset_code }}</td>
                            <td class="px-5 py-3.5 align-top break-words whitespace-normal">{{ $asset->name }}</td>
                            <td class="px-5 py-3.5 align-top break-words whitespace-normal">{{ $asset->category }}</td>
                            <td class="px-5 py-3.5 align-top break-words whitespace-normal">{{ $asset->location }}</td>
                            <td class="px-5 py-3.5 align-top capitalize break-words whitespace-normal">{{ $asset->condition }}</td>
                            <td class="px-5 py-3.5 align-top">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('assets.show', $asset) }}" class="rounded-xl bg-slate-100 px-3 py-1.5">Detail</a>
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('assets.export.word', $asset) }}" class="rounded-xl bg-cyan-50 px-3 py-1.5 text-cyan-700">Export Word</a>
                                        <a href="{{ route('assets.edit', $asset) }}" class="rounded-xl bg-amber-50 px-3 py-1.5 text-amber-700">Ubah</a>
                                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Hapus aset ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl bg-rose-50 px-3 py-1.5 text-rose-700">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada aset yang tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">
            {{ $assets->links() }}
        </div>
    </div>

    @if (auth()->user()->isAdmin())
        <div id="print-selection-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4">
            <div class="max-h-[92vh] w-full max-w-4xl overflow-hidden rounded-[2rem] bg-white shadow-2xl">
                <div class="border-b border-slate-100 px-5 py-5 sm:px-6">
                    <p class="text-sm uppercase tracking-[0.3em] text-cyan-700">Export Word</p>
                    <h3 class="mt-2 text-xl font-semibold text-slate-900 sm:text-2xl">Pilih Aset yang Mau Diexport</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Sistem akan membuat file Word berisi gambar QR tiap aset dan tabel informasi barang termasuk penanggung jawab.</p>
                </div>

                <form method="POST" action="{{ route('assets.export.word.bulk') }}" class="flex max-h-[calc(92vh-96px)] flex-col" data-loading-form data-print-selection-form data-selection-endpoint="{{ route('assets.selection') }}" data-initial-selected='@json(collect(old('asset_ids', []))->map(fn ($id) => (int) $id)->values())'>
                    @csrf
                    <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                            <input type="search" name="search" placeholder="Cari kode, register, nama, atau lokasi" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" data-asset-search>
                            <label class="inline-flex items-center gap-3 text-sm font-medium text-slate-700">
                                <input type="checkbox" data-select-visible-assets class="h-4 w-4 rounded border-slate-300 text-cyan-600">
                                Pilih semua hasil yang tampil
                            </label>
                        </div>
                        <p class="text-sm text-slate-500"><span data-selected-count>0</span> aset dipilih</p>
                    </div>

                    <div class="overflow-y-auto px-5 py-4 sm:px-6">
                        <div class="space-y-3" data-asset-results></div>
                        <p class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" data-selection-error></p>
                        <div class="hidden rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500" data-selection-empty>
                            Tidak ada aset yang cocok dengan pencarian ini.
                        </div>
                        <div class="hidden justify-center pt-4" data-selection-load-more-wrap>
                            <button type="button" class="rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700" data-selection-load-more>Muat lebih banyak</button>
                        </div>
                        <div class="hidden py-8 text-center text-sm text-slate-500" data-selection-loading>Memuat daftar aset...</div>
                        <div data-selection-hidden-inputs></div>
                        @error('asset_ids')
                            <p class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-5 sm:flex-row sm:justify-end sm:px-6">
                        <button type="button" data-close-print-modal class="rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700">Batal</button>
                        <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Export Word Aset Terpilih</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
