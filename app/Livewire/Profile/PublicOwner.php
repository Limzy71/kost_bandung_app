<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Profil Pemilik Kost — KostBandung.id')]
class PublicOwner extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        abort_if($user->role !== 'owner', 404);

        $this->user = $user;
    }

    public function render()
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
