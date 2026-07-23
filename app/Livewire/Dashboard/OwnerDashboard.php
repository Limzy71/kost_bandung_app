<?php

namespace App\Livewire\Dashboard;

use App\Models\Inquiry;
use App\Models\Kost;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class OwnerDashboard extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function mount()
    {
        // If user does a hard refresh with '?page=' in the URL, redirect to clean URL to force reset.
        if (request()->has('page')) {
            return redirect()->to(request()->url());
        }
    }

    public function applyFilters(): void
    {
        $this->resetPage();
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

    public function resetFilters(): void
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $kost = $user->kosts()->find($kostId);

        if ($kost) {
            $kost->is_available = ! $kost->is_available;
            $kost->save();

            $statusText = $kost->is_available ? 'TERSEDIA' : 'PENUH';
            $this->dispatch('show-toast', message: 'Status ketersediaan "' . $kost->name . '" diubah ke ' . $statusText);
        }
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $allOwnerKosts = $user->kosts();

        $totalProperti = $allOwnerKosts->count();
        $totalKamarTersedia = (clone $allOwnerKosts)->where('is_available', true)->count();

        $ownerKostIds = (clone $allOwnerKosts)->pluck('id');
        $pesanMasuk = Inquiry::whereIn('kost_id', $ownerKostIds)->count();

        $kostsQuery = $user->kosts()
            ->with(['primaryImage', 'facilities'])
            ->withCount('inquiries')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('district', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%');
            })
            ->latest();

        $kosts = $kostsQuery->paginate(9);

        return view('livewire.dashboard.owner-dashboard', [
            'owner' => $user,
            'totalProperti' => $totalProperti,
            'totalKamarTersedia' => $totalKamarTersedia,
            'pesanMasuk' => $pesanMasuk,
            'kosts' => $kosts,
        ])->layout('layouts.app', [
            'title' => 'Dashboard Pemilik Kost — KostBandung.id',
        ]);
    }
}
