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
    public string $total_rooms = '1';
    public string $available_rooms = '1';
    public array $selectedFacilities = [];
    public $photo;

    protected array $rules = [
        'name' => 'required|string|max:255',
        'gender_type' => 'required|in:putra,putri,campur',
        'description' => 'required|string|min:10',
        'district' => 'required|string|max:100',
        'address' => 'required|string|max:500',
        'price_monthly' => 'required|numeric|min:100000',
        'total_rooms' => 'required|integer|min:1',
        'available_rooms' => 'required|integer|min:0',
        'selectedFacilities' => 'nullable|array',
        'selectedFacilities.*' => 'exists:facilities,id',
        'photo' => 'required|image|max:2048',
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
        'total_rooms.required' => 'Total jumlah kamar wajib diisi.',
        'total_rooms.integer' => 'Total kamar harus berupa angka bulat.',
        'total_rooms.min' => 'Total kamar minimal 1.',
        'available_rooms.required' => 'Sisa kamar tersedia wajib diisi.',
        'available_rooms.integer' => 'Sisa kamar harus berupa angka bulat.',
        'photo.required' => 'Foto utama kost wajib diunggah.',
        'photo.image' => 'File harus berupa gambar (JPG, PNG, WEBP).',
        'photo.max' => 'Ukuran foto tidak boleh melebihi 2MB.',
    ];

    public function save()
    {
        $this->validate();

        $slug = Str::slug($this->name);
        $originalSlug = $slug;
        $count = 1;
        while (Kost::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        // Store photo in public storage
        $path = $this->photo->store('kosts', 'public');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Create Kost record with pending status (requires Admin review before public listing)
        $kost = Kost::create([
            'user_id' => $user->id,
            'name' => $this->name,
            'slug' => $slug,
            'description' => $this->description,
            'gender_type' => $this->gender_type,
            'price_monthly' => $this->price_monthly,
            'address' => $this->address,
            'district' => $this->district,
            'latitude' => -6.917464, // Center of Bandung default
            'longitude' => 107.619123,
            'is_available' => ((int)$this->available_rooms > 0),
            'status' => 'pending', // Draft / Pending Admin review
            'total_rooms' => (int)$this->total_rooms,
            'available_rooms' => (int)$this->available_rooms,
        ]);

        // Create KostImage primary record
        KostImage::create([
            'kost_id' => $kost->id,
            'image_path' => $path,
            'is_primary' => true,
        ]);

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
        ])->layout('layouts.app', [
            'title' => 'Tambah Kost Baru — KostBandung.id',
        ]);
    }
}
