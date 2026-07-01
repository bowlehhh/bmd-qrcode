<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $asset->asset_code }} - {{ $asset->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 px-4 py-8 text-white">
    <div class="mx-auto max-w-5xl">
        <div class="rounded-[2rem] bg-[radial-gradient(circle_at_top_right,_rgba(34,211,238,0.22),_transparent_28%),linear-gradient(135deg,_#0f172a,_#020617)] p-8 shadow-2xl">
            <p class="text-sm uppercase tracking-[0.35em] text-cyan-300">Hasil Scan QR Aset</p>
            <h1 class="mt-3 text-3xl font-semibold">{{ $asset->name }}</h1>
            <p class="mt-2 text-slate-300">{{ $asset->asset_code }} - {{ $asset->location }}</p>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <dl class="grid gap-4 md:grid-cols-2">
                        <div><dt class="text-sm text-cyan-100/70">Kode Barang</dt><dd class="mt-1 font-semibold">{{ $asset->asset_code }}</dd></div>
                        <div><dt class="text-sm text-cyan-100/70">Nomor Register</dt><dd class="mt-1 font-semibold">{{ $asset->register_number ?: '-' }}</dd></div>
                        <div><dt class="text-sm text-cyan-100/70">Kategori</dt><dd class="mt-1 font-semibold">{{ $asset->category }}</dd></div>
                        <div><dt class="text-sm text-cyan-100/70">Merk / Type</dt><dd class="mt-1 font-semibold">{{ $asset->brand ?: '-' }}</dd></div>
                        <div><dt class="text-sm text-cyan-100/70">Tahun perolehan</dt><dd class="mt-1 font-semibold">{{ $asset->year_acquired ?: '-' }}</dd></div>
                        <div><dt class="text-sm text-cyan-100/70">Lokasi</dt><dd class="mt-1 font-semibold">{{ $asset->location }}</dd></div>
                        <div><dt class="text-sm text-cyan-100/70">Penanggung jawab</dt><dd class="mt-1 font-semibold">{{ $asset->person_in_charge ?: '-' }}</dd></div>
                        <div><dt class="text-sm text-cyan-100/70">Masih digunakan</dt><dd class="mt-1 font-semibold">{{ $asset->is_in_use ? 'Ya' : 'Tidak' }}</dd></div>
                        <div><dt class="text-sm text-cyan-100/70">Kondisi</dt><dd class="mt-1 font-semibold capitalize">{{ $asset->condition }}</dd></div>
                        <div class="md:col-span-2"><dt class="text-sm text-cyan-100/70">Keterangan</dt><dd class="mt-1 leading-7">{{ $asset->description ?: '-' }}</dd></div>
                    </dl>
                </section>

                <section class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    @if ($asset->photo_path)
                        <img src="{{ url('storage/'.$asset->photo_path) }}" alt="{{ $asset->name }}" class="h-80 w-full rounded-3xl object-cover">
                    @else
                        <div class="flex h-80 items-center justify-center rounded-3xl bg-white/5 text-slate-400">Foto barang belum tersedia.</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</body>
</html>
