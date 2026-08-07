@php($asset = $asset ?? null)

<div class="grid gap-4 md:grid-cols-2 lg:gap-3.5">
    <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Barang</label>
        <input id="name" name="name" type="text" value="{{ old('name', $asset->name ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required>
        @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    @if (! $asset)
        <div>
            <label for="asset_code" class="mb-1.5 block text-sm font-medium text-slate-700">Kode Barang</label>
            <input id="asset_code" name="asset_code" type="text" value="{{ old('asset_code', $asset->asset_code ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required>
            @error('asset_code')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>
    @elseif ($asset)
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Kode Barang</label>
            <div class="rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3">{{ $asset->asset_code }}</div>
            <p class="mt-2 text-xs text-slate-500">Kode barang dikunci agar QR Code tetap dan tidak berubah.</p>
        </div>
    @endif

    <div>
        <label for="register_number" class="mb-1.5 block text-sm font-medium text-slate-700">Nomor Register</label>
        <input id="register_number" name="register_number" type="text" value="{{ old('register_number', $asset->register_number ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3">
        @error('register_number')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="brand" class="mb-1.5 block text-sm font-medium text-slate-700">Merk / Type</label>
        <input id="brand" name="brand" type="text" value="{{ old('brand', $asset->brand ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3">
        @error('brand')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="year_acquired" class="mb-1.5 block text-sm font-medium text-slate-700">Tahun Perolehan</label>
        <input id="year_acquired" name="year_acquired" type="number" value="{{ old('year_acquired', $asset->year_acquired ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3">
        @error('year_acquired')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="condition" class="mb-1.5 block text-sm font-medium text-slate-700">Kondisi</label>
        <select id="condition" name="condition" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required>
            @foreach (['baik' => 'Baik', 'rusak' => 'Rusak', 'perlu perbaikan' => 'Perlu Perbaikan'] as $value => $label)
                <option value="{{ $value }}" @selected(old('condition', $asset->condition ?? 'baik') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('condition')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="person_in_charge" class="mb-1.5 block text-sm font-medium text-slate-700">Penanggung Jawab</label>
        <input id="person_in_charge" name="person_in_charge" type="text" value="{{ old('person_in_charge', $asset->person_in_charge ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3">
        @error('person_in_charge')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="location" class="mb-1.5 block text-sm font-medium text-slate-700">Lokasi Barang</label>
        <input id="location" name="location" type="text" value="{{ old('location', $asset->location ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required>
        @error('location')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="photo" class="mb-1.5 block text-sm font-medium text-slate-700">Foto Barang <span class="font-normal text-slate-400">(opsional)</span></label>
        <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" data-photo-input class="block w-full cursor-pointer rounded-2xl border border-slate-200 bg-white text-sm text-slate-600 file:mr-4 file:cursor-pointer file:border-0 file:bg-cyan-50 file:px-4 file:py-3 file:font-semibold file:text-cyan-800 hover:file:bg-cyan-100">
        <p class="mt-2 text-xs text-slate-500">Format JPG, PNG, atau WebP. Ukuran akhir maksimal 2 MB. Foto yang lebih besar akan dikompres otomatis sebelum disimpan.</p>
        <p data-photo-feedback class="mt-2 hidden text-sm" role="status" aria-live="polite"></p>
        @error('photo')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror

        @if ($asset?->photo_path)
            <div class="mt-3 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                <img src="{{ url('storage/'.$asset->photo_path) }}" alt="Foto {{ $asset->name }}" class="h-16 w-16 rounded-xl object-cover">
                <p class="text-sm text-slate-600">Foto saat ini. Pilih foto baru di atas untuk menggantinya.</p>
            </div>
        @endif
    </div>

    <div class="md:col-span-2">
        <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">Keterangan</label>
        <textarea id="description" name="description" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3">{{ old('description', $asset->description ?? '') }}</textarea>
        @error('description')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-6 flex flex-wrap gap-3 lg:mt-5">
    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white lg:px-4.5 lg:py-2.5">{{ $submitLabel }}</button>
    <a href="{{ route('assets.index') }}" class="rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 lg:px-4.5 lg:py-2.5">Kembali</a>
</div>
