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

        // 4. Data Dummy Kost
        $dummyKosts = [
            ['name' => 'Kost Exclusive Dago Asri', 'gender' => 'campur', 'price' => 1750000, 'district' => 'Coblong', 'address' => 'Jl. Dago Asri No. 12, Coblong, Bandung', 'boost' => true],
            ['name' => 'Kost Putri Muslimah Cikutra', 'gender' => 'putri', 'price' => 850000, 'district' => 'Cibeunying Kidul', 'address' => 'Jl. Cikutra Barat No. 45', 'boost' => false],
            ['name' => 'Kost Putra Sukajadi Nyaman', 'gender' => 'putra', 'price' => 1200000, 'district' => 'Sukajadi', 'address' => 'Jl. Sukagalih No. 90, Sukajadi', 'boost' => false],
            ['name' => 'Griya Pasteur Executive', 'gender' => 'campur', 'price' => 2500000, 'district' => 'Cicendo', 'address' => 'Jl. Dr. Djunjunan No. 111', 'boost' => true],
            ['name' => 'Kost Lengkong Besar 10A', 'gender' => 'putra', 'price' => 1000000, 'district' => 'Lengkong', 'address' => 'Jl. Lengkong Besar 10A', 'boost' => false],
            ['name' => 'Wisma Buahbatu Asri', 'gender' => 'putri', 'price' => 1400000, 'district' => 'Buahbatu', 'address' => 'Jl. Margacinta No. 8', 'boost' => true],
            ['name' => 'Kost Setiabudi Permai', 'gender' => 'campur', 'price' => 1800000, 'district' => 'Sukasari', 'address' => 'Jl. Dr. Setiabudi No. 200', 'boost' => false],
            ['name' => 'Kost Putra Antapani', 'gender' => 'putra', 'price' => 750000, 'district' => 'Antapani', 'address' => 'Jl. Terusan Jakarta No. 12', 'boost' => false],
            ['name' => 'Paviliun Dipatiukur', 'gender' => 'campur', 'price' => 2200000, 'district' => 'Coblong', 'address' => 'Jl. Dipatiukur No. 80', 'boost' => true],
            ['name' => 'Kost Hijabers Kiaracondong', 'gender' => 'putri', 'price' => 900000, 'district' => 'Kiaracondong', 'address' => 'Jl. Ibrahim Adjie No. 3', 'boost' => false],
            ['name' => 'Kost Pria Sumur Bandung', 'gender' => 'putra', 'price' => 1100000, 'district' => 'Sumur Bandung', 'address' => 'Jl. Braga No. 9', 'boost' => false],
            ['name' => 'Kost Campur Cibiru Asri', 'gender' => 'campur', 'price' => 600000, 'district' => 'Cibiru', 'address' => 'Jl. A.H. Nasution No. 44', 'boost' => false],
            ['name' => 'Kost Exclusive Arcamanik', 'gender' => 'putri', 'price' => 1500000, 'district' => 'Arcamanik', 'address' => 'Jl. Pacuan Kuda No. 7', 'boost' => true],
            ['name' => 'Wisma Regol Indah', 'gender' => 'putra', 'price' => 950000, 'district' => 'Regol', 'address' => 'Jl. Pasirluyu No. 2', 'boost' => false],
            ['name' => 'Kost Melati Gedebage', 'gender' => 'campur', 'price' => 700000, 'district' => 'Gedebage', 'address' => 'Jl. Soekarno Hatta No. 800', 'boost' => false],
        ];

        foreach ($dummyKosts as $index => $data) {
            $kost = Kost::create([
                'user_id' => $owner->id,
                'name' => $data['name'],
                'slug' => Str::slug($data['name'] . ' ' . $index),
                'description' => 'Fasilitas lengkap, lingkungan aman dan nyaman di ' . $data['district'],
                'gender_type' => $data['gender'],
                'price_monthly' => $data['price'],
                'address' => $data['address'],
                'district' => $data['district'],
                'latitude' => -6.917464 + (rand(-100, 100) / 10000), // Randomize slightly around Bandung
                'longitude' => 107.619123 + (rand(-100, 100) / 10000),
                'is_available' => true,
                'boosted_at' => $data['boost'] ? now() : null,
            ]);

            // Randomly attach 2-5 facilities
            $randomFacilities = array_rand($facilityModels, rand(2, 5));
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