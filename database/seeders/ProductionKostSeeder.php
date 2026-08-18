<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductionKostSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::updateOrCreate(
            ['email' => 'owner@kostbandung.web.id'],
            [
                'name' => 'Owner Profesional',
                'password' => bcrypt('password123'),
                'role' => 'owner',
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'identity_verification_status' => 'verified',
                'identity_verified_at' => now(),
            ]
        );

        $kosts = [
            [
                'name' => 'The Dago Suites',
                'district' => 'Coblong',
                'address' => 'Jl. Dago Asri No. 12, Coblong, Bandung',
                'gender_type' => 'campur',
                'price_monthly' => 2500000,
                'rent_period' => 'monthly',
                'ownership_verification_status' => 'verified',
                'ownership_verified_at' => now(),
                'facilities' => ['AC', 'Kamar Mandi', 'WiFi', 'Kasur', 'Lemari', 'Parkir Motor', 'Keamanan 24 Jam'],
                'latitude' => -6.8797,
                'longitude' => 107.6148, // Near ITB
                'description' => 'Kost eksklusif dengan fasilitas lengkap, berlokasi strategis hanya 5 menit jalan kaki ke kampus ITB dan area komersial Dago. Cocok untuk mahasiswa dan profesional.',
            ],
            [
                'name' => 'Setiabudi Residence',
                'district' => 'Sukasari',
                'address' => 'Jl. Dr. Setiabudi No. 190, Sukasari, Bandung',
                'gender_type' => 'putri',
                'price_monthly' => 1800000,
                'rent_period' => 'monthly',
                'ownership_verification_status' => 'verified',
                'ownership_verified_at' => now(),
                'facilities' => ['Kamar Mandi', 'WiFi', 'Water Heater', 'Dapur', 'CCTV', 'Ruang Tamu'],
                'latitude' => -6.8601,
                'longitude' => 107.5941, // Near UPI
                'description' => 'Kost khusus putri yang nyaman dan aman di lingkungan asri Setiabudi. Dekat dengan kampus UPI dan Enhaii. Suasana tenang sangat mendukung untuk belajar.',
            ],
            [
                'name' => 'Cibiru Student Living',
                'district' => 'Cibiru',
                'address' => 'Jl. A.H. Nasution No. 105, Cibiru, Bandung',
                'gender_type' => 'putra',
                'price_monthly' => 850000,
                'rent_period' => 'monthly',
                'ownership_verification_status' => 'unverified',
                'ownership_verified_at' => null,
                'facilities' => ['WiFi', 'Parkir Motor', 'Dapur', 'Mesin Cuci'],
                'latitude' => -6.9312,
                'longitude' => 107.7183, // Near UIN SGD
                'description' => 'Kost khusus putra dengan harga terjangkau dan fasilitas memadai. Berjarak kurang dari 1 km ke UIN Sunan Gunung Djati. Dekat dengan berbagai fasilitas umum dan minimarket.',
            ],
            [
                'name' => 'Pasteur Harmony Living',
                'district' => 'Sukajadi',
                'address' => 'Jl. Surya Sumantri No. 45, Sukajadi, Bandung',
                'gender_type' => 'campur',
                'price_monthly' => 2000000,
                'rent_period' => 'monthly',
                'ownership_verification_status' => 'verified',
                'ownership_verified_at' => now(),
                'facilities' => ['AC', 'Kamar Mandi', 'WiFi', 'TV', 'Parkir Mobil'],
                'latitude' => -6.8835,
                'longitude' => 107.5815, // Near Maranatha
                'description' => 'Hunian premium bergaya modern minimalis yang berada tepat di jantung area Surya Sumantri. Hanya selangkah menuju Universitas Kristen Maranatha dan akses tol Pasteur.',
            ],
            [
                'name' => 'Tamansari Executive',
                'district' => 'Bandung Wetan',
                'address' => 'Jl. Tamansari No. 20, Bandung Wetan, Bandung',
                'gender_type' => 'putri',
                'price_monthly' => 1500000,
                'rent_period' => 'monthly',
                'ownership_verification_status' => 'verified',
                'ownership_verified_at' => now(),
                'facilities' => ['Kamar Mandi', 'WiFi', 'Akses Kunci Pintar', 'Area Kerja'],
                'latitude' => -6.9022,
                'longitude' => 107.6095, // Near UNISBA / UNPAS
                'description' => 'Kost eksekutif khusus putri di pusat kota Bandung. Akses sangat mudah ke berbagai kampus (UNISBA, UNPAS Tamansari) dan pusat perbelanjaan (BEC, BIP).',
            ],
        ];

        foreach ($kosts as $data) {
            $facilities = $data['facilities'];
            unset($data['facilities']); // Remove it from the insert array

            $data['user_id'] = $owner->id;
            $data['slug'] = Str::slug($data['name']).'-'.rand(1000, 9999);

            $kost = Kost::create($data);

            // Attach facilities
            foreach ($facilities as $facilityName) {
                // Find or create facility just in case it doesn't exist
                $facility = Facility::firstOrCreate(
                    ['name' => $facilityName],
                    ['type' => 'room', 'status' => 'approved', 'icon' => 'check']
                );
                $kost->facilities()->attach($facility->id);
            }
        }
    }
}
