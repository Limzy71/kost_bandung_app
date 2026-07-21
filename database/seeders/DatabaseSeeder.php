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
        // 1. Akun Owner
        User::firstOrCreate(
            ['email' => 'owner@kostbandung.id'],
            [
                'name' => 'Owner Kost',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        // 2. Akun User Biasa
        User::firstOrCreate(
            ['email' => 'user@kostbandung.id'],
            [
                'name' => 'Pencari Kost',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $this->call([
            KostSeeder::class,
        ]);
    }
}