<?php

namespace App\Livewire;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kost;

class KostSearch extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $gender = '';

    #[Url(history: true)]
    public $price_min = '';

    #[Url(history: true)]
    public $price_max = '';

    #[Url(history: true)]
    public $district = '';

    #[Url(history: true)]
    public int $page = 1;

    public function mount(): void
    {
        $page = request()->integer('page', 1);
        if ($page > 1) {
            $this->setPage($page);
        }
    }

    public function updating($key)
    {
        if (in_array($key, ['search', 'gender', 'price_min', 'price_max', 'district'])) {
            $this->page = 1;
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->gender = '';
        $this->district = '';
        $this->price_min = '';
        $this->price_max = '';
        $this->page = 1;
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

        $query = Kost::query()->with(['primaryImage', 'facilities'])->where('is_available', true);

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

        return view('livewire.kost-search', [
            'kosts' => $query->paginate(12),
            'districts' => $districts,
        ]);
    }
}
