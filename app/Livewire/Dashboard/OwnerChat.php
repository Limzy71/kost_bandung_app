<?php

namespace App\Livewire\Dashboard;

use App\Events\KostMessageSent;
use App\Models\KostConversation;
use App\Models\KostMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Url;
use Livewire\Component;

class OwnerChat extends Component
{
    #[Url(as: 'conversation', history: true)]
    public ?int $conversation = null;

    public ?int $selectedConversationId = null;

    public string $newBody = '';

    public function mount(): void
    {
        if ($this->conversation) {
            $this->openConversation($this->conversation);
        }
    }

    public function openConversation(int $id): void
    {
        $conversation = $this->findOwnedConversation($id);

        if (! $conversation) {
            abort(403);
        }

        $this->selectedConversationId = $conversation->id;
        $this->conversation = $conversation->id;
        $this->markRead($conversation);
    }

    public function markSelectedRead(): void
    {
        if (! $this->selectedConversationId) {
            return;
        }

        $conversation = $this->findOwnedConversation($this->selectedConversationId);

        if ($conversation) {
            $this->markRead($conversation);
        }
    }

    public function handleIncomingMessage(array $payload): void
    {
        $payload = (array) $payload;
        $conversationId = (int) ($payload['conversation_id'] ?? 0);

        if ($this->selectedConversationId === $conversationId) {
            $this->markSelectedRead();
        } else {
            $this->dispatchBadgeUpdate();
        }

        $this->dispatch('$refresh');
    }

    protected function getListeners(): array
    {
        return [
            'echo-private:App.Models.User.'.auth()->id().',.kost.message.sent' => 'handleIncomingMessage',
        ];
    }

    public function sendMessage(): void
    {
        $conversation = $this->findOwnedConversation($this->selectedConversationId);

        if (! $conversation) {
            abort(403);
        }

        $this->validate([
            'newBody' => 'required|string|max:2000',
        ], [
            'newBody.required' => 'Pesan tidak boleh kosong.',
            'newBody.max' => 'Pesan maksimal 2000 karakter.',
        ]);

        $key = 'kost_chat_'.Auth::id();

        if (RateLimiter::tooManyAttempts($key, 20)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('newBody', 'Terlalu banyak mengirim pesan. Coba lagi dalam '.$seconds.' detik.');

            return;
        }

        $message = KostMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $this->newBody,
        ]);

        broadcast(new KostMessageSent($message, (int) $conversation->seeker_id));

        RateLimiter::hit($key, 60);

        $conversation->forceFill(['status' => KostConversation::STATUS_OPEN])->save();

        $this->reset('newBody');

        $this->dispatchBadgeUpdate();
    }

    public function toggleArchive(int $id): void
    {
        $conversation = $this->findOwnedConversation($id);

        if (! $conversation) {
            abort(403);
        }

        $conversation->update([
            'status' => $conversation->isHiddenForOwner()
                ? KostConversation::STATUS_OPEN
                : KostConversation::STATUS_ARCHIVED_BY_OWNER,
        ]);

        if ($this->selectedConversationId === $conversation->id) {
            $this->selectedConversationId = null;
            $this->conversation = null;
        }

        $this->dispatchBadgeUpdate();
    }

    protected function markRead(KostConversation $conversation): void
    {
        KostMessage::where('conversation_id', $conversation->id)
            ->whereNull('read_at')
            ->where(function ($q) {
                $q->whereNull('sender_id')
                    ->orWhere('sender_id', '!=', Auth::id());
            })
            ->update(['read_at' => now()]);

        $this->dispatchBadgeUpdate();
    }

    protected function findOwnedConversation(int $id): ?KostConversation
    {
        return KostConversation::whereHas('kost', fn ($q) => $q->where('user_id', Auth::id()))
            ->find($id);
    }

    protected function dispatchBadgeUpdate(): void
    {
        $count = KostMessage::whereNull('read_at')
            ->whereHas('conversation', function ($q) {
                $q->whereHas('kost', fn ($k) => $k->where('user_id', Auth::id()));
            })
            ->count();

        $this->dispatch('kost-chats-updated', count: $count);
    }

    public function render(): View
    {
        $conversations = KostConversation::with(['kost', 'seeker', 'latestMessage'])
            ->whereHas('kost', fn ($q) => $q->where('user_id', Auth::id()))
            ->where('status', '!=', KostConversation::STATUS_ARCHIVED_BY_OWNER)
            ->latest('updated_at')
            ->get();

        $unreadCounts = KostMessage::query()
            ->whereNull('read_at')
            ->whereHas('conversation', fn ($q) => $q->whereHas('kost', fn ($k) => $k->where('user_id', Auth::id())))
            ->where(function ($q) {
                $q->whereNull('sender_id')->orWhere('sender_id', '!=', Auth::id());
            })
            ->selectRaw('conversation_id, count(*) as total')
            ->groupBy('conversation_id')
            ->pluck('total', 'conversation_id');

        $selected = null;

        if ($this->selectedConversationId) {
            $selected = $this->findOwnedConversation($this->selectedConversationId);

            if ($selected) {
                $selected->load(['kost', 'seeker', 'messages.sender']);
            }
        }

        return view('livewire.dashboard.owner-chat', [
            'conversations' => $conversations,
            'unreadCounts' => $unreadCounts,
            'selected' => $selected,
        ])->layout('layouts.app', ['title' => 'Obrolan Kost — KostBandung']);
    }
}
