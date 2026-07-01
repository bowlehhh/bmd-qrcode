<?php

namespace App\Http\Requests;

class UpdateAssetRequest extends AssetRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return $this->assetRules(forUpdate: true);
    }
}
