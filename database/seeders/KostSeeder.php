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
            ['name' => 'Water Heater', 'type' => 'room'],
            ['name' => 'Ruang Santai', 'type' => 'building'],
            ['name' => 'Laundry', 'type' => 'building'],
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
            ['name' => 'Pintu gerbang dikunci jam 23.00 WIB'],
        ];

        $ruleModels = [];
        foreach ($rules as $rule) {
            $ruleModels[] = Rule::create($rule);
        }

        // 3. Buat User Pemilik Kost & User Biasa
        $owner = User::firstOrCreate(
            ['email' => 'owner@kostbandung.id'],
            [
                'name' => 'Owner Kost',
                'password' => bcrypt('password'),
                'role' => 'owner',
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@kostbandung.id'],
            [
                'name' => 'Pencari Kost',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );

        // 4. Data Dummy Kost
        $dummyKosts = [
            // Coblong
            ['name' => 'Kost Eksklusif Dago Asri', 'gender' => 'campur', 'price' => 2500000, 'district' => 'Coblong', 'address' => 'Jl. Dago Asri No. 12', 'boost' => true],
            ['name' => 'Pondok Dipatiukur', 'gender' => 'putri', 'price' => 1800000, 'district' => 'Coblong', 'address' => 'Jl. Dipatiukur No. 80', 'boost' => false],
            ['name' => 'Wisma Putra Cisitu', 'gender' => 'putra', 'price' => 1200000, 'district' => 'Coblong', 'address' => 'Jl. Cisitu Indah No. 5', 'boost' => false],
            
            // Lengkong
            ['name' => 'Pondok Lengkong Asri', 'gender' => 'campur', 'price' => 2100000, 'district' => 'Lengkong', 'address' => 'Jl. Lengkong Besar No. 10', 'boost' => true],
            ['name' => 'Kost Putri Cikawao', 'gender' => 'putri', 'price' => 1500000, 'district' => 'Lengkong', 'address' => 'Jl. Cikawao No. 45', 'boost' => false],
            ['name' => 'Kost Putra Burangrang', 'gender' => 'putra', 'price' => 1350000, 'district' => 'Lengkong', 'address' => 'Jl. Burangrang No. 8', 'boost' => false],
            
            // Kiaracondong
            ['name' => 'Kost Campur Kiaracondong', 'gender' => 'campur', 'price' => 900000, 'district' => 'Kiaracondong', 'address' => 'Jl. Ibrahim Adjie No. 120', 'boost' => false],
            ['name' => 'Wisma Kircon Permai', 'gender' => 'putri', 'price' => 850000, 'district' => 'Kiaracondong', 'address' => 'Jl. Kiaracondong Barat No. 3', 'boost' => false],
            ['name' => 'Kost Putra Babakan Sari', 'gender' => 'putra', 'price' => 750000, 'district' => 'Kiaracondong', 'address' => 'Jl. Babakan Sari No. 44', 'boost' => true],
            
            // Sukasari
            ['name' => 'Kost Premium Setiabudi', 'gender' => 'campur', 'price' => 3500000, 'district' => 'Sukasari', 'address' => 'Jl. Dr. Setiabudi No. 250', 'boost' => true],
            ['name' => 'Griya Gegerkalong', 'gender' => 'putri', 'price' => 1900000, 'district' => 'Sukasari', 'address' => 'Jl. Gegerkalong Hilir No. 11', 'boost' => false],
            ['name' => 'Sarijadi Cozy House', 'gender' => 'putra', 'price' => 1400000, 'district' => 'Sukasari', 'address' => 'Jl. Sarijadi Raya No. 9', 'boost' => false],
            
            // Buahbatu
            ['name' => 'Wisma Buahbatu Executive', 'gender' => 'campur', 'price' => 2800000, 'district' => 'Buahbatu', 'address' => 'Jl. Buahbatu No. 101', 'boost' => true],
            ['name' => 'Kost Putri Margacinta', 'gender' => 'putri', 'price' => 1250000, 'district' => 'Buahbatu', 'address' => 'Jl. Margacinta No. 8', 'boost' => false],
            ['name' => 'Kost Putra Turangga', 'gender' => 'putra', 'price' => 1100000, 'district' => 'Buahbatu', 'address' => 'Jl. Turangga No. 22', 'boost' => false],
            
            // Sumur Bandung
            ['name' => 'Braga City Kost', 'gender' => 'campur', 'price' => 4500000, 'district' => 'Sumur Bandung', 'address' => 'Jl. Braga No. 99', 'boost' => true],
            ['name' => 'Kost Putri Veteran', 'gender' => 'putri', 'price' => 2200000, 'district' => 'Sumur Bandung', 'address' => 'Jl. Veteran No. 4', 'boost' => false],
            ['name' => 'Wisma Merdeka Boys', 'gender' => 'putra', 'price' => 1800000, 'district' => 'Sumur Bandung', 'address' => 'Jl. Merdeka No. 70', 'boost' => false],
            
            // Antapani
            ['name' => 'Kost Antapani Asri', 'gender' => 'campur', 'price' => 1450000, 'district' => 'Antapani', 'address' => 'Jl. Antapani Lama No. 5', 'boost' => false],
            ['name' => 'Griya Putri Sindanglaya', 'gender' => 'putri', 'price' => 1150000, 'district' => 'Antapani', 'address' => 'Jl. Sindanglaya No. 12', 'boost' => false],
            ['name' => 'Kost Putra Purwakarta', 'gender' => 'putra', 'price' => 850000, 'district' => 'Antapani', 'address' => 'Jl. Purwakarta No. 33', 'boost' => false],
            
            // Cibiru
            ['name' => 'Kost Cibiru Indah', 'gender' => 'campur', 'price' => 700000, 'district' => 'Cibiru', 'address' => 'Jl. A.H. Nasution No. 200', 'boost' => false],
            ['name' => 'Pondok UIN Putri', 'gender' => 'putri', 'price' => 650000, 'district' => 'Cibiru', 'address' => 'Jl. Desa Cipadung No. 1', 'boost' => false],
            ['name' => 'Kost Putra Manisi', 'gender' => 'putra', 'price' => 500000, 'district' => 'Cibiru', 'address' => 'Jl. Manisi No. 9', 'boost' => true],
            
            // Extra varied
            ['name' => 'The Suites Metro Kost', 'gender' => 'campur', 'price' => 3000000, 'district' => 'Buahbatu', 'address' => 'Jl. Soekarno Hatta No. 689', 'boost' => true],
            ['name' => 'Kost Cihampelas Center', 'gender' => 'putri', 'price' => 2600000, 'district' => 'Coblong', 'address' => 'Jl. Cihampelas No. 150', 'boost' => false],
        ];

        foreach ($dummyKosts as $index => $data) {
            $kost = Kost::create([
                'user_id' => $owner->id,
                'name' => $data['name'],
                'slug' => Str::slug($data['name'] . ' ' . $index),
                'description' => 'Hunian yang nyaman, modern, dan berlokasi strategis di ' . $data['district'] . ', Bandung. Cocok untuk mahasiswa dan pekerja kantoran.',
                'gender_type' => $data['gender'],
                'price_monthly' => $data['price'],
                'address' => $data['address'],
                'district' => $data['district'],
                'latitude' => -6.917464 + (rand(-100, 100) / 10000), 
                'longitude' => 107.619123 + (rand(-100, 100) / 10000),
                'is_available' => true,
                'boosted_at' => $data['boost'] ? now() : null,
            ]);

            // Create placeholder image using placehold.co
            KostImage::create([
                'kost_id' => $kost->id,
                'image_path' => 'https://placehold.co/800x600/eeeeee/31343c?text=' . urlencode($data['name']),
                'is_primary' => true,
            ]);

            // Randomly attach 2-5 facilities
            $randomFacilities = array_rand($facilityModels, rand(3, 6));
            $facilitiesToAttach = is_array($randomFacilities) ? $randomFacilities : [$randomFacilities];
            $attachIds = [];
            foreach ($facilitiesToAttach as $fId) {
                $attachIds[] = $facilityModels[$fId]->id;
            }
            $kost->facilities()->attach($attachIds);

            // Randomly attach 1-3 rules
            $randomRules = array_rand($ruleModels, rand(1, 3));
            $rulesToAttach = is_array($randomRules) ? $randomRules : [$randomRules];
            $attachRuleIds = [];
            foreach ($rulesToAttach as $rId) {
                $attachRuleIds[] = $ruleModels[$rId]->id;
            }
            $kost->rules()->attach($attachRuleIds);
        }
    }
}