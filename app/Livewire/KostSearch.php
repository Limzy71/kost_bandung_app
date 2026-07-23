<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kost;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class KostSearch extends Component
{
    use WithPagination;

    public $search = '';

    public $gender = '';

    public $price_min = '';

    public $price_max = '';

    public $district = '';

    public function mount()
    {
        // If user does a hard refresh with '?page=' in the URL, redirect to clean URL to force reset.
        if (request()->has('page')) {
            return redirect()->to(request()->url());
        }
    }

    public function updating($key)
    {
        if (in_array($key, ['search', 'gender', 'price_min', 'price_max', 'district'])) {
            $this->resetPage();
        }
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->gender = '';
        $this->district = '';
        $this->price_min = '';
        $this->price_max = '';
        $this->resetPage();
    }

    public function updatedPage(): void
    {
        $this->dispatch('scroll-to-home-list');
    }

    public function render()
    {
        if (is_numeric($this->price_min) && is_numeric($this->price_max)) {
            if ((int)$this->price_min > (int)$this->price_max) {
                $this->price_max = null;
            }
        }

        $query = Kost::query()->with(['primaryImage', 'facilities'])->where('status', 'published')->where('is_available', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->gender) {
            $query->where('gender_type', $this->gender);
        }

        if ($this->price_min) {
            $query->where('price_monthly', '>=', $this->price_min);
        }

        if ($this->price_max) {
            $query->where('price_monthly', '<=', $this->price_max);
        }

        if ($this->district) {
            $query->where('district', $this->district);
        }

        $query->orderByRaw('boosted_at IS NULL, boosted_at DESC')
                ->orderByDesc('created_at');

        $districts = Kost::select('district')->whereNotNull('district')->distinct()->orderBy('district')->pluck('district');
        $kosts = $query->paginate(12);

        $mapItems = $kosts->map(function ($k) {
            $priceFormatted = $k->price_monthly >= 1000000 
                ? round($k->price_monthly / 1000000, 1) . 'Jt'
                : round($k->price_monthly / 1000) . 'K';
                
            $priceFull = 'Rp ' . number_format($k->price_monthly, 0, ',', '.');
            $img = $k->primaryImage 
                ? (Str::startsWith($k->primaryImage->image_path, 'http') ? $k->primaryImage->image_path : Storage::url($k->primaryImage->image_path))
                : 'https://placehold.co/400x300/eeeeee/31343c?text=' . urlencode($k->name);

            return [
                'id' => $k->id,
                'name' => $k->name,
                'slug' => $k->slug,
                'district' => $k->district,
                'address' => $k->address,
                'gender' => $k->gender_type,
                'price_short' => $priceFormatted,
                'price_full' => $priceFull,
                'lat' => (float) $k->latitude,
                'lng' => (float) $k->longitude,
                'image' => $img,
                'url' => route('kost.show', $k->slug),
                'is_boosted' => (bool) $k->boosted_at,
            ];
        })->values()->toArray();

        return view('livewire.kost-search', [
            'kosts' => $kosts,
            'districts' => $districts,
            'mapItems' => $mapItems,
            'googleMapsApiKey' => config('services.google.maps_api_key') ?: env('GOOGLE_MAPS_API_KEY'),
        ]);
    }
}
