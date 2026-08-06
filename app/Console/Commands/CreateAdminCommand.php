<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:create-admin {email : Alamat email untuk akun admin} {--name= : Nama lengkap admin}';

    /**
     * @var string
     */
    protected $description = 'Membuat akun administrator dengan peran "admin" dan email terverifikasi.';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));

        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $name = $this->option('name');

        if (! is_string($name) || trim($name) === '') {
            $name = $this->ask('Nama lengkap admin');
        }

        $password = $this->secret('Kata sandi untuk akun admin (minimal 8 karakter)');

        if (! is_string($password) || strlen($password) < 8) {
            $this->error('Kata sandi minimal 8 karakter.');

            return self::FAILURE;
        }

        $passwordConfirmation = $this->secret('Ulangi kata sandi');

        if ($password !== $passwordConfirmation) {
            $this->error('Konfirmasi kata sandi tidak cocok.');

            return self::FAILURE;
        }

        $user = User::create([
            'name' => trim((string) $name),
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->info("Akun administrator berhasil dibuat untuk {$user->email}.");

        return self::SUCCESS;
    }
}
