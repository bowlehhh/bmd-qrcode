<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'BMD QR Asset' }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('branding/logo-kominfo-kubar.jpeg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="compact-app min-h-screen bg-slate-100 text-slate-900">
    <div id="mobile-sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-950/70 lg:hidden"></div>

    <div class="min-h-screen lg:grid lg:grid-cols-[240px_1fr]">
        <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-[290px] -translate-x-full flex-col bg-slate-950 px-6 py-8 text-white shadow-2xl transition-transform duration-300 lg:static lg:min-h-screen lg:w-auto lg:translate-x-0 lg:px-5 lg:py-6 lg:shadow-none">
            <div class="flex flex-1 flex-col">
                <div class="flex items-start justify-between gap-4">
                    <div class="mb-7">
                        <p class="text-xs uppercase tracking-[0.3em] text-cyan-300">DINAS KOMINFO</p>
                        <div class="mt-3 flex items-center gap-3">
                            @include('partials.kominfo-logo', ['size' => 'h-14 w-14 lg:h-12 lg:w-12', 'alt' => 'Logo Kominfo', 'class' => 'rounded-full bg-white p-1'])
                            <div>
                                <h1 class="text-2xl font-semibold leading-none lg:text-[1.9rem]">BMD QR Asset</h1>
                                <p class="mt-2 text-sm leading-6 text-slate-300">Manajemen aset dengan QR code yang mudah dipakai.</p>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="close-mobile-sidebar" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-800 bg-slate-900 text-slate-200 lg:hidden" aria-label="Tutup menu">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                </div>

                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}" class="block rounded-2xl px-4 py-3 lg:py-2.5 {{ request()->routeIs('dashboard') ? 'bg-cyan-400 text-slate-950' : 'bg-slate-900 text-slate-100 hover:bg-slate-800' }}">Dashboard</a>
                    <a href="{{ route('assets.index') }}" class="block rounded-2xl px-4 py-3 lg:py-2.5 {{ request()->routeIs('assets.*') ? 'bg-cyan-400 text-slate-950' : 'bg-slate-900 text-slate-100 hover:bg-slate-800' }}">Data Aset</a>
                    <a href="{{ route('exports.history') }}" class="block rounded-2xl px-4 py-3 lg:py-2.5 {{ request()->routeIs('exports.history') ? 'bg-cyan-400 text-slate-950' : 'bg-slate-900 text-slate-100 hover:bg-slate-800' }}">Riwayat Export</a>
                </nav>
            </div>

            <div class="mt-auto rounded-3xl border border-slate-800 bg-slate-900 p-4 lg:p-3.5">
                <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-sm uppercase text-slate-400">{{ auth()->user()->role }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-4" data-logout-form>
                    @csrf
                    <button type="submit" class="w-full rounded-xl border border-slate-700 px-4 py-2 text-sm hover:bg-slate-800">Logout</button>
                </form>
            </div>
        </aside>

        <main class="px-4 py-4 sm:px-8 sm:py-6 lg:px-7 lg:py-4">
            <div class="mb-4 flex items-center justify-between rounded-3xl bg-white px-4 py-3 shadow-sm lg:hidden">
                <div class="min-w-0 flex items-center gap-3">
                    @include('partials.kominfo-logo', ['size' => 'h-12 w-12', 'alt' => 'Logo Kominfo', 'class' => 'rounded-full bg-white p-1'])
                    <div class="min-w-0">
                    <p class="text-[10px] uppercase tracking-[0.24em] text-cyan-700">DINAS KOMINFO</p>
                    <p class="mt-1 text-base font-semibold text-slate-950">BMD QR Asset</p>
                    </div>
                </div>
                <button type="button" id="open-mobile-sidebar" class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-white" aria-label="Buka menu">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <div id="logout-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4">
        <div class="w-full max-w-md rounded-[2rem] bg-white p-6 text-slate-900 shadow-2xl">
            <p class="text-sm uppercase tracking-[0.3em] text-cyan-700">Konfirmasi</p>
            <h3 class="mt-3 text-2xl font-semibold">Keluar dari aplikasi?</h3>
            <p class="mt-3 text-sm leading-7 text-slate-600">Pastikan Anda benar-benar ingin logout dari sistem BMD QR Asset.</p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button type="button" id="cancel-logout" class="rounded-2xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700">Batal</button>
                <button type="button" id="confirm-logout" class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white">Ya, Logout</button>
            </div>
        </div>
    </div>

    <div id="loading-overlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4">
        <div class="w-full max-w-sm rounded-[2rem] bg-white p-6 text-center text-slate-900 shadow-2xl">
            <div class="mx-auto h-12 w-12 animate-spin rounded-full border-4 border-slate-200 border-t-cyan-500"></div>
            <h3 class="mt-5 text-xl font-semibold">Menyimpan Data</h3>
            <p class="mt-2 text-sm text-slate-600">Tunggu sebentar, data aset sedang diproses dan QR Code sedang dibuat.</p>
        </div>
    </div>
</body>
</html>
