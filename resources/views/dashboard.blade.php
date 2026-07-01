@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between" data-page-heading>
        <div>
            <p class="text-sm uppercase tracking-[0.3em] text-cyan-700">Dashboard</p>
            <h2 class="mt-2 text-2xl font-semibold sm:text-3xl">Ringkasan Aset BMD</h2>
        </div>
        <a href="{{ route('assets.create') }}" class="self-start rounded-2xl bg-slate-950 px-5 py-3 text-center text-sm font-semibold text-white">Tambah Aset</a>
    </div>

    <div class="mt-5 grid gap-3 sm:gap-4 md:grid-cols-2 xl:grid-cols-4" data-page-grid>
        <div class="rounded-3xl bg-white p-5 shadow-sm sm:p-6" data-stat-card>
            <p class="text-sm text-slate-500">Jumlah seluruh aset</p>
            <p class="stat-number mt-3 text-4xl font-semibold">{{ $summary['total'] }}</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm sm:p-6" data-stat-card>
            <p class="text-sm text-slate-500">Aset kondisi baik</p>
            <p class="stat-number mt-3 text-4xl font-semibold text-emerald-600">{{ $summary['baik'] }}</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm sm:p-6" data-stat-card>
            <p class="text-sm text-slate-500">Aset rusak</p>
            <p class="stat-number mt-3 text-4xl font-semibold text-rose-600">{{ $summary['rusak'] }}</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm sm:p-6" data-stat-card>
            <p class="text-sm text-slate-500">Jumlah lokasi aset</p>
            <p class="stat-number mt-3 text-4xl font-semibold text-cyan-700">{{ $summary['lokasi'] }}</p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:gap-6 xl:grid-cols-[1.1fr_0.9fr]" data-page-grid>
        <section class="rounded-3xl bg-white p-5 shadow-sm sm:p-6" data-page-card>
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Aset Terbaru</h3>
                <a href="{{ route('assets.index') }}" class="text-sm text-cyan-700">Lihat semua</a>
            </div>
            <div class="mt-4 space-y-3 sm:hidden">
                @forelse ($latestAssets as $asset)
                    <article class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs uppercase tracking-[0.22em] text-cyan-700">Kode Aset</p>
                                <p class="mt-2 break-words font-semibold text-slate-950">{{ $asset->asset_code }}</p>
                                <p class="mt-2 break-words text-sm text-slate-600">{{ $asset->name }}</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold capitalize text-slate-700">{{ $asset->condition }}</span>
                        </div>
                        <div class="mt-4 rounded-2xl bg-white px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Lokasi</p>
                            <p class="mt-2 break-words text-sm font-medium text-slate-800">{{ $asset->location }}</p>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">Belum ada aset.</div>
                @endforelse
            </div>
            <div class="mt-4 hidden overflow-x-auto sm:block">
                <table class="min-w-full table-fixed text-left text-sm">
                    <thead class="text-slate-500">
                        <tr>
                            <th class="w-[22%] pb-3 pr-4">Kode</th>
                            <th class="w-[34%] pb-3 pr-4">Nama</th>
                            <th class="w-[24%] pb-3 pr-4">Lokasi</th>
                            <th class="w-[20%] pb-3">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestAssets as $asset)
                            <tr>
                                <td class="py-3 pr-4 align-top font-medium break-words whitespace-normal">{{ $asset->asset_code }}</td>
                                <td class="py-3 pr-4 align-top break-words whitespace-normal">{{ $asset->name }}</td>
                                <td class="py-3 pr-4 align-top break-words whitespace-normal">{{ $asset->location }}</td>
                                <td class="py-3 align-top capitalize break-words whitespace-normal">{{ $asset->condition }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-500">Belum ada aset.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-5 shadow-sm sm:p-6" data-page-card>
            <h3 class="text-lg font-semibold">Riwayat Aktivitas</h3>
            <div class="mt-4 space-y-3 sm:space-y-4">
                @forelse ($activities as $activity)
                    <div class="rounded-2xl border border-slate-100 p-4">
                        <p class="text-sm font-medium">{{ $activity->description }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $activity->user?->name ?? 'Sistem' }} - {{ $activity->created_at->format('d M Y H:i') }}</p>
                        @if ($activity->subject_label)
                            <p class="mt-2 break-words text-xs uppercase tracking-[0.16em] text-cyan-700">{{ $activity->subject_label }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada aktivitas tercatat.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
