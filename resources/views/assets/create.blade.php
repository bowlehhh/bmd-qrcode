@extends('layouts.app')

@section('content')
    <div data-page-heading>
        <p class="text-sm uppercase tracking-[0.3em] text-cyan-700">Tambah Aset</p>
        <h2 class="mt-2 text-3xl font-semibold">Input Data Barang</h2>
        <p class="mt-2 text-slate-500">Setelah disimpan, sistem akan otomatis membuat QR Code berdasarkan kode aset.</p>
    </div>

    <form method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data" class="mt-5 rounded-3xl bg-white p-6 shadow-sm lg:mt-4 lg:p-5" data-loading-form data-page-card>
        @csrf
        @include('assets._form', ['submitLabel' => 'Simpan Aset'])
    </form>
@endsection
