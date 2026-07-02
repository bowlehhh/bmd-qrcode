@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between" data-page-heading>
        <div>
            <p class="text-sm uppercase tracking-[0.3em] text-cyan-700">Riwayat Export</p>
            <h2 class="mt-2 text-2xl font-semibold sm:text-3xl">Log Export Word QR Aset</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">Halaman ini khusus menampilkan semua riwayat export Word agar lebih jelas dan mudah dicek.</p>
        </div>
        <a href="{{ route('assets.index') }}" class="self-start rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Kembali ke Data Aset</a>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" data-page-grid>
        <div class="rounded-3xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total export</p>
            <p class="mt-3 text-4xl font-semibold text-slate-950">{{ $summary['total_exports'] }}</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Export single</p>
            <p class="mt-3 text-4xl font-semibold text-cyan-700">{{ $summary['single_exports'] }}</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Export massal</p>
            <p class="mt-3 text-4xl font-semibold text-slate-950">{{ $summary['bulk_exports'] }}</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total aset diexport</p>
            <p class="mt-3 text-4xl font-semibold text-emerald-600">{{ $summary['assets_exported'] }}</p>
        </div>
    </div>

    <div class="mt-5 space-y-4">
        @forelse ($logs as $log)
            @php
                $properties = $log->properties ?? [];
                $exportAssets = $properties['assets'] ?? [];
            @endphp
            <article class="rounded-3xl bg-white p-5 shadow-sm sm:p-6" data-page-card>
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">
                                {{ ($properties['export_type'] ?? 'single') === 'bulk' ? 'Export Massal' : 'Export Single' }}
                            </span>
                            <span class="text-sm text-slate-500">{{ $log->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <h3 class="mt-3 text-lg font-semibold text-slate-950">{{ $log->description }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Oleh <span class="font-semibold text-slate-700">{{ $log->user?->name ?? 'Sistem' }}</span>
                            dari IP <span class="font-semibold text-slate-700">{{ $log->ip_address ?: '-' }}</span>
                        </p>
                    </div>

                    <div class="grid gap-3 rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-600 sm:min-w-[280px]">
                        <p><span class="font-semibold text-slate-900">File:</span> {{ $properties['filename'] ?? '-' }}</p>
                        <p><span class="font-semibold text-slate-900">Jumlah aset:</span> {{ $properties['total_assets'] ?? 1 }}</p>
                        <p><span class="font-semibold text-slate-900">Ringkasan:</span> {{ $properties['asset'] ?? ($log->subject_label ?: '-') }}</p>
                    </div>
                </div>

                @if (! empty($exportAssets))
                    <div class="mt-5 overflow-hidden rounded-3xl border border-slate-100">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-slate-50 text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3">Kode</th>
                                        <th class="px-4 py-3">Nama</th>
                                        <th class="px-4 py-3">Penanggung Jawab</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($exportAssets as $asset)
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $asset['asset_code'] ?? '-' }}</td>
                                            <td class="px-4 py-3 text-slate-700">{{ $asset['name'] ?? '-' }}</td>
                                            <td class="px-4 py-3 text-slate-700">{{ $asset['person_in_charge'] ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif ($log->subject_label)
                    <div class="mt-5 rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-600">
                        <span class="font-semibold text-slate-900">Aset:</span> {{ $log->subject_label }}
                    </div>
                @endif
            </article>
        @empty
            <div class="rounded-3xl bg-white px-6 py-10 text-center text-slate-500 shadow-sm">
                Belum ada riwayat export Word.
            </div>
        @endforelse
    </div>

    <div class="mt-5">
        {{ $logs->links() }}
    </div>
@endsection
