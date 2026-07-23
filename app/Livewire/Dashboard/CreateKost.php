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
    public string $district = 'Coblong';
    public string $address = '';
    public string $price_monthly = '';
    public string $latitude = '-6.917464';
    public string $longitude = '107.619123';
    public string $total_rooms = '1';
    public string $available_rooms = '1';
    public array $selectedFacilities = [];
    public array $photos = [];

    protected array $rules = [
        'name' => 'required|string|max:255',
        'gender_type' => 'required|in:putra,putri,campur',
        'description' => 'required|string|min:10',
        'district' => 'required|string|max:100',
        'address' => 'required|string|max:500',
        'price_monthly' => 'required|numeric|min:100000',
        'latitude' => 'required|numeric|min:-6.9800|max:-6.8300',
        'longitude' => 'required|numeric|min:107.5400|max:107.7500',
        'total_rooms' => 'required|integer|min:1',
        'available_rooms' => 'required|integer|min:0',
        'selectedFacilities' => 'nullable|array',
        'selectedFacilities.*' => 'exists:facilities,id',
        'photos' => 'required|array|min:1|max:8',
        'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
    ];

    protected array $messages = [
        'name.required' => 'Nama kost wajib diisi.',
        'gender_type.required' => 'Tipe kost wajib dipilih.',
        'description.required' => 'Deskripsi kost wajib diisi.',
        'description.min' => 'Deskripsi kost minimal 10 karakter.',
        'district.required' => 'Kecamatan wajib dipilih.',
        'address.required' => 'Alamat lengkap wajib diisi.',
        'price_monthly.required' => 'Harga per bulan wajib diisi.',
        'price_monthly.numeric' => 'Harga per bulan harus berupa angka.',
        'price_monthly.min' => 'Harga per bulan minimal Rp 100.000.',
        'latitude.required' => 'Titik lokasi peta wajib ditentukan.',
        'latitude.min' => 'Lokasi kost harus berada di dalam area administratif Kota Bandung.',
        'latitude.max' => 'Lokasi kost harus berada di dalam area administratif Kota Bandung.',
        'longitude.required' => 'Titik lokasi peta wajib ditentukan.',
        'longitude.min' => 'Lokasi kost harus berada di dalam area administratif Kota Bandung.',
        'longitude.max' => 'Lokasi kost harus berada di dalam area administratif Kota Bandung.',
        'total_rooms.required' => 'Total jumlah kamar wajib diisi.',
        'total_rooms.integer' => 'Total kamar harus berupa angka bulat.',
        'total_rooms.min' => 'Total kamar minimal 1.',
        'available_rooms.required' => 'Sisa kamar tersedia wajib diisi.',
        'available_rooms.integer' => 'Sisa kamar harus berupa angka bulat.',
        'photos.required' => 'Minimal 1 foto kost wajib diunggah.',
        'photos.min' => 'Minimal 1 foto kost wajib diunggah.',
        'photos.max' => 'Maksimal 8 foto kost dapat diunggah.',
        'photos.*.image' => 'File harus berupa gambar (JPG, PNG, WEBP).',
        'photos.*.mimes' => 'File harus berupa gambar dengan format JPG, PNG, atau WEBP.',
        'photos.*.max' => 'Ukuran setiap foto tidak boleh melebihi 2MB.',
    ];

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

        $lat = (float) $this->latitude;
        $lng = (float) $this->longitude;
        if ($lat < -6.9800 || $lat > -6.8300 || $lng < 107.5400 || $lng > 107.7500) {
            $this->addError('latitude', 'Lokasi kost harus berada di dalam area administratif Kota Bandung.');
            return;
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

        $districts = [
            'Coblong',
            'Lengkong',
            'Kiaracondong',
            'Sukasari',
            'Buahbatu',
            'Sumur Bandung',
            'Antapani',
            'Cibiru',
            'Cidadap',
            'Bandung Wetan',
            'Cicendo',
            'Regol',
            'Astanajanyar',
            'Bojongloa Kaler',
            'Bojongloa Kidul',
            'Babakan Ciparay',
            'Bandung Kulon',
            'Andir',
            'Sukajadi',
            'Batununggal',
            'Rancasari',
            'Arcamanik',
            'Ujungberung',
            'Gedebage',
            'Panyileukan',
            'Mandalajati',
            'Cinambo',
        ];

        return view('livewire.dashboard.create-kost', [
            'facilities' => $facilities,
            'districts' => $districts,
            'googleMapsApiKey' => config('services.google.maps_api_key') ?: env('GOOGLE_MAPS_API_KEY'),
        ])->layout('layouts.app', [
            'title' => 'Tambah Kost Baru — KostBandung.id',
        ]);
    }
}
