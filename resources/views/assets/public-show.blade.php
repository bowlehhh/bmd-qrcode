<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $asset->asset_code }} - {{ $asset->name }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('branding/logo-kominfo-kubar.jpeg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(56,189,248,0.18),_transparent_24%),linear-gradient(180deg,_#020617,_#060b23)] px-4 py-5 text-white sm:px-6 sm:py-8">
    <div class="mx-auto max-w-6xl">
        <div class="rounded-[2.2rem] border border-cyan-300/12 bg-[radial-gradient(circle_at_top_right,_rgba(34,211,238,0.18),_transparent_24%),linear-gradient(135deg,_rgba(15,23,42,0.98),_rgba(5,10,27,0.98))] p-4 shadow-[0_30px_90px_rgba(2,6,23,0.55)] sm:p-8">
            <div class="rounded-[1.8rem] border border-cyan-300/10 bg-slate-950/35 p-5 sm:p-7">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <div class="flex items-center gap-4">
                            @include('partials.kominfo-logo', ['size' => 'h-16 w-16 sm:h-20 sm:w-20', 'alt' => 'Logo Kominfo', 'class' => 'rounded-full bg-white p-2 shadow-xl shadow-cyan-400/10'])
                            <div class="min-w-0">
                                <p class="text-xs uppercase tracking-[0.45em] text-sky-300 sm:text-sm">Hasil Scan QR Aset</p>
                                <h1 class="mt-3 break-words text-3xl font-semibold tracking-tight text-white sm:text-5xl">{{ $asset->name }}</h1>
                                <p class="mt-3 break-words text-lg text-slate-300 sm:text-xl">{{ $asset->asset_code }} - {{ $asset->location }}</p>
                            </div>
                        </div>
                        <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-cyan-300/15 bg-cyan-400/10 px-4 py-2.5 text-sm font-medium text-cyan-100">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-cyan-300 shadow-[0_0_12px_rgba(103,232,249,0.9)]"></span>
                            Informasi ini selalu mengikuti data aset terbaru
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                        <button type="button" onclick="window.history.back()" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-cyan-300/15 bg-white/5 px-5 py-3.5 text-sm font-semibold text-white transition hover:border-cyan-300/35 hover:bg-cyan-400/10">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
                            </svg>
                            Kembali
                        </button>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-cyan-400 px-5 py-3.5 text-sm font-semibold text-slate-950 transition hover:bg-sky-300">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5L12 4l9 7.5M5 10.5V20h14v-9.5" />
                            </svg>
                            Kembali ke Scan
                        </a>
                    </div>
                </div>

                <div class="mt-8 grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
                    <section class="rounded-[1.8rem] border border-cyan-300/10 bg-white/[0.04] p-5 shadow-inner shadow-cyan-950/15 sm:p-6">
                        <div class="mb-5 flex items-center justify-between gap-4 border-b border-cyan-300/10 pb-4">
                            <div>
                                <p class="text-lg font-semibold text-white sm:text-xl">Informasi Barang</p>
                                <p class="mt-1 text-sm leading-7 text-slate-400">Tampilan dibuat lebih lega agar jelas dibaca di HP maupun desktop.</p>
                            </div>
                            <div class="hidden h-14 w-14 items-center justify-center rounded-2xl bg-cyan-400/12 text-cyan-100 shadow-lg shadow-cyan-950/20 sm:flex">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 19h14M7 16V8h10v8M9 5h6" />
                                </svg>
                            </div>
                        </div>

                        <dl class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-white/6 bg-slate-900/55 px-4 py-4">
                                <dt class="text-xs font-medium uppercase tracking-[0.26em] text-sky-200/80">Kode Barang</dt>
                                <dd class="mt-2 break-words text-xl font-semibold text-white sm:text-2xl">{{ $asset->asset_code }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/6 bg-slate-900/55 px-4 py-4">
                                <dt class="text-xs font-medium uppercase tracking-[0.26em] text-sky-200/80">Nomor Register</dt>
                                <dd class="mt-2 break-words text-xl font-semibold text-white sm:text-2xl">{{ $asset->register_number ?: '-' }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/6 bg-slate-900/55 px-4 py-4">
                                <dt class="text-xs font-medium uppercase tracking-[0.26em] text-sky-200/80">Merk / Type</dt>
                                <dd class="mt-2 break-words text-xl font-semibold text-white sm:text-2xl">{{ $asset->brand ?: '-' }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/6 bg-slate-900/55 px-4 py-4">
                                <dt class="text-xs font-medium uppercase tracking-[0.26em] text-sky-200/80">Tahun Perolehan</dt>
                                <dd class="mt-2 break-words text-xl font-semibold text-white sm:text-2xl">{{ $asset->year_acquired ?: '-' }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/6 bg-slate-900/55 px-4 py-4">
                                <dt class="text-xs font-medium uppercase tracking-[0.26em] text-sky-200/80">Penanggung Jawab</dt>
                                <dd class="mt-2 break-words text-xl font-semibold text-white sm:text-2xl">{{ $asset->person_in_charge ?: '-' }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/6 bg-slate-900/55 px-4 py-4">
                                <dt class="text-xs font-medium uppercase tracking-[0.26em] text-sky-200/80">Kondisi</dt>
                                <dd class="mt-2 break-words text-xl font-semibold capitalize text-white sm:text-2xl">{{ $asset->condition }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/6 bg-slate-900/55 px-4 py-4 sm:col-span-2">
                                <dt class="text-xs font-medium uppercase tracking-[0.26em] text-sky-200/80">Lokasi Barang</dt>
                                <dd class="mt-2 break-words text-xl font-semibold text-white sm:text-2xl">{{ $asset->location }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/6 bg-slate-900/55 px-4 py-4 sm:col-span-2">
                                <dt class="text-xs font-medium uppercase tracking-[0.26em] text-sky-200/80">Keterangan</dt>
                                <dd class="mt-2 break-words text-base leading-8 text-slate-100 sm:text-lg">{{ $asset->description ?: '-' }}</dd>
                            </div>
                        </dl>
                    </section>

                    <section class="rounded-[1.8rem] border border-cyan-300/10 bg-white/[0.04] p-5 shadow-inner shadow-cyan-950/15 sm:p-6">
                        <div class="mb-5 flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-400/14 text-cyan-100 shadow-lg shadow-cyan-950/20">
                                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M7 4h10l3 3v10l-3 3H7l-3-3V7l3-3Z" />
                                    <circle cx="12" cy="12" r="3.5" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-white sm:text-xl">Visual Barang</p>
                                <p class="text-sm leading-7 text-slate-400">Foto aset bila ada, atau branding Kominfo yang tetap enak dilihat jika foto belum tersedia.</p>
                            </div>
                        </div>

                        @if ($asset->photo_path)
                            <img src="{{ url('storage/'.$asset->photo_path) }}" alt="{{ $asset->name }}" class="h-72 w-full rounded-[1.7rem] border border-cyan-300/12 object-cover shadow-xl shadow-cyan-950/20 sm:h-96">
                        @else
                            <div class="flex h-72 flex-col items-center justify-center rounded-[1.7rem] border border-cyan-300/12 bg-[radial-gradient(circle_at_top,_rgba(56,189,248,0.18),_transparent_34%),linear-gradient(180deg,_rgba(15,23,42,0.78),_rgba(15,23,42,0.96))] px-6 text-center sm:h-96">
                                @include('partials.kominfo-logo', ['size' => 'h-24 w-24 sm:h-28 sm:w-28', 'alt' => 'Logo Kominfo', 'class' => 'rounded-full bg-white p-2 shadow-xl shadow-cyan-400/10'])
                                <p class="mt-6 text-2xl font-semibold text-white">BMD QR Asset</p>
                                <p class="mt-3 max-w-sm text-sm leading-7 text-slate-300 sm:text-base">Foto barang belum tersedia. Informasi aset tetap tampil lengkap, rapi, dan mudah dibaca.</p>
                                <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-400/10 px-3 py-2 text-xs font-medium text-cyan-100">
                                    <span class="inline-flex h-2 w-2 rounded-full bg-cyan-300"></span>
                                    Dinas Kominfo Kutai Barat
                                </div>
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
