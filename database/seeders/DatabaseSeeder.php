<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@kostbandung.web.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'identity_verification_status' => 'verified',
                'identity_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@kostbandung.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'identity_verification_status' => 'verified',
                'identity_verified_at' => now(),
            ]
        );

        // 2. Akun Owner
        User::updateOrCreate(
            ['email' => 'owner@kostbandung.web.id'],
            [
                'name' => 'Owner Profesional',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'identity_verification_status' => 'verified',
                'identity_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'owner@kostbandung.id'],
            [
                'name' => 'Owner Kost',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'identity_verification_status' => 'verified',
                'identity_verified_at' => now(),
            ]
        );

        // 3. Akun User Biasa
        User::updateOrCreate(
            ['email' => 'user@kostbandung.web.id'],
            [
                'name' => 'Pencari Kost',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@kostbandung.id'],
            [
                'name' => 'Pencari Kost',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
            ]
        );

        $this->call([
            DemoKostSeeder::class,
        ]);
    }
}
