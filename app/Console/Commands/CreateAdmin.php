<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {email? : Email admin} {--name= : Nama admin}';

    protected $description = 'Membuat akun admin pertama tanpa kredensial bawaan';

    public function handle(): int
    {
        $name = trim((string) ($this->option('name') ?: $this->ask('Nama admin')));
        $email = trim((string) ($this->argument('email') ?: $this->ask('Email admin')));
        $password = (string) $this->secret('Password admin (minimal 12 karakter)');
        $confirmation = (string) $this->secret('Ulangi password admin');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        if (! hash_equals($password, $confirmation)) {
            $this->components->error('Konfirmasi password tidak sama. Akun admin tidak dibuat.');

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'role' => 'admin',
            'password' => $password,
        ]);

        $this->components->info('Akun admin berhasil dibuat. Simpan password di password manager.');

        return self::SUCCESS;
    }
}
