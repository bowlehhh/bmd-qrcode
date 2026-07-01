@extends('layouts.app')

@section('content')
    <div data-page-heading>
        <p class="text-sm uppercase tracking-[0.3em] text-cyan-700">Ubah Aset</p>
        <h2 class="mt-2 text-3xl font-semibold">{{ $asset->name }}</h2>
        <p class="mt-2 text-slate-500">Admin dapat mengubah seluruh data barang tanpa mengubah kode aset dan barcode yang sudah dibuat.</p>
    </div>

    <form method="POST" action="{{ route('assets.update', $asset) }}" enctype="multipart/form-data" class="mt-5 rounded-3xl bg-white p-6 shadow-sm lg:mt-4 lg:p-5">
        @csrf
        @method('PUT')
        @include('assets._form', ['submitLabel' => 'Simpan Perubahan'])
    </form>
@endsection
