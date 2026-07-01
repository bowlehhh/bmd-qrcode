<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class AssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function assetRules(bool $forUpdate = false): array
    {
        return [
            'asset_code' => [$forUpdate ? 'sometimes' : 'required', 'string', 'max:50', 'unique:assets,asset_code'],
            'register_number' => ['nullable', 'string', 'max:255'],
            'name' => [$forUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'category' => [$forUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'year_acquired' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:2100'],
            'location' => [$forUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'person_in_charge' => ['nullable', 'string', 'max:255'],
            'is_in_use' => ['required', 'boolean'],
            'condition' => ['required', 'in:baik,rusak,perlu perbaikan'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute tidak boleh lebih dari :max karakter.',
            'digits' => ':attribute harus terdiri dari :digits digit.',
            'integer' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min.',
            'boolean' => ':attribute harus dipilih dengan benar.',
            'in' => ':attribute yang dipilih tidak valid.',
            'image' => 'Foto barang harus berupa gambar.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'asset_code' => 'Nomor / Kode Aset',
            'register_number' => 'Nomor Register',
            'name' => 'Nama Barang',
            'category' => 'Kategori Barang',
            'brand' => 'Merk / Type',
            'year_acquired' => 'Tahun Perolehan',
            'location' => 'Lokasi Barang',
            'person_in_charge' => 'Penanggung Jawab',
            'is_in_use' => 'Status Masih Digunakan',
            'condition' => 'Kondisi Barang',
            'photo' => 'Foto Barang',
            'description' => 'Keterangan',
        ];
    }
}
