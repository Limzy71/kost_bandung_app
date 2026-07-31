<?php

namespace App\Livewire\Admin;

use App\Models\Facility;
use App\Models\Kost;
use Livewire\Component;
use Livewire\WithPagination;

class ModerationDashboard extends Component
{
    use WithPagination;

    public string $search = '';
    public string $activeTab = 'pending'; // 'pending', 'published', 'rejected', 'all', 'facilities'

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function approve(int $kostId): void
    {
        $kost = Kost::find($kostId);

        if ($kost) {
            $kost->status = 'published';
            $kost->save();

            $this->dispatch('show-toast', message: 'Properti "' . $kost->name . '" telah DISETUJUI & TAYANG PUBLIK!');
        }
    }

    public function reject(int $kostId): void
    {
        $kost = Kost::find($kostId);

        if ($kost) {
            $kost->status = 'rejected';
            $kost->save();

            $this->dispatch('show-toast', message: 'Properti "' . $kost->name . '" telah DITOLAK.');
        }
    }

    public function approveFacility(int $facilityId): void
    {
        $facility = Facility::find($facilityId);

        if ($facility) {
            $facility->status = 'approved';
            $facility->save();

            $this->dispatch('show-toast', message: 'Fasilitas "' . $facility->name . '" telah DISETUJUI & Tersedia untuk Semua Pemilik!');
        }
    }

    public function rejectFacility(int $facilityId): void
    {
        $facility = Facility::find($facilityId);

        if ($facility) {
            $facility->kosts()->detach();
            $facility->status = 'rejected';
            $facility->save();

            $this->dispatch('show-toast', message: 'Fasilitas "' . $facility->name . '" telah DITOLAK dan DILEPAS dari seluruh kost.');
        }
    }

    public function render()
    {
        $pendingCount = Kost::where('status', 'pending')->count();
        $publishedCount = Kost::where('status', 'published')->count();
        $rejectedCount = Kost::where('status', 'rejected')->count();
        $totalCount = Kost::count();
        $pendingFacilityCount = Facility::where('status', 'pending')->count();

        if ($this->activeTab === 'facilities') {
            $facilities = Facility::where('status', 'pending')
                ->with(['kosts.user'])
                ->orderBy('name')
                ->paginate(9);

            return view('livewire.admin.moderation-dashboard', [
                'facilities' => $facilities,
                'pendingCount' => $pendingCount,
                'publishedCount' => $publishedCount,
                'rejectedCount' => $rejectedCount,
                'totalCount' => $totalCount,
                'pendingFacilityCount' => $pendingFacilityCount,
            ])->layout('layouts.app', [
                'title' => 'Moderation Dashboard — Admin KostBandung.id',
            ]);
        }

        $query = Kost::query()
            ->with(['user', 'primaryImage', 'facilities', 'rules'])
            ->when($this->activeTab !== 'all', function ($q) {
                $q->where('status', $this->activeTab);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('district', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($u) {
                            $u->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'published' THEN 2 ELSE 3 END")
            ->latest();

        return view('livewire.admin.moderation-dashboard', [
            'kosts' => $query->paginate(9),
            'pendingCount' => $pendingCount,
            'publishedCount' => $publishedCount,
            'rejectedCount' => $rejectedCount,
            'totalCount' => $totalCount,
            'pendingFacilityCount' => $pendingFacilityCount,
        ])->layout('layouts.app', [
            'title' => 'Moderation Dashboard — Admin KostBandung.id',
        ]);
    }
}
