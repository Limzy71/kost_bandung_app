<?php

namespace App\Livewire\Dashboard;

use App\Models\Inquiry;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;
use Livewire\WithPagination;

/**
 * @property-read int $totalProperti
 * @property-read int $totalKamarTersedia
 * @property-read int $pesanMasuk
 */
class OwnerDashboard extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    /**
     * @return RedirectResponse|Redirector|null
     */
    public function mount()
    {
        // If user does a hard refresh with '?page=' in the URL, redirect to clean URL to force reset.
        if (request()->has('page')) {
            return redirect()->to(request()->url());
        }

        return null;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function updatedPage(): void
    {
        $this->dispatch('scroll-to-list');
    }

    public function toggleAvailability(int $kostId): void
    {
        /** @var User $user */
        $user = Auth::user();
        $kost = $user->kosts()->find($kostId);

        if (! $kost) {
            return;
        }

        $kost->is_available = ! $kost->is_available;
        $kost->available_rooms = $kost->is_available ? ($kost->total_rooms ?: 1) : 0;
        $kost->save();

        $statusText = $kost->is_available ? 'TERSEDIA' : 'PENUH';
        $this->dispatch('show-toast', message: 'Status ketersediaan "'.$kost->name.'" diubah ke '.$statusText);
    }

    #[Computed]
    public function totalProperti(): int
    {
        return $this->ownerKostsQuery()->count();
    }

    #[Computed]
    public function totalKamarTersedia(): int
    {
        return $this->ownerKostsQuery()->where('is_available', true)->count();
    }

    #[Computed]
    public function pesanMasuk(): int
    {
        return Inquiry::whereIn('kost_id', $this->ownerKostsQuery()->toBase()->select('id'))->count();
    }

    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $kosts = $this->searchQuery()->paginate(9);

        return view('livewire.dashboard.owner-dashboard', [
            'owner' => $user,
            'totalProperti' => $this->totalProperti,
            'totalKamarTersedia' => $this->totalKamarTersedia,
            'pesanMasuk' => $this->pesanMasuk,
            'kosts' => $kosts,
        ])->layout('layouts.app', [
            'title' => 'Dashboard Pemilik Kost — KostBandung.id',
        ]);
    }

    /**
     * @return HasMany<Kost, User>
     */
    private function ownerKostsQuery(): HasMany
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->kosts();
    }

    /**
     * @return Builder<Kost>
     */
    private function searchQuery(): Builder
    {
        return $this->ownerKostsQuery()->getQuery()
            ->with(['primaryImage', 'facilities'])
            ->withCount('inquiries')
            ->when($this->search, function ($query) {
                $term = '%'.addcslashes($this->search, '%_').'%';

                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                        ->orWhere('district', 'like', $term)
                        ->orWhere('address', 'like', $term);
                });
            })
            ->latest();
    }
}
