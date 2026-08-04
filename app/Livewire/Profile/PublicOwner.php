<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Profil Pemilik Kost — KostBandung.id')]
class PublicOwner extends Component
{
    public User $user;

    public string $backUrl = '';

    public string $backLabel = '';

    public function mount(User $user): void
    {
        abort_if($user->role !== 'owner', 404);

        $this->user = $user;

        $from = request('from');
        $kostSlug = request('kost');

        if ($from === 'kost' && $kostSlug) {
            $this->backUrl = route('kost.show', $kostSlug);
            $this->backLabel = 'Kembali ke Detail Kost';
        } else {
            $this->backUrl = route('home');
            $this->backLabel = 'Kembali ke Beranda Utama';
        }
    }

    public function render(): View
    {
        return view('livewire.profile.public-owner', [
            'user' => $this->user,
            'kosts' => $this->user->kosts()
                ->with('primaryImage')
                ->where('status', 'published')
                ->latest()
                ->get(),
            'totalKosts' => $this->user->kosts()->count(),
            'availableKosts' => $this->user->kosts()
                ->where('status', 'published')
                ->where('is_available', true)
                ->count(),
        ])->layout('layouts.app', [
            'title' => 'Profil Pemilik Kost — KostBandung.id',
        ]);
    }
}
