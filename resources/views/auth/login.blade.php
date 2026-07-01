<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login BMD QR Asset</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('branding/logo-kominfo-kubar.jpeg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <div class="flex min-h-screen items-center justify-center px-3 py-4 sm:px-4 sm:py-10">
        <div class="grid w-full max-w-6xl overflow-hidden rounded-[1.8rem] bg-slate-900 shadow-2xl lg:grid-cols-[1.05fr_0.95fr]">
            <section class="bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.35),_transparent_35%),linear-gradient(135deg,_#082f49,_#020617)] p-6 sm:p-10">
                <p class="text-sm uppercase tracking-[0.4em] text-cyan-200">DINAS KOMINFO</p>
                <div class="mt-4 flex items-center gap-4">
                    @include('partials.kominfo-logo', ['size' => 'h-16 w-16 sm:h-20 sm:w-20', 'alt' => 'Logo Kominfo', 'class' => 'rounded-full bg-white p-1.5'])
                    <div class="h-px flex-1 bg-white/15"></div>
                </div>
                <h1 class="mt-4 max-w-md text-3xl font-semibold leading-tight sm:mt-6 sm:text-4xl">Aplikasi Manajemen Aset BMD berbasis QR Code.</h1>
                <p class="mt-4 max-w-lg text-slate-300">Input barang, buat QR otomatis, tempel di aset, scan, lalu tampilkan detail barang secara cepat dan rapi.</p>
                <div class="mt-8 grid gap-3 sm:mt-10 sm:grid-cols-3 sm:gap-4">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-2xl font-semibold">1</p>
                        <p class="mt-2 text-sm text-slate-300">Tambah data aset</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-2xl font-semibold">2</p>
                        <p class="mt-2 text-sm text-slate-300">Generate & cetak QR</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-2xl font-semibold">3</p>
                        <p class="mt-2 text-sm text-slate-300">Scan untuk lihat detail</p>
                    </div>
                </div>
            </section>

            <section class="space-y-4 bg-slate-900 p-4 sm:space-y-6 sm:p-10">
                <div class="rounded-3xl border border-slate-800 bg-slate-950/70 p-6">
                    <h2 class="text-2xl font-semibold">Login Admin</h2>
                    <p class="mt-2 text-sm text-slate-400">Masuk untuk menambahkan barang, membuat QR, dan mencetak barcode.</p>

                    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                        @csrf
                        <div>
                            <label for="email" class="mb-2 block text-sm text-slate-300">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 outline-none ring-0 focus:border-cyan-400" required>
                            @error('email')
                                <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="mb-2 block text-sm text-slate-300">Password</label>
                            <input id="password" name="password" type="password" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 outline-none ring-0 focus:border-cyan-400" required>
                        </div>
                        <button type="submit" class="w-full rounded-2xl bg-cyan-400 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-300">Masuk ke Dashboard</button>
                    </form>

                    <div class="mt-6 rounded-2xl border border-slate-800 bg-slate-900 p-4 text-sm text-slate-300">
                        <p class="font-semibold text-white">Akun demo</p>
                        <p class="mt-2 break-all">Admin: `admin@bmd.test` / `password`</p>
                    </div>
                </div>

                <div class="rounded-3xl border border-cyan-400/20 bg-slate-950/70 p-6">
                    <div class="flex flex-col gap-4">
                        <div class="min-w-0">
                            <h3 class="text-xl font-semibold">Scan Barcode / QR</h3>
                            <p class="mt-2 text-sm leading-7 text-slate-400">Arahkan kamera ke barcode yang dibuat admin. Detail barang akan langsung muncul sebagai pop up.</p>
                        </div>
                        <button type="button" id="start-scanner" class="w-full touch-manipulation rounded-2xl bg-cyan-400 px-4 py-4 text-base font-semibold text-slate-950 active:scale-[0.99]">Mulai Scan Sekarang</button>
                    </div>

                    <div id="asset-scanner" data-asset-scanner class="mt-5 hidden">
                        <div id="reader" class="overflow-hidden rounded-3xl border border-slate-800 bg-black"></div>
                        <p id="scanner-status" class="mt-3 text-sm leading-7 text-slate-400">Scanner belum dijalankan.</p>
                        <p id="scanner-help" class="mt-2 text-xs leading-6 text-slate-500">Jika kamera HP tidak mau terbuka, biasanya browser memblokir akses kamera pada alamat HTTP biasa.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div id="asset-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 px-4">
        <div class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-[2rem] border border-cyan-400/20 bg-slate-900 p-5 text-white shadow-2xl sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-cyan-300">Detail Hasil Scan</p>
                    <h3 id="modal-name" class="mt-2 text-2xl font-semibold sm:text-3xl"></h3>
                    <p id="modal-code" class="mt-2 text-slate-300"></p>
                </div>
                <button type="button" id="close-asset-modal" class="rounded-2xl border border-slate-700 px-4 py-2 text-sm">Tutup</button>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-3xl bg-white/5 p-5">
                    <dl class="grid gap-4 md:grid-cols-2">
                        <div><dt class="text-sm text-slate-400">Kode Barang</dt><dd id="modal-asset-code" class="mt-1 font-semibold"></dd></div>
                        <div><dt class="text-sm text-slate-400">Nomor Register</dt><dd id="modal-register-number" class="mt-1 font-semibold"></dd></div>
                        <div><dt class="text-sm text-slate-400">Kategori</dt><dd id="modal-category" class="mt-1 font-semibold"></dd></div>
                        <div><dt class="text-sm text-slate-400">Merk / Type</dt><dd id="modal-brand" class="mt-1 font-semibold"></dd></div>
                        <div><dt class="text-sm text-slate-400">Tahun Perolehan</dt><dd id="modal-year" class="mt-1 font-semibold"></dd></div>
                        <div><dt class="text-sm text-slate-400">Lokasi</dt><dd id="modal-location" class="mt-1 font-semibold"></dd></div>
                        <div><dt class="text-sm text-slate-400">Penanggung Jawab</dt><dd id="modal-person-in-charge" class="mt-1 font-semibold"></dd></div>
                        <div><dt class="text-sm text-slate-400">Masih Digunakan</dt><dd id="modal-is-in-use" class="mt-1 font-semibold"></dd></div>
                        <div><dt class="text-sm text-slate-400">Kondisi</dt><dd id="modal-condition" class="mt-1 font-semibold"></dd></div>
                        <div class="md:col-span-2"><dt class="text-sm text-slate-400">Keterangan</dt><dd id="modal-description" class="mt-1 leading-7"></dd></div>
                    </dl>
                    <a id="modal-detail-link" href="#" target="_blank" class="mt-5 inline-flex rounded-2xl bg-cyan-400 px-4 py-3 text-sm font-semibold text-slate-950">Buka Halaman Detail</a>
                </div>

                <div class="rounded-3xl bg-white/5 p-5">
                    <div id="modal-photo-empty" class="flex h-56 items-center justify-center rounded-3xl border border-dashed border-slate-700 text-center text-slate-400 sm:h-72">Foto barang belum tersedia.</div>
                    <img id="modal-photo" src="" alt="" class="hidden h-56 w-full rounded-3xl object-cover sm:h-72">
                </div>
            </div>
        </div>
    </div>
</body>
</html>
