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
        <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">Keterangan</label>
        <textarea id="description" name="description" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3">{{ old('description', $asset->description ?? '') }}</textarea>
        @error('description')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="category" class="mb-1.5 block text-sm font-medium text-slate-700">Kategori Barang</label>
        <input id="category" name="category" type="text" value="{{ old('category', $asset->category ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required>
        <p class="mt-2 text-xs text-slate-500">Kolom ini tetap dipakai untuk kebutuhan pengelompokan data di aplikasi.</p>
        @error('category')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="is_in_use" class="mb-1.5 block text-sm font-medium text-slate-700">Masih Digunakan</label>
        <select id="is_in_use" name="is_in_use" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required>
            <option value="1" @selected((string) old('is_in_use', isset($asset) ? (int) $asset->is_in_use : 1) === '1')>Ya, masih digunakan</option>
            <option value="0" @selected((string) old('is_in_use', isset($asset) ? (int) $asset->is_in_use : 1) === '0')>Tidak digunakan</option>
        </select>
        @error('is_in_use')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="photo" class="mb-1.5 block text-sm font-medium text-slate-700">Foto Barang</label>
        <input id="photo" name="photo" type="file" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3">
        @error('photo')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
        @if ($asset?->photo_path)
            <p class="mt-2 text-xs text-slate-500">Foto lama akan diganti bila upload baru.</p>
        @endif
    </div>
</div>

<div class="mt-6 flex flex-wrap gap-3 lg:mt-5">
    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white lg:px-4.5 lg:py-2.5">{{ $submitLabel }}</button>
    <a href="{{ route('assets.index') }}" class="rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 lg:px-4.5 lg:py-2.5">Kembali</a>
</div>
