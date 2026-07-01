<?php

namespace App\Http\Requests;

class StoreAssetRequest extends AssetRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return $this->assetRules();
    }
}
