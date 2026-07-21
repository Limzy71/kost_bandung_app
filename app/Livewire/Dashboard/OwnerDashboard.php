<?php

namespace App\Livewire\Dashboard;

use App\Models\Inquiry;
use App\Models\Kost;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OwnerDashboard extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleAvailability(int $kostId): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $kost = $user->kosts()->find($kostId);

        if ($kost) {
            $kost->is_available = ! $kost->is_available;
            $kost->save();
            session()->flash('status', 'Status ketersediaan "' . $kost->name . '" berhasil diperbarui.');
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
            ->with(['primaryImage', 'facilities', 'inquiries'])
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
