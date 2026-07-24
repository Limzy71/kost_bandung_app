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
        User::firstOrCreate(
            ['email' => 'admin@kostbandung.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Akun Owner
        User::firstOrCreate(
            ['email' => 'owner@kostbandung.id'],
            [
                'name' => 'Owner Kost',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        // 3. Akun User Biasa
        User::firstOrCreate(
            ['email' => 'user@kostbandung.id'],
            [
                'name' => 'Pencari Kost',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $this->call([
            DemoKostSeeder::class,
        ]);
    }
}