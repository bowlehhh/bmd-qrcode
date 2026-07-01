<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $assetCode = 'BMD-'.fake()->unique()->numerify('#####');
        $userId = User::query()->value('id') ?? User::factory();

        return [
            'asset_code' => $assetCode,
            'register_number' => fake()->optional()->numerify('REG-#####'),
            'name' => fake()->words(3, true),
            'category' => fake()->randomElement(['Elektronik', 'Furnitur', 'Kendaraan']),
            'brand' => fake()->company(),
            'year_acquired' => (int) fake()->year(),
            'location' => fake()->randomElement(['Ruang IT', 'Gudang', 'Lantai 2']),
            'person_in_charge' => fake()->name(),
            'is_in_use' => true,
            'condition' => fake()->randomElement(['baik', 'rusak', 'perlu perbaikan']),
            'photo_path' => null,
            'description' => fake()->sentence(),
            'qr_code_path' => 'assets/qrcodes/'.$assetCode.'.svg',
            'qr_target_url' => '/aset/'.$assetCode,
            'created_by' => $userId,
            'updated_by' => $userId,
        ];
    }
}
