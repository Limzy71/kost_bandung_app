<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kost;

class KostDetail extends Component
{
    public Kost $kost;

    public function mount(Kost $kost)
    {
        $this->kost = $kost;
        $this->kost->load(['facilities', 'rules', 'images', 'owner']);
    }

    public function render()
    {
        return view('livewire.kost-detail')->layout('layouts.app');
    }
}
