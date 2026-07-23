<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Inquiry;
use App\Models\Kost;
use Illuminate\Support\Facades\Auth;

class InquiryIndex extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, unread, read, archived

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function markAsRead($id)
    {
        $inquiry = Inquiry::whereHas('kost', function($q) {
            $q->where('user_id', Auth::id());
        })->find($id);

        if ($inquiry && $inquiry->status === 'unread') {
            $inquiry->update(['status' => 'read']);
        }
    }

    public function toggleArchive($id)
    {
        $inquiry = Inquiry::whereHas('kost', function($q) {
            $q->where('user_id', Auth::id());
        })->find($id);

        if ($inquiry) {
            $inquiry->update(['status' => $inquiry->status === 'archived' ? 'read' : 'archived']);
        }
    }

    public function render()
    {
        // Get inquiries for kosts owned by this user
        $query = Inquiry::with(['kost'])
            ->whereHas('kost', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc');

        if ($this->filter === 'unread') {
            $query->where('status', 'unread');
        } elseif ($this->filter === 'read') {
            $query->where('status', 'read');
        } elseif ($this->filter === 'archived') {
            $query->where('status', 'archived');
        } else {
            $query->where('status', '!=', 'archived');
        }

        $inquiries = $query->paginate(10);

        return view('livewire.dashboard.inquiry-index', [
            'inquiries' => $inquiries
        ])->layout('layouts.app', ['title' => 'Inbox Pesan — KostBandung.id']);
    }
}
