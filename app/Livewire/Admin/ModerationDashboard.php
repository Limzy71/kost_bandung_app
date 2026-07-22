<?php

namespace App\Livewire\Admin;

use App\Models\Kost;
use Livewire\Component;
use Livewire\WithPagination;

class ModerationDashboard extends Component
{
    use WithPagination;

    public string $search = '';
    public string $activeTab = 'pending'; // 'pending', 'published', 'rejected', 'all'

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

    public function render()
    {
        $pendingCount = Kost::where('status', 'pending')->count();
        $publishedCount = Kost::where('status', 'published')->count();
        $rejectedCount = Kost::where('status', 'rejected')->count();
        $totalCount = Kost::count();

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
        ])->layout('layouts.app', [
            'title' => 'Moderation Dashboard — Admin KostBandung.id',
        ]);
    }
}
