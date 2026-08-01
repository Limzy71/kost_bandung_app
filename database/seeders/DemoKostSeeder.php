<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoKostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Admin and Owner exist
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@kostbandung.id'],
            ['name' => 'Administrator', 'password' => \Illuminate\Support\Facades\Hash::make('password'), 'role' => 'admin']
        );

        $owner = \App\Models\User::firstOrCreate(
            ['email' => 'owner@kostbandung.id'],
            ['name' => 'Owner Kost', 'password' => \Illuminate\Support\Facades\Hash::make('password'), 'role' => 'owner']
        );

        // Ensure standard facilities exist (grouped by room/building)
        $defaultFacilities = [
            // Fasilitas Kamar
            ['name' => 'AC', 'type' => 'room'],
            ['name' => 'Kamar Mandi', 'type' => 'room'],
            ['name' => 'Kasur', 'type' => 'room'],
            ['name' => 'Bantal', 'type' => 'room'],
            ['name' => 'Lemari', 'type' => 'room'],
            ['name' => 'Meja', 'type' => 'room'],
            ['name' => 'Kursi', 'type' => 'room'],
            ['name' => 'Meja Rias', 'type' => 'room'],
            ['name' => 'Kipas Angin', 'type' => 'room'],
            ['name' => 'Water Heater', 'type' => 'room'],
            ['name' => 'Kulkas', 'type' => 'room'],
            ['name' => 'Cermin', 'type' => 'room'],
            ['name' => 'Jendela', 'type' => 'room'],
            ['name' => 'TV', 'type' => 'room'],

            // Fasilitas Umum
            ['name' => 'Wi-Fi', 'type' => 'building'],
            ['name' => 'Kamar Mandi', 'type' => 'building'],
            ['name' => 'Dapur', 'type' => 'building'],
            ['name' => 'Kulkas', 'type' => 'building'],
            ['name' => 'Tempat Jemuran', 'type' => 'building'],
            ['name' => 'CCTV', 'type' => 'building'],
            ['name' => 'Penjaga Kost', 'type' => 'building'],
            ['name' => 'Mesin Cuci', 'type' => 'building'],
            ['name' => 'Ruang Tamu', 'type' => 'building'],

            // Fasilitas Parkir
            ['name' => 'Parkir Mobil', 'type' => 'parking'],
            ['name' => 'Parkir Motor', 'type' => 'parking'],
            ['name' => 'Parkir Sepeda', 'type' => 'parking'],
        ];

        foreach ($defaultFacilities as $facility) {
            \App\Models\Facility::updateOrCreate(
                [
                    'name' => $facility['name'],
                    'type' => $facility['type'],
                ],
                [
                    'icon' => \App\Models\Facility::resolveIcon($facility['name']),
                    'status' => 'approved',
                ],
            );
        }

        // Ensure standard rules exist
        $defaultRules = [
            'Akses 24 Jam',
            'Ada Jam Malam',
            'Dilarang Merokok di Dalam Kamar',
            'Wajib Lapor Saat Membawa Tamu',
            'Boleh Pasutri',
            'Boleh Membawa Anak',
            'Lawan Jenis Di Larang Ke Kamar',
            'Denda Kerusakan Barang Kos',
            'Tamu Menginap Di Kenakan Biaya',
            'Dilarang Bawa Hewan',
            'Ada Jam Malam Untuk Tamu',
            'Maks. 2 Orang/ Kamar',
            'Maks. 1 Orang/ Kamar',
        ];

        foreach ($defaultRules as $ruleName) {
            \App\Models\Rule::firstOrCreate(['name' => $ruleName]);
        }
    }
}
