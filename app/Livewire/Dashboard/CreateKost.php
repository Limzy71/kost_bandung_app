<?php

namespace App\Livewire\Dashboard;

use App\Models\Facility;
use App\Models\Kost;
use App\Models\KostImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateKost extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $gender_type = 'campur';
    public string $description = '';
    public string $district = '';
    public string $address = '';
    public string $price_monthly = '';
    public string $latitude = '';
    public string $longitude = '';
    public string $total_rooms = '1';
    public string $available_rooms = '1';
    public array $selectedFacilities = [];
    public array $photos = [];
    public ?string $district_auto_message = null;

    public function updatedDistrict($value)
    {
        $districtsConfig = config('bandung.districts', []);
        if (isset($districtsConfig[$value]['center'])) {
            $this->latitude = (string) $districtsConfig[$value]['center']['lat'];
            $this->longitude = (string) $districtsConfig[$value]['center']['lng'];
        }
    }

    public function updatedAddress($value)
    {
        if (!empty(trim($value))) {
            $this->dispatch('geocode-address', address: trim($value));
        }
    }

    public function updatedPriceMonthly($value)
    {
        if (is_numeric($value) && $value < 0) {
            $this->price_monthly = '0';
        }
    }

    public function updatedTotalRooms($value)
    {
        if (is_numeric($value) && $value < 0) {
            $this->total_rooms = '0';
        }
    }

    public function updatedAvailableRooms($value)
    {
        if (is_numeric($value) && $value < 0) {
            $this->available_rooms = '0';
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'gender_type' => 'required|in:putra,putri,campur',
            'description' => 'required|string|min:10|max:500',
            'district' => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys(config('bandung.districts', [])))],
            'address' => 'required|string|max:500',
            'price_monthly' => 'required|numeric|min:100000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'total_rooms' => 'required|integer|min:1',
            'available_rooms' => 'required|integer|min:0',
            'selectedFacilities' => 'nullable|array',
            'selectedFacilities.*' => 'exists:facilities,id',
            'photos' => 'required|array|min:4|max:10',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama kost wajib diisi.',
            'gender_type.required' => 'Tipe kost wajib dipilih.',
            'description.required' => 'Deskripsi kost wajib diisi.',
            'description.min' => 'Deskripsi kost minimal 10 karakter.',
            'description.max' => 'Deskripsi kost maksimal 500 karakter.',
            'district.required' => 'Kecamatan wajib dipilih.',
            'district.in' => 'Kecamatan tidak valid.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'price_monthly.required' => 'Harga per bulan wajib diisi.',
            'price_monthly.numeric' => 'Harga per bulan harus berupa angka.',
            'price_monthly.min' => 'Harga per bulan minimal Rp 100.000.',
            'latitude.required' => 'Titik lokasi peta wajib ditentukan.',
            'longitude.required' => 'Titik lokasi peta wajib ditentukan.',
            'total_rooms.required' => 'Total jumlah kamar wajib diisi.',
            'total_rooms.integer' => 'Total kamar harus berupa angka bulat.',
            'total_rooms.min' => 'Total kamar minimal 1.',
            'available_rooms.required' => 'Sisa kamar tersedia wajib diisi.',
            'available_rooms.integer' => 'Sisa kamar harus berupa angka bulat.',
            'photos.required' => 'MINIMAL 4 FOTO KOST WAJIB DIUNGGAH.',
            'photos.min' => 'MINIMAL 4 FOTO KOST WAJIB DIUNGGAH.',
            'photos.max' => 'MAKSIMAL 10 FOTO KOST DAPAT DIUNGGAH.',
            'photos.*.image' => 'File harus berupa gambar (JPG, PNG, WEBP).',
            'photos.*.mimes' => 'File harus berupa gambar dengan format JPG, PNG, atau WEBP.',
            'photos.*.max' => 'Ukuran setiap foto tidak boleh melebihi 2MB.',
        ];
    }

    public function removePhoto($index)
    {
        if (isset($this->photos[$index])) {
            unset($this->photos[$index]);
            // Re-index array so it stays contiguous
            $this->photos = array_values($this->photos);
        }
    }

    public function save()
    {
        $this->validate();

        $key = 'create_kost_' . request()->ip() . '_' . Auth::id();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            $this->addError('name', 'TERLALU BANYAK MENAMBAH PROPERTI. TUNGGU ' . ceil($seconds/60) . ' MENIT.');
            return;
        }

        \Illuminate\Support\Facades\RateLimiter::hit($key, 3600);

        $lat = (float) $this->latitude;
        $lng = (float) $this->longitude;
        
        $districts = config('bandung.districts', []);
        $bounds = $districts[$this->district]['bounds'] ?? null;
        if ($bounds) {
            if (
                $lat < $bounds['lat_min'] || $lat > $bounds['lat_max'] ||
                $lng < $bounds['lng_min'] || $lng > $bounds['lng_max']
            ) {
                $this->addError('latitude', 'Koordinat peta tidak berada di dalam wilayah Kecamatan yang dipilih.');
                return;
            }
        }

        $slug = Str::slug($this->name);
        $originalSlug = $slug;
        $count = 1;
        while (Kost::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Create Kost record with dynamically selected coordinates
        $kost = Kost::create([
            'user_id' => $user->id,
            'name' => $this->name,
            'slug' => $slug,
            'description' => $this->description,
            'gender_type' => $this->gender_type,
            'price_monthly' => $this->price_monthly,
            'address' => $this->address,
            'district' => $this->district,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_available' => ((int)$this->available_rooms > 0),
            'status' => 'pending', // Draft / Pending Admin review
            'total_rooms' => (int)$this->total_rooms,
            'available_rooms' => (int)$this->available_rooms,
        ]);

        // Store photos in public storage and create KostImage records
        foreach ($this->photos as $index => $photo) {
            $path = $photo->store('kosts', 'public');
            
            KostImage::create([
                'kost_id' => $kost->id,
                'image_path' => $path,
                'is_primary' => $index === 0,
            ]);
        }
        $this->photos = [];

        // Attach facilities if selected
        if (! empty($this->selectedFacilities)) {
            $kost->facilities()->attach($this->selectedFacilities);
        }

        session()->flash('status', 'Properti kost "' . $kost->name . '" berhasil diajukan dan sedang dalam peninjauan Admin!');

        return redirect()->route('dashboard');
    }

    public function render()
    {
        $facilities = Facility::orderBy('name')->get();

        if ($facilities->isEmpty()) {
            $defaultFacilities = [
                ['name' => 'Wi-Fi 100Mbps', 'type' => 'room'],
                ['name' => 'Kamar Mandi Dalam', 'type' => 'room'],
                ['name' => 'AC (Air Conditioner)', 'type' => 'room'],
                ['name' => 'Water Heater (Air Hangat)', 'type' => 'room'],
                ['name' => 'Kasur Springbed & Lemari', 'type' => 'room'],
                ['name' => 'Meja & Kursi Belajar/Kerja', 'type' => 'room'],
                ['name' => 'Kulkas Dalam Kamar', 'type' => 'room'],
                ['name' => 'Dapur Bersama', 'type' => 'building'],
                ['name' => 'Kulkas Bersama', 'type' => 'building'],
                ['name' => 'Mesin Cuci / Laundry', 'type' => 'building'],
                ['name' => 'Parkir Mobil & Motor', 'type' => 'building'],
                ['name' => 'CCTV 24 Jam & Keamanan', 'type' => 'building'],
                ['name' => 'Jam Bebas 24 Jam', 'type' => 'building'],
                ['name' => 'Termasuk Listrik (Gratis)', 'type' => 'room'],
            ];

            foreach ($defaultFacilities as $facility) {
                Facility::firstOrCreate(['name' => $facility['name']], $facility);
            }

            $facilities = Facility::orderBy('name')->get();
        }

        $districts = array_keys(config('bandung.districts', []));

        return view('livewire.dashboard.create-kost', [
            'facilities' => $facilities,
            'districts' => $districts,
            'googleMapsApiKey' => config('services.google.maps_api_key') ?: env('GOOGLE_MAPS_API_KEY'),
        ])->layout('layouts.app', [
            'title' => 'Tambah Kost Baru — KostBandung.id',
        ]);
    }
}
