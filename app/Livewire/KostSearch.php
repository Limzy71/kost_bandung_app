<?php

namespace App\Livewire;

use App\Models\Kost;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class KostSearch extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $gender = '';

    #[Url]
    public ?string $price_min = '';

    #[Url]
    public ?string $price_max = '';

    #[Url]
    public string $district = '';

    #[Url]
    public string $rent_period = '';

    #[Url]
    public bool $verified_only = false;

    /** @var list<string> Selected facility names for filtering (AND condition). */
    #[Url]
    public array $facilities = [];

    /** Sort mode: recommended, price_asc, price_desc, newest. */
    #[Url]
    public string $sort = 'recommended';

    // Stored as a Livewire public property so Alpine can read it via $wire.mapItems
    // without needing x-effect or inline JSON in HTML attributes.
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $mapItems = [];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedGender(): void
    {
        $this->resetPage();
    }

    public function updatedDistrict(): void
    {
        $this->resetPage();
    }

    public function updatedPriceMin(): void
    {
        $this->resetPage();
    }

    public function updatedRentPeriod(): void
    {
        $this->resetPage();
    }

    public function updatedPriceMax(): void
    {
        $this->resetPage();
    }

    public function updatedVerifiedOnly(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updatedFacilities(): void
    {
        $this->resetPage();
    }

    public function toggleFacility(string $facilityName): void
    {
        if (in_array($facilityName, $this->facilities, true)) {
            $this->facilities = array_values(array_diff($this->facilities, [$facilityName]));
        } else {
            $this->facilities[] = $facilityName;
        }

        $this->resetPage();
    }

    public function setPricePreset(string $preset): void
    {
        $values = match ($preset) {
            'under_1m' => ['min' => '', 'max' => '1000000'],
            '1m_2m' => ['min' => '1000000', 'max' => '2000000'],
            '2m_3m' => ['min' => '2000000', 'max' => '3000000'],
            'above_3m' => ['min' => '3000000', 'max' => ''],
            default => ['min' => '', 'max' => ''],
        };

        $this->price_min = $values['min'];
        $this->price_max = $values['max'];
        $this->resetPage();
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    /**
     * Apply all draft filters at once from client side.
     *
     * @param list<string> $facilities
     */
    public function applyAllFilters(
        string $search = '',
        string $district = '',
        string $gender = '',
        string $rent_period = '',
        ?string $price_min = '',
        ?string $price_max = '',
        bool $verified_only = false,
        array $facilities = []
    ): void {
        $this->search = $search;
        $this->district = $district;
        $this->gender = $gender;
        $this->rent_period = $rent_period;
        $this->price_min = $price_min ?: '';
        $this->price_max = $price_max ?: '';
        $this->verified_only = $verified_only;
        $this->facilities = $facilities;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->gender = '';
        $this->district = '';
        $this->rent_period = '';
        $this->price_min = '';
        $this->price_max = '';
        $this->verified_only = false;
        $this->facilities = [];
        $this->sort = 'recommended';
        $this->resetPage();
    }

    public function updatedPage(): void
    {
        $this->dispatch('scroll-to-home-list');
    }

    public function render(): View
    {
        if (is_numeric($this->price_min) && is_numeric($this->price_max)) {
            if ((int) $this->price_min > (int) $this->price_max) {
                $this->price_max = null;
            }
        }

        $query = Kost::query()
            ->with(['primaryImage', 'user', 'facilities' => function ($q) {
                $q->where('status', 'approved');
            }])
            ->where('status', 'published')
            ->where('is_available', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('address', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->gender) {
            $query->where('gender_type', $this->gender);
        }

        if ($this->rent_period) {
            $query->where('rent_period', $this->rent_period);
        }

        if ($this->price_min) {
            $query->where('price_monthly', '>=', (int) $this->price_min);
        }

        if ($this->price_max) {
            $query->where('price_monthly', '<=', (int) $this->price_max);
        }

        if ($this->verified_only) {
            $query->where('ownership_verification_status', 'verified')
                ->whereHas('user', fn ($q) => $q->where('identity_verification_status', 'verified'));
        }

        // Facility filter (AND condition: must have ALL selected facilities)
        foreach ($this->facilities as $facilityName) {
            $query->whereHas('facilities', fn ($q) => $q->where('name', $facilityName)->where('status', 'approved'));
        }

        // Compute district counts before applying the district filter
        $districtCounts = (clone $query)
            ->selectRaw('district, count(*) as total')
            ->groupBy('district')
            ->pluck('total', 'district')
            ->toArray();

        if ($this->district) {
            $query->where('district', $this->district);
        }

        $now = now();

        // Apply sorting
        match ($this->sort) {
            'price_asc' => $query->orderBy('price_monthly'),
            'price_desc' => $query->orderByDesc('price_monthly'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query
                ->orderByRaw(
                    'CASE WHEN boost_expires_at IS NOT NULL AND boost_expires_at > ? THEN 1 ELSE 0 END DESC',
                    [$now]
                )
                ->orderByDesc('created_at'),
        };

        $districts = [];
        foreach (config('bandung.districts', []) as $key => $data) {
            $count = $districtCounts[$key] ?? 0;
            $districts[$key] = "$key ($count)";
        }

        $kosts = $query->paginate(12);

        // Build mapItems and store as public property so $wire.mapItems is
        // reactive in Alpine without needing inline JSON in HTML attributes.
        $this->mapItems = $kosts->getCollection()->map(function ($k) {
            $price = (int) $k->price_monthly;
            $priceFormatted = $price >= 1000000
                ? round($price / 1000000, 1).'Jt'
                : round($price / 1000).'K';

            $priceFull = 'Rp '.number_format($price, 0, ',', '.');
            $priceUnit = Kost::rentPeriodUnit($k->rent_period);

            $img = $k->primaryImage
                ? (Str::startsWith($k->primaryImage->image_path, 'http')
                    ? $k->primaryImage->image_path
                    : Storage::url($k->primaryImage->image_path))
                : 'https://placehold.co/400x300/eeeeee/31343c?text='.urlencode($k->name);

            return [
                'id' => $k->id,
                'name' => $k->name,
                'slug' => $k->slug,
                'district' => $k->district,
                'address' => $k->address,
                'gender' => $k->gender_type,
                'price_short' => $priceFormatted,
                'price_full' => $priceFull,
                'price_unit' => $priceUnit,
                'lat' => (float) $k->latitude,
                'lng' => (float) $k->longitude,
                'image' => $img,
                'url' => route('kost.show', $k->slug),
                'is_boosted' => $k->boost_expires_at?->isFuture() ?? false,
            ];
        })->values()->toArray();

        // Notify Alpine map component that markers need to be refreshed
        $this->dispatch('map-items-updated');

        $totalKostInDb = Kost::where('status', 'published')->where('is_available', true)->count();
        $hasSearch = ! empty(trim($this->search));
        $hasOtherFilters = (bool) ($this->gender || $this->district || $this->rent_period || $this->price_min || $this->price_max || $this->verified_only || $this->facilities || $this->sort !== 'recommended');

        return view('livewire.kost-search', [
            'kosts' => $kosts,
            'districts' => $districts,
            'districtBounds' => config('bandung.districts', []),
            'googleMapsApiKey' => config('services.google.maps_api_key'),
            'hasActiveFilter' => $hasSearch || $hasOtherFilters,
            'hasSearchOnly' => $hasSearch && ! $hasOtherFilters,
            'hasBothSearchAndFilters' => $hasSearch && $hasOtherFilters,
            'totalKostInDb' => $totalKostInDb,
        ]);
    }
}
