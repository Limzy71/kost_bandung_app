<?php

namespace App\Livewire\Dashboard;

use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SeekerInquiries extends Component
{
    use WithPagination;

    public string $filter = 'all'; // all, replied, waiting

    public function mount(): void
    {
        // Mark all unseen owner replies as seen when the seeker opens this page,
        // clearing the notification badge in the navbar.
        Inquiry::where('user_id', Auth::id())
            ->whereNotNull('owner_reply')
            ->whereNull('seeker_seen_reply_at')
            ->update(['seeker_seen_reply_at' => now()]);
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Inquiry::with(['kost'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($this->filter === 'replied') {
            $query->whereNotNull('owner_reply');
        } elseif ($this->filter === 'waiting') {
            $query->whereNull('owner_reply');
        }

        $inquiries = $query->paginate(10);

        return view('livewire.dashboard.seeker-inquiries', [
            'inquiries' => $inquiries,
        ])->layout('layouts.app', ['title' => 'Pesan Terkirim — KostBandung.web.id']);
    }
}
