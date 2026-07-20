<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kost;
use App\Models\Facility;
use App\Models\Rule;
use App\Models\KostImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KostSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Fasilitas Standar
        $facilities = [
            ['name' => 'Wi-Fi 100Mbps', 'type' => 'room'],
            ['name' => 'Kamar Mandi Dalam', 'type' => 'room'],
            ['name' => 'AC', 'type' => 'room'],
            ['name' => 'Kasur Springbed & Lemari', 'type' => 'room'],
            ['name' => 'Dapur Bersama', 'type' => 'building'],
            ['name' => 'Parkir Mobil & Motor', 'type' => 'building'],
            ['name' => 'CCTV 24 Jam', 'type' => 'building'],
        ];

        $facilityModels = [];
        foreach ($facilities as $facility) {
            $facilityModels[] = Facility::create($facility);
        }

        // 2. Buat Data Aturan Kost
        $rules = [
            ['name' => 'Dilarang membawa hewan peliharaan'],
            ['name' => 'Jam bertamu maksimal pukul 22.00 WIB'],
            ['name' => 'Bukan kost bebas / lawan jenis dilarang masuk kamar'],
            ['name' => 'Dilarang merokok di dalam kamar'],
        ];

        $ruleModels = [];
        foreach ($rules as $rule) {
            $ruleModels[] = Rule::create($rule);
        }

        // 3. Buat User Pemilik Kost
        $owner = User::firstOrCreate(
            ['email' => 'owner@kostbandung.com'],
            [
                'name' => 'Ikhsan Pemilik Kost',
                'password' => bcrypt('password'),
            ]
        );

        // 4. Buat Sample Data Kost di Area Bandung (Coblong/Dago)
        $kost = Kost::create([
            'user_id' => $owner->id,
            'name' => 'Kost Exclusive Dago Asri',
            'slug' => Str::slug('Kost Exclusive Dago Asri'),
            'description' => 'Kost nyaman dekat kampus Unpas & ITB. Lingkungan tenang, aman dengan fasilitas lengkap.',
            'gender_type' => 'campur',
            'price_monthly' => 1750000.00,
            'address' => 'Jl. Dago Asri No. 12, Coblong, Bandung',
            'district' => 'Coblong',
            'latitude' => -6.88330000,
            'longitude' => 107.61500000,
            'is_available' => true,
            'boosted_at' => now(),
        ]);

        // 5. Hubungkan Fasilitas & Aturan ke Kost
        $kost->facilities()->attach([$facilityModels[0]->id, $facilityModels[1]->id, $facilityModels[2]->id, $facilityModels[6]->id]);
        $kost->rules()->attach([$ruleModels[1]->id, $ruleModels[2]->id]);

        // 6. Buat Sample Gambar Kost
        KostImage::create([
            'kost_id' => $kost->id,
            'image_path' => 'kosts/sample-room.jpg',
            'is_primary' => true,
        ]);
    }
}