<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'asset_code',
    'register_number',
    'name',
    'category',
    'brand',
    'year_acquired',
    'location',
    'person_in_charge',
    'is_in_use',
    'condition',
    'last_printed_at',
    'photo_path',
    'description',
    'qr_code_path',
    'qr_target_url',
    'created_by',
    'updated_by',
])]
class Asset extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_in_use' => 'boolean',
            'last_printed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'asset_code';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
