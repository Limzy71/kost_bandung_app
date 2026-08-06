<?php

namespace App\Livewire\Contact;

use App\Models\AdminConversation;
use App\Models\AdminMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Livewire\WithPagination;

class AdminChat extends Component
{
    use WithPagination;

    public string $tab = 'active'; // active | history

    public ?int $selectedConversationId = null;

    public bool $showCompose = false;

    public string $category = '';

    public string $newBody = '';

    public string $followUpBody = '';

    public function mount(): void
    {
        abort_if(auth()->user()->role === 'admin', 403, 'Admin tidak dapat menggunakan fitur ini sebagai pengirim.');

        // Tandai semua balasan admin yang belum dibaca sebagai sudah dibaca,
        // membersihkan badge di navbar.
        AdminMessage::where('sender_type', 'admin')
            ->whereNull('read_at')
            ->whereHas('conversation', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->update(['read_at' => now()]);
    }

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    public function openCompose(): void
    {
        $this->showCompose = true;
    }

    public function closeCompose(): void
    {
        $this->reset(['category', 'newBody']);
        $this->showCompose = false;
    }

    public function sendNewConversation(): void
    {
        AdminConversation::expireStale();
        AdminConversation::pruneSoftDeleted();

        $this->validate([
            'category' => 'required|in:komplain,pertanyaan,masukan,lainnya',
            'newBody' => 'required|string|max:2000',
        ]);

        $key = $this->rateLimitKey();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('newBody', 'Terlalu banyak mengirim pesan. Coba lagi dalam '.$seconds.' detik.');

            return;
        }

        $conversation = AdminConversation::create([
            'user_id' => Auth::id(),
            'sender_role' => auth()->user()->role,
            'category' => $this->category,
            'status' => 'open',
            'awaiting_reply_at' => now(),
        ]);

        AdminMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'user',
            'sender_id' => Auth::id(),
            'body' => $this->newBody,
        ]);

        RateLimiter::hit($key, 3600);

        $this->reset(['category', 'newBody']);
        $this->showCompose = false;
        $this->selectedConversationId = $conversation->id;

        $this->dispatch('show-toast', message: 'Pesan berhasil dikirim ke Admin. Anda akan dibalas maksimal 1x24 jam.');
    }

    public function openConversation(int $id): void
    {
        $conversation = $this->findOwnedConversation($id);

        if (! $conversation) {
            abort(403);
        }

        $this->selectedConversationId = $conversation->id;
    }

    public function sendFollowUp(): void
    {
        AdminConversation::expireStale();
        AdminConversation::pruneSoftDeleted();

        $conversation = $this->findOwnedConversation($this->selectedConversationId);

        if (! $conversation) {
            abort(403);
        }

        if (! $conversation->isOpen()) {
            $this->addError('followUpBody', 'Percakapan ini sudah ditutup. Silakan buka percakapan baru untuk menghubungi Admin.');

            return;
        }

        $this->validate([
            'followUpBody' => 'required|string|max:2000',
        ]);

        $key = $this->rateLimitKey();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('followUpBody', 'Terlalu banyak mengirim pesan. Coba lagi dalam '.$seconds.' detik.');

            return;
        }

        AdminMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'user',
            'sender_id' => Auth::id(),
            'body' => $this->followUpBody,
        ]);

        $conversation->update(['awaiting_reply_at' => now()]);

        RateLimiter::hit($key, 3600);

        $this->reset('followUpBody');

        $this->dispatch('show-toast', message: 'Pesan terkirim. Admin akan membalas maksimal 1x24 jam.');
    }

    protected function rateLimitKey(): string
    {
        return 'admin_chat_'.Auth::id();
    }

    protected function findOwnedConversation(int $id): ?AdminConversation
    {
        return AdminConversation::where('user_id', Auth::id())->find($id);
    }

    public function render(): View
    {
        AdminConversation::expireStale();
        AdminConversation::pruneSoftDeleted();

        $query = AdminConversation::with(['user', 'latestMessage'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($this->tab === 'active') {
            $query->where('status', 'open');
        } else {
            $query->where('status', 'closed');
        }

        $conversations = $query->paginate(15);

        $selected = null;

        if ($this->selectedConversationId) {
            $selected = $this->findOwnedConversation($this->selectedConversationId);

            if ($selected) {
                $selected->load(['messages.sender', 'user']);
            }
        }

        return view('livewire.contact.admin-chat', [
            'conversations' => $conversations,
            'selected' => $selected,
        ])->layout('layouts.app', ['title' => 'Hubungi Admin — KostBandung.web.id']);
    }
}
