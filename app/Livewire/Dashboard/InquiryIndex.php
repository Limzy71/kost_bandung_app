<?php

namespace App\Livewire\Dashboard;

use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class InquiryIndex extends Component
{
    use WithPagination;

    public string $filter = 'all'; // all, unread, read, archived

    public ?int $replyingToId = null;

    public string $replyMessage = '';

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function openReplyModal(int $id): void
    {
        $inquiry = $this->findOwnedInquiry($id);

        if ($inquiry) {
            $this->replyingToId = $inquiry->id;
            $this->replyMessage = $inquiry->owner_reply ?? '';
        }
    }

    public function closeReplyModal(): void
    {
        $this->reset(['replyingToId', 'replyMessage']);
    }

    public function replyInquiry(): void
    {
        $this->validate([
            'replyMessage' => 'required|string|max:1000',
        ]);

        $inquiry = $this->findOwnedInquiry($this->replyingToId);

        if (! $inquiry) {
            abort(403);
        }

        $inquiry->update([
            'owner_reply' => $this->replyMessage,
            'replied_at' => now(),
            'status' => 'read',
        ]);

        $this->reset(['replyingToId', 'replyMessage']);

        session()->flash('success', 'Balasan berhasil dikirim ke pencari kost.');
    }

    protected function findOwnedInquiry(int $id): ?Inquiry
    {
        return Inquiry::whereHas('kost', function ($q) {
            $q->where('user_id', Auth::id());
        })->find($id);
    }

    public function markAsRead(int $id): void
    {
        $inquiry = $this->findOwnedInquiry($id);

        if ($inquiry && $inquiry->status === 'unread') {
            $inquiry->update(['status' => 'read']);
        }
    }

    public function toggleArchive(int $id): void
    {
        $inquiry = $this->findOwnedInquiry($id);

        if ($inquiry) {
            $inquiry->update(['status' => $inquiry->status === 'archived' ? 'read' : 'archived']);
        }
    }

    public function render(): View
    {
        // Get inquiries for kosts owned by this user
        $query = Inquiry::with(['kost'])
            ->whereHas('kost', function ($q) {
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
            'inquiries' => $inquiries,
        ])->layout('layouts.app', ['title' => 'Inbox Pesan — KostBandung.web.id']);
    }
}
