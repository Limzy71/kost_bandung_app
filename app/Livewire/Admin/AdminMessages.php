<?php

namespace App\Livewire\Admin;

use App\Models\AdminConversation;
use App\Models\AdminMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMessages extends Component
{
    use WithPagination;

    public string $filter = 'unanswered'; // unanswered | open | history

    public ?int $selectedConversationId = null;

    public string $replyBody = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403, 'Akses ditolak. Halaman ini khusus Administrator.');
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->selectedConversationId = null;
        $this->resetPage();
    }

    public function openConversation(int $id): void
    {
        $conversation = AdminConversation::find($id);

        if (! $conversation) {
            abort(404);
        }

        $this->selectedConversationId = $conversation->id;

        // Tandai pesan user yang belum dibaca sebagai sudah dibaca.
        AdminMessage::where('conversation_id', $conversation->id)
            ->where('sender_type', 'user')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function replyConversation(): void
    {
        AdminConversation::expireStale();
        AdminConversation::pruneSoftDeleted();

        $conversation = $this->findConversation($this->selectedConversationId);

        if (! $conversation) {
            abort(404);
        }

        if (! $conversation->isOpen()) {
            $this->addError('replyBody', 'Percakapan sudah ditutup dan tidak dapat dibalas lagi.');

            return;
        }

        $this->validate([
            'replyBody' => 'required|string|max:2000',
        ]);

        AdminMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'sender_id' => Auth::id(),
            'body' => $this->replyBody,
        ]);

        $conversation->update([
            'awaiting_reply_at' => null,
            'status' => 'open',
        ]);

        $this->reset('replyBody');

        $this->dispatch('show-toast', message: 'Balasan berhasil dikirim ke pengguna.');
    }

    public function closeConversation(int $id): void
    {
        $conversation = $this->findConversation($id);

        if (! $conversation) {
            abort(404);
        }

        $conversation->update([
            'status' => 'closed',
            'closed_reason' => 'admin',
            'closed_at' => now(),
            'awaiting_reply_at' => null,
        ]);

        $this->selectedConversationId = null;

        $this->dispatch('show-toast', message: 'Percakapan ditutup dan masuk ke riwayat pengguna.');
    }

    public function deleteConversation(int $id): void
    {
        $conversation = $this->findConversation($id);

        if (! $conversation) {
            abort(404);
        }

        if ($conversation->isOpen()) {
            $this->addError('replyBody', 'Percakapan masih aktif. Tutup terlebih dahulu sebelum menghapus.');

            return;
        }

        $conversation->delete(); // soft delete

        if ($this->selectedConversationId === $conversation->id) {
            $this->selectedConversationId = null;
        }

        $this->dispatch('show-toast', message: 'Percakapan dihapus. Riwayat tersimpan 30 hari sebelum dibersihkan otomatis.');
    }

    protected function findConversation(int $id): ?AdminConversation
    {
        return AdminConversation::find($id);
    }

    public function render(): View
    {
        AdminConversation::expireStale();
        AdminConversation::pruneSoftDeleted();

        $query = AdminConversation::with(['user', 'latestMessage'])->orderBy('created_at', 'desc');

        if ($this->filter === 'unanswered') {
            $query->where('status', 'open')->whereNotNull('awaiting_reply_at');
        } elseif ($this->filter === 'open') {
            $query->where('status', 'open');
        } else {
            $query->where('status', 'closed');
        }

        $conversations = $query->paginate(15);

        $selected = null;

        if ($this->selectedConversationId) {
            $selected = AdminConversation::with(['messages.sender', 'user'])->find($this->selectedConversationId);
        }

        $counts = [
            'unanswered' => AdminConversation::where('status', 'open')->whereNotNull('awaiting_reply_at')->count(),
            'open' => AdminConversation::where('status', 'open')->count(),
            'history' => AdminConversation::where('status', 'closed')->count(),
        ];

        return view('livewire.admin.admin-messages', [
            'conversations' => $conversations,
            'selected' => $selected,
            'counts' => $counts,
        ])->layout('layouts.app', ['title' => 'Inbox Bantuan Admin — KostBandung.web.id']);
    }
}
