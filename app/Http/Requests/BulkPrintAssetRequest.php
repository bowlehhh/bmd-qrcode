<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkPrintAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'asset_ids' => ['required', 'array', 'min:1', 'max:200'],
            'asset_ids.*' => ['required', 'integer', 'distinct', 'exists:assets,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'asset_ids.required' => 'Pilih minimal satu aset untuk diexport ke Word.',
            'asset_ids.min' => 'Pilih minimal satu aset untuk diexport ke Word.',
            'asset_ids.max' => 'Export Word massal dibatasi maksimal 200 aset per proses agar server tetap stabil.',
            'asset_ids.*.exists' => 'Ada aset yang dipilih tetapi tidak ditemukan.',
            'asset_ids.*.distinct' => 'Daftar aset yang dipilih tidak boleh duplikat.',
        ];
    }
}
