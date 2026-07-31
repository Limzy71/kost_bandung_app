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
            ['name' => 'Kamar Mandi Dalam', 'type' => 'room'],
            ['name' => 'AC (Air Conditioner)', 'type' => 'room'],
            ['name' => 'Kasur', 'type' => 'room'],
            ['name' => 'Bantal & Guling', 'type' => 'room'],
            ['name' => 'Lemari', 'type' => 'room'],
            ['name' => 'Meja & Kursi Belajar/Kerja', 'type' => 'room'],
            ['name' => 'Kipas Angin', 'type' => 'room'],
            ['name' => 'Water Heater (Air Hangat)', 'type' => 'room'],
            ['name' => 'Kulkas Dalam Kamar', 'type' => 'room'],
            ['name' => 'Rak Pakaian', 'type' => 'room'],
            ['name' => 'Gorden', 'type' => 'room'],
            ['name' => 'Cermin', 'type' => 'room'],

            // Fasilitas Umum
            ['name' => 'Wi-Fi 100Mbps', 'type' => 'building'],
            ['name' => 'Kamar Mandi Luar', 'type' => 'building'],
            ['name' => 'Dapur Bersama', 'type' => 'building'],
            ['name' => 'Kulkas Bersama', 'type' => 'building'],
            ['name' => 'Mesin Cuci / Laundry', 'type' => 'building'],
            ['name' => 'Parkir Mobil & Motor', 'type' => 'building'],
            ['name' => 'CCTV 24 Jam & Keamanan', 'type' => 'building'],
            ['name' => 'Jam Bebas 24 Jam', 'type' => 'building'],
            ['name' => 'Termasuk Listrik (Gratis)', 'type' => 'building'],
            ['name' => 'Ruang Tamu Bersama', 'type' => 'building'],
            ['name' => 'Tempat Jemuran', 'type' => 'building'],
        ];

        // Remove legacy combined facility in favor of separate items
        \App\Models\Facility::where('name', 'Kasur Springbed & Lemari')->get()
            ->each(function ($facility) {
                $facility->kosts()->detach();
                $facility->delete();
            });

        foreach ($defaultFacilities as $facility) {
            \App\Models\Facility::updateOrCreate(
                ['name' => $facility['name']],
                ['type' => $facility['type']],
            );
        }

        // Ensure standard rules exist
        $defaultRules = [
            'Bebas Akses 24 Jam',
            'Ada Jam Malam',
            'Dilarang Membawa Hewan Peliharaan',
            'Dilarang Merokok di Dalam Area Kost',
            'Boleh Membawa Tamu (Dengan Batasan Jam)',
            'Wajib Lapor Saat Membawa Tamu',
            'Boleh Memasak di Dapur Bersama',
            'Khusus 1 Orang per Kamar',
            'Deposit Dikembalikan Saat Keluar',
            'Bebas Membawa Tamu',
        ];

        foreach ($defaultRules as $ruleName) {
            \App\Models\Rule::firstOrCreate(['name' => $ruleName]);
        }

        // Demo Photos Directory check
        $sourceDir = storage_path('app/demo-photos');
        $destDir = storage_path('app/public/kosts');
        
        if (!\Illuminate\Support\Facades\File::exists($destDir)) {
            \Illuminate\Support\Facades\File::makeDirectory($destDir, 0755, true);
        }

        // If demo photos don't exist, create some placeholder images just for testing gracefully
        if (!\Illuminate\Support\Facades\File::exists($sourceDir)) {
            \Illuminate\Support\Facades\File::makeDirectory($sourceDir, 0755, true);
            for ($i = 1; $i <= 10; $i++) {
                // We'll create small dummy images
                $img = imagecreatetruecolor(800, 600);
                $bg = imagecolorallocate($img, rand(100,255), rand(100,255), rand(100,255));
                imagefill($img, 0, 0, $bg);
                imagejpeg($img, $sourceDir . '/' . $i . '.jpg');
                imagedestroy($img);
            }
        }

        // Fetch demo images
        $demoPhotos = \Illuminate\Support\Facades\File::files($sourceDir);
        if (count($demoPhotos) < 10) {
            $this->command->warn('Kurang dari 10 foto di demo-photos. Beberapa kost mungkin berbagi foto.');
        }

        // Helper to copy and attach photos
        $attachPhotos = function (\App\Models\Kost $kost, int $count) use (&$demoPhotos, $destDir) {
            for ($i = 0; $i < $count; $i++) {
                if (empty($demoPhotos)) break;
                // Pick a random photo
                $photo = $demoPhotos[array_rand($demoPhotos)];
                $filename = 'demo_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.jpg';
                \Illuminate\Support\Facades\File::copy($photo->getPathname(), $destDir . '/' . $filename);
                
                \App\Models\KostImage::create([
                    'kost_id' => $kost->id,
                    'image_path' => 'kosts/' . $filename,
                    'is_primary' => $i === 0,
                ]);
            }
        };

        // Helper to sync facilities by name
        $attachFacilities = function (\App\Models\Kost $kost, array $names) {
            $ids = \App\Models\Facility::whereIn('name', $names)->pluck('id')->all();
            $kost->facilities()->sync($ids);
        };

        // Seed Kost 1
        $kost1 = \App\Models\Kost::firstOrCreate(
            ['slug' => \Illuminate\Support\Str::slug('Kost Putra Dago Asri Neo-Brutal')],
            [
                'user_id' => $owner->id,
                'name' => 'Kost Putra Dago Asri Neo-Brutal',
                'description' => 'Kost modern bergaya arsitektur brutalist dengan fasilitas premium di kawasan Dago Asri.',
                'gender_type' => 'putra',
                'price_monthly' => 1800000,
                'address' => 'Jl. Dago Asri No. 99, Coblong',
                'district' => 'Coblong',
                'latitude' => -6.8830,
                'longitude' => 107.6145,
                'is_available' => true,
                'status' => 'published',
                'total_rooms' => 15,
                'available_rooms' => 3,
                'boosted_at' => now(),
            ]
        );
        if ($kost1->wasRecentlyCreated) $attachPhotos($kost1, 5);
        $attachFacilities($kost1, [
            'Kamar Mandi Dalam', 'AC (Air Conditioner)', 'Kasur', 'Bantal & Guling', 'Lemari',
            'Meja & Kursi Belajar/Kerja', 'Kipas Angin', 'Water Heater (Air Hangat)', 'Kulkas Dalam Kamar',
            'Gorden', 'Cermin', 'Rak Pakaian',
            'Wi-Fi 100Mbps', 'Dapur Bersama', 'Kulkas Bersama', 'Mesin Cuci / Laundry',
            'Parkir Mobil & Motor', 'CCTV 24 Jam & Keamanan', 'Jam Bebas 24 Jam', 'Termasuk Listrik (Gratis)',
        ]);

        // Seed Kost 2
        $kost2 = \App\Models\Kost::firstOrCreate(
            ['slug' => \Illuminate\Support\Str::slug('Wisma Putri Dipatiukur Minimalis')],
            [
                'user_id' => $owner->id,
                'name' => 'Wisma Putri Dipatiukur Minimalis',
                'description' => 'Kost khusus putri dekat kampus UNPAD. Lingkungan aman, tenang, dan strategis.',
                'gender_type' => 'putri',
                'price_monthly' => 1250000,
                'address' => 'Jl. Dipatiukur Dalam No. 12',
                'district' => 'Coblong',
                'latitude' => -6.8910,
                'longitude' => 107.6160,
                'is_available' => true,
                'status' => 'published',
                'total_rooms' => 10,
                'available_rooms' => 2,
            ]
        );
        if ($kost2->wasRecentlyCreated) $attachPhotos($kost2, 3);
        $attachFacilities($kost2, [
            'Kamar Mandi Dalam', 'Kasur', 'Bantal & Guling', 'Lemari', 'Meja & Kursi Belajar/Kerja',
            'Kipas Angin', 'Rak Pakaian', 'Gorden',
            'Wi-Fi 100Mbps', 'Kamar Mandi Luar', 'Dapur Bersama', 'Mesin Cuci / Laundry',
            'CCTV 24 Jam & Keamanan', 'Jam Bebas 24 Jam', 'Tempat Jemuran',
        ]);

        // Seed Kost 3
        $kost3 = \App\Models\Kost::firstOrCreate(
            ['slug' => \Illuminate\Support\Str::slug('Kost Campur Tubagus Ismail Ekonomis')],
            [
                'user_id' => $owner->id,
                'name' => 'Kost Campur Tubagus Ismail Ekonomis',
                'description' => 'Hunian kost terjangkau dan strategis. Bebas macet, dekat ITB dan UNPAD.',
                'gender_type' => 'campur',
                'price_monthly' => 750000,
                'address' => 'Jl. Tubagus Ismail Sekeloa No. 4',
                'district' => 'Coblong',
                'latitude' => -6.8885,
                'longitude' => 107.6185,
                'is_available' => true,
                'status' => 'published',
                'total_rooms' => 8,
                'available_rooms' => 4,
            ]
        );
        if ($kost3->wasRecentlyCreated) $attachPhotos($kost3, 2);
        $attachFacilities($kost3, [
            'Kasur', 'Lemari', 'Kipas Angin', 'Meja & Kursi Belajar/Kerja',
            'Wi-Fi 100Mbps', 'Kamar Mandi Luar', 'Dapur Bersama', 'Kulkas Bersama',
            'Mesin Cuci / Laundry', 'Parkir Mobil & Motor', 'Jam Bebas 24 Jam', 'Tempat Jemuran',
        ]);

        // Seed Kost 4 (Pending)
        $kost4 = \App\Models\Kost::firstOrCreate(
            ['slug' => \Illuminate\Support\Str::slug('Kost Exclusive Ciumbuleuwit')],
            [
                'user_id' => $owner->id,
                'name' => 'Kost Exclusive Ciumbuleuwit',
                'description' => 'Kost sultan di kawasan elite Ciumbuleuwit. Sedang menunggu review moderator admin.',
                'gender_type' => 'campur',
                'price_monthly' => 2500000,
                'address' => 'Jl. Ciumbuleuwit Atas No. 88',
                'district' => 'Cidadap',
                'latitude' => -6.8450,
                'longitude' => 107.6050,
                'is_available' => true,
                'status' => 'pending',
                'total_rooms' => 20,
                'available_rooms' => 5,
            ]
        );
        if ($kost4->wasRecentlyCreated) $attachPhotos($kost4, 3);
        $attachFacilities($kost4, [
            'Kamar Mandi Dalam', 'AC (Air Conditioner)', 'Kasur', 'Bantal & Guling', 'Lemari',
            'Meja & Kursi Belajar/Kerja', 'Rak Pakaian', 'Cermin',
            'Wi-Fi 100Mbps', 'Parkir Mobil & Motor', 'CCTV 24 Jam & Keamanan',
            'Termasuk Listrik (Gratis)', 'Ruang Tamu Bersama',
        ]);

        $this->command->info('✅ Demo Kost Properties seeded successfully with realistic photos!');
    }
}
