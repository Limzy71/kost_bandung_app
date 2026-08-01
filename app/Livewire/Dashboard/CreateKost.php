<?php

namespace App\Livewire\Dashboard;

use App\Models\Facility;
use App\Models\Kost;
use App\Models\KostImage;
use App\Models\KostPrice;
use App\Models\Rule;
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
    public string $rent_period = 'monthly';
    public string $price_deposit = '';
    public bool $include_utilities = false;
    public string $latitude = '';
    public string $longitude = '';
    public string $total_rooms = '1';
    public string $available_rooms = '1';
    public string $whatsapp_contact = '';
    public string $nearby_landmarks = '';
    public array $selectedFacilities = [];
    public array $customFacilities = [];
    public string $newRoomFacility = '';
    public string $newBuildingFacility = '';
    public array $selectedRules = [];
    public array $customRules = [];
    public string $newRule = '';
    public string $newLandmark = '';
    public array $landmarkList = [];
    public string $additional_rules_note = '';
    public array $photos = [];
    public array $existingPhotos = [];
    public ?int $primaryPhotoId = null;
    public ?string $district_auto_message = null;
    public array $extraPeriods = [];
    public array $extraPeriodPrices = [
        'daily' => '',
        'weekly' => '',
        'three_monthly' => '',
        'six_monthly' => '',
        'yearly' => '',
    ];

    public function updatedDistrict($value)
    {
        if ($this->latitude === '' && $this->longitude === '') {
            $districtsConfig = config('bandung.districts', []);
            if (isset($districtsConfig[$value]['center'])) {
                $this->latitude = (string) $districtsConfig[$value]['center']['lat'];
                $this->longitude = (string) $districtsConfig[$value]['center']['lng'];
            }
        }
    }


    public function updatedPriceMonthly($value)
    {
        if (is_numeric($value) && $value < 0) {
            $this->price_monthly = '0';
        }
    }

    public function updatedPriceDeposit($value)
    {
        if (is_numeric($value) && $value < 0) {
            $this->price_deposit = '0';
        }
    }

    public function updatedRentPeriod($value)
    {
        $this->extraPeriods = array_values(array_filter(
            $this->extraPeriods,
            fn ($period) => $period !== $value
        ));
        $this->extraPeriodPrices[$value] = '';
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

    public function updatedExtraPeriods($value)
    {
        foreach (array_keys($this->extraPeriodPrices) as $period) {
            if (! in_array($period, $value)) {
                $this->extraPeriodPrices[$period] = '';
            }
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
            'rent_period' => 'required|in:daily,weekly,monthly,three_monthly,six_monthly,yearly',
            'price_deposit' => 'nullable|numeric|min:0',
            'include_utilities' => 'boolean',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'total_rooms' => 'required|integer|min:1',
            'available_rooms' => 'required|integer|min:0|lte:total_rooms',
            'whatsapp_contact' => 'nullable|regex:/^[0-9+()\-\s]{9,16}$/',
            'nearby_landmarks' => 'nullable|string|max:1000',
            'selectedFacilities' => 'nullable|array',
            'selectedFacilities.*' => 'exists:facilities,id',
            'customFacilities' => 'nullable|array',
            'customFacilities.*.name' => [
                'string',
                'max:50',
                'distinct',
                function ($attribute, $value, $fail) {
                    $name = trim((string) $value);
                    if ($name === '') {
                        $fail('Nama fasilitas tidak boleh kosong.');
                        return;
                    }
                    if (Facility::whereRaw('LOWER(name) = ?', [Str::lower($name)])->exists()) {
                        $fail('Fasilitas "' . $name . '" sudah tersedia di daftar fasilitas.');
                    }
                },
            ],
            'customFacilities.*.type' => 'required|in:room,building',
            'selectedRules' => 'nullable|array',
            'selectedRules.*' => 'exists:rules,id',
            'customRules' => 'nullable|array',
            'customRules.*' => [
                'string',
                'max:50',
                'distinct',
                function ($attribute, $value, $fail) {
                    $name = trim((string) $value);
                    if ($name === '') {
                        $fail('Nama aturan tidak boleh kosong.');
                        return;
                    }
                    if (Rule::whereRaw('LOWER(name) = ?', [Str::lower($name)])->exists()) {
                        $fail('Aturan "' . $name . '" sudah tersedia di daftar aturan.');
                    }
                },
            ],
            'additional_rules_note' => 'nullable|string|max:500',
            'photos' => 'required|array|min:4|max:10',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'extraPeriods' => 'nullable|array',
            'extraPeriods.*' => 'in:daily,weekly,monthly,three_monthly,six_monthly,yearly',
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
            'price_monthly.required' => 'Harga sewa utama wajib diisi.',
            'price_monthly.numeric' => 'Harga sewa utama harus berupa angka.',
            'price_monthly.min' => 'Harga sewa utama minimal Rp 100.000.',
            'rent_period.required' => 'Periode sewa utama wajib dipilih.',
            'rent_period.in' => 'Periode sewa utama tidak valid.',
            'price_deposit.numeric' => 'Uang deposit harus berupa angka.',
            'price_deposit.min' => 'Uang deposit tidak boleh negatif.',
            'latitude.required' => 'Titik lokasi peta wajib ditentukan.',
            'longitude.required' => 'Titik lokasi peta wajib ditentukan.',
            'total_rooms.required' => 'Total jumlah kamar wajib diisi.',
            'total_rooms.integer' => 'Total kamar harus berupa angka bulat.',
            'total_rooms.min' => 'Total kamar minimal 1.',
            'available_rooms.required' => 'Sisa kamar tersedia wajib diisi.',
            'available_rooms.integer' => 'Sisa kamar harus berupa angka bulat.',
            'available_rooms.min' => 'Sisa kamar minimal 0.',
            'available_rooms.lte' => 'Sisa kamar tersedia tidak boleh melebihi total jumlah kamar.',
            'whatsapp_contact.regex' => 'Nomor WhatsApp tidak valid. Gunakan format angka 9–16 digit.',
            'photos.required' => 'MINIMAL 4 FOTO KOST WAJIB DIUNGGAH.',
            'photos.min' => 'MINIMAL 4 FOTO KOST WAJIB DIUNGGAH.',
            'photos.max' => 'MAKSIMAL 10 FOTO KOST DAPAT DIUNGGAH.',
            'photos.*.image' => 'File harus berupa gambar (JPG, PNG, WEBP).',
            'photos.*.mimes' => 'File harus berupa gambar dengan format JPG, PNG, atau WEBP.',
            'photos.*.max' => 'Ukuran setiap foto tidak boleh melebihi 2MB.',
            'extraPeriods.*.in' => 'Periode sewa tidak valid.',
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

    public function setPrimaryPhoto($index)
    {
        if (! isset($this->photos[$index])) {
            return;
        }

        $photo = $this->photos[$index];
        unset($this->photos[$index]);
        array_unshift($this->photos, $photo);
        $this->photos = array_values($this->photos);
    }

    public function addFacility(string $type)
    {
        $property = $type === 'room' ? 'newRoomFacility' : 'newBuildingFacility';
        $this->resetErrorBag($property);

        $name = trim($this->$property);

        if ($name === '') {
            $this->addError($property, 'Nama fasilitas tidak boleh kosong.');
            return;
        }

        if (mb_strlen($name) > 50) {
            $this->addError($property, 'Fasilitas lain maksimal 50 karakter.');
            return;
        }

        $duplicateInDb = Facility::whereRaw('LOWER(name) = ?', [Str::lower($name)])->exists();
        if ($duplicateInDb) {
            $this->addError($property, 'Fasilitas "' . $name . '" sudah tersedia di daftar fasilitas.');
            return;
        }

        foreach ($this->customFacilities as $existing) {
            if (Str::lower($existing['name']) === Str::lower($name)) {
                $this->addError($property, 'Fasilitas "' . $name . '" sudah ditambahkan.');
                return;
            }
        }

        $this->customFacilities[] = [
            'name' => $name,
            'type' => $type,
        ];
        $this->$property = '';
    }

    public function removeCustomFacility($index)
    {
        if (isset($this->customFacilities[$index])) {
            unset($this->customFacilities[$index]);
            $this->customFacilities = array_values($this->customFacilities);
        }
    }

    public function addRule()
    {
        $this->resetErrorBag('newRule');

        $name = trim($this->newRule);

        if ($name === '') {
            $this->addError('newRule', 'Nama aturan tidak boleh kosong.');
            return;
        }

        if (mb_strlen($name) > 50) {
            $this->addError('newRule', 'Aturan lain maksimal 50 karakter.');
            return;
        }

        $duplicateInDb = Rule::whereRaw('LOWER(name) = ?', [Str::lower($name)])->exists();
        if ($duplicateInDb) {
            $this->addError('newRule', 'Aturan "' . $name . '" sudah tersedia di daftar aturan.');
            return;
        }

        foreach ($this->customRules as $existing) {
            if (Str::lower($existing) === Str::lower($name)) {
                $this->addError('newRule', 'Aturan "' . $name . '" sudah ditambahkan.');
                return;
            }
        }

        $this->customRules[] = $name;
        $this->newRule = '';
    }

    public function removeCustomRule($index)
    {
        if (isset($this->customRules[$index])) {
            unset($this->customRules[$index]);
            $this->customRules = array_values($this->customRules);
        }
    }

    public function addLandmark()
    {
        $name = trim($this->newLandmark);
        if ($name === '') {
            return;
        }

        foreach ($this->landmarkList as $existing) {
            if (Str::lower($existing) === Str::lower($name)) {
                $this->addError('newLandmark', 'Landmark "' . $name . '" sudah ditambahkan.');
                return;
            }
        }

        $this->landmarkList[] = $name;
        $this->newLandmark = '';
        $this->syncLandmarksString();
    }

    public function addLandmarks(array $items)
    {
        $added = 0;
        foreach ($items as $item) {
            $name = trim((string) $item);
            $name = Str::limit($name, 60, '');
            if ($name === '' || count($this->landmarkList) >= 12 || $this->landmarkExists($name)) {
                continue;
            }
            $this->landmarkList[] = $name;
            $added++;
        }

        if ($added > 0) {
            $this->syncLandmarksString();
        }

        $this->dispatch('landmarks-added', added: $added);
    }

    private function landmarkExists(string $name): bool
    {
        foreach ($this->landmarkList as $existing) {
            if (Str::lower($existing) === Str::lower($name)) {
                return true;
            }
        }

        return false;
    }

    public function removeLandmark($index)
    {
        if (isset($this->landmarkList[$index])) {
            unset($this->landmarkList[$index]);
            $this->landmarkList = array_values($this->landmarkList);
            $this->syncLandmarksString();
        }
    }

    private function syncLandmarksString()
    {
        $this->nearby_landmarks = implode(', ', $this->landmarkList);
    }

    public function boot()
    {
        $this->withValidator(function ($validator) {
            $validator->after(function ($validator) {
                foreach ($this->extraPeriods as $period) {
                    $price = trim((string) ($this->extraPeriodPrices[$period] ?? ''));
                    if ($price === '') {
                        $validator->errors()->add(
                            'extraPeriodPrices.' . $period,
                            'Harga periode ini wajib diisi karena sudah dipilih.'
                        );
                    } elseif (! is_numeric($price) || $price < 10000) {
                        $validator->errors()->add(
                            'extraPeriodPrices.' . $period,
                            'Harga periode ini tidak valid (minimal Rp 10.000).'
                        );
                    }
                }

                if (in_array($this->rent_period, $this->extraPeriods, true)) {
                    $validator->errors()->add(
                        'extraPeriods',
                        'Periode sewa utama tidak boleh diduplikasi pada periode lain.'
                    );
                }
            });
        });
    }

    public function save()
    {
        $this->validate();

        $key = 'create_kost_' . request()->ip() . '_' . Auth::id();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            if ($seconds < 60) {
                $this->addError('name', 'Terlalu banyak permintaan pembuatan kost. Silakan tunggu ' . $seconds . ' detik lagi.');
            } else {
                $this->addError('name', 'Terlalu banyak permintaan pembuatan kost. Silakan tunggu ' . ceil($seconds/60) . ' menit lagi.');
            }
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
            'rent_period' => $this->rent_period,
            'price_deposit' => $this->price_deposit !== '' ? $this->price_deposit : null,
            'include_utilities' => $this->include_utilities,
            'address' => $this->address,
            'district' => $this->district,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_available' => ((int)$this->available_rooms > 0),
            'status' => 'pending', // Draft / Pending Admin review
            'total_rooms' => (int)$this->total_rooms,
            'available_rooms' => (int)$this->available_rooms,
            'whatsapp_contact' => $this->whatsapp_contact !== '' ? $this->whatsapp_contact : null,
            'nearby_landmarks' => $this->nearby_landmarks !== '' ? $this->nearby_landmarks : null,
            'additional_rules_note' => $this->additional_rules_note !== '' ? $this->additional_rules_note : null,
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

        // Store extra period prices
        $this->extraPeriods = array_values(array_unique($this->extraPeriods));
        foreach ($this->extraPeriods as $period) {
            KostPrice::create([
                'kost_id' => $kost->id,
                'period' => $period,
                'price' => (float) $this->extraPeriodPrices[$period],
            ]);
        }

        // Attach facilities if selected
        $facilityIds = $this->selectedFacilities;

        foreach ($this->customFacilities as $custom) {
            $name = trim((string) ($custom['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $type = in_array($custom['type'] ?? 'building', ['room', 'building']) ? $custom['type'] : 'building';
            $facility = Facility::firstOrCreate(
                ['name' => $name],
                ['type' => $type, 'status' => 'pending', 'user_id' => $user->id]
            );
            $facilityIds[] = $facility->id;
        }

        if (! empty($facilityIds)) {
            $kost->facilities()->attach(array_unique($facilityIds));
        }

        // Attach rules if selected
        $ruleIds = $this->selectedRules;

        foreach ($this->customRules as $customRuleName) {
            $name = trim($customRuleName);
            if ($name === '') {
                continue;
            }
            $rule = Rule::firstOrCreate(['name' => $name]);
            $ruleIds[] = $rule->id;
        }

        if (! empty($ruleIds)) {
            $kost->rules()->attach(array_unique($ruleIds));
        }

        session()->flash('status', 'Properti kost "' . $kost->name . '" berhasil diajukan dan sedang dalam peninjauan Admin!');

        return redirect()->route('dashboard');
    }

    public function render()
    {
        $facilities = Facility::where('status', 'approved')->orderBy('name')->get();

        $rules = Rule::orderBy('name')->get();

        $districts = array_keys(config('bandung.districts', []));

        return view('livewire.dashboard.create-kost', [
            'facilities' => $facilities,
            'rules' => $rules,
            'extraPeriodLabels' => KostPrice::periodLabels(),
            'districts' => $districts,
            'googleMapsApiKey' => config('services.google.maps_api_key') ?: env('GOOGLE_MAPS_API_KEY'),
        ])->layout('layouts.app', [
            'title' => 'Tambah Kost Baru — KostBandung.id',
        ]);
    }
}
