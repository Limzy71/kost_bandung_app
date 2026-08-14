<?php

namespace App\Livewire;

use App\Models\AdminConversation;
use App\Models\AdminMessage;
use App\Models\KostMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NavbarBadges extends Component
{
    public function refresh(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $role = $user->role;

        $adminReplies = AdminMessage::where('sender_type', 'admin')
            ->whereNull('read_at')
            ->whereHas('conversation', fn ($q) => $q->where('user_id', $user->id))
            ->count();

        $adminUnanswered = $role === 'admin'
            ? AdminConversation::where('status', 'open')->whereNotNull('awaiting_reply_at')->count()
            : 0;

        $chat = in_array($role, ['owner', 'user'], true)
            ? KostMessage::whereNull('read_at')
                ->where(function ($q) use ($user) {
                    $q->whereNull('sender_id')->orWhere('sender_id', '!=', $user->id);
                })
                ->whereHas('conversation', function ($q) use ($user, $role) {
                    if ($role === 'owner') {
                        $q->whereHas('kost', fn ($k) => $k->where('user_id', $user->id));
                    } else {
                        $q->where('seeker_id', $user->id);
                    }
                })
                ->count()
            : 0;

        $this->dispatch('badges-updated', chat: $chat, adminReplies: $adminReplies, adminUnanswered: $adminUnanswered);
    }

    protected function getListeners(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $listeners = [
            'echo-private:App.Models.User.'.$user->id.',.admin.message.sent' => 'refresh',
            'echo-private:App.Models.User.'.$user->id.',.admin.conversation.closed' => 'refresh',
            'echo-private:App.Models.User.'.$user->id.',.kost.message.sent' => 'refresh',
            'refresh-navbar-badges' => 'refresh',
        ];

        if ($user->role === 'admin') {
            $listeners['echo-private:admin.inbox,.admin.message.sent'] = 'refresh';
        }

        return $listeners;
    }

    public function render(): View
    {
        return view('livewire.navbar-badges');
    }
}
