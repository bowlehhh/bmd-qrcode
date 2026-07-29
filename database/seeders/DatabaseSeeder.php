<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = trim((string) config('admin.seed.email'));
        $password = (string) config('admin.seed.password');

        if ($email === '' || $password === '') {
            $this->command?->warn('Admin seeder dilewati: ADMIN_SEED_EMAIL dan ADMIN_SEED_PASSWORD harus diisi di .env.');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => (string) config('admin.seed.name'),
                'role' => 'admin',
                'password' => $password,
            ],
        );
    }
}
