<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kost;
use App\Models\Inquiry;
use App\Models\Facility;
use Illuminate\Support\Facades\Auth;

class KostDetail extends Component
{
    public Kost $kost;

    public string $inquiry_name = '';
    public string $inquiry_phone = '';
    public string $inquiry_message = '';

    protected function rules()
    {
        return [
            'inquiry_name' => 'required|string|max:255',
            'inquiry_phone' => 'required|string|max:20',
            'inquiry_message' => 'required|string|max:1000',
        ];
    }

    protected $messages = [
        'inquiry_name.required' => 'Nama lengkap wajib diisi.',
        'inquiry_phone.required' => 'Nomor WhatsApp wajib diisi.',
        'inquiry_message.required' => 'Pesan tidak boleh kosong.',
    ];

    public string $backUrl = '';
    public string $backLabel = '';

    public function mount(Kost $kost)
    {
        $this->kost = $kost;
        
        if ($this->kost->status !== 'published') {
            abort_if(!auth()->check() || (auth()->user()->role !== 'admin' && auth()->id() !== $this->kost->user_id), 404);
        }

        $this->kost->load(['facilities', 'rules', 'images', 'user', 'prices']);

        // Determine back URL and label dynamically based on origin
        $previousUrl = url()->previous();
        $from = request('from');

        if ($from === 'moderation') {
            $this->backUrl = route('admin.moderation');
            $this->backLabel = 'Kembali ke Panel Moderasi';
        } elseif ($from === 'dashboard' || str_contains($previousUrl ?? '', '/dashboard')) {
            $this->backUrl = route('dashboard');
            $this->backLabel = 'Kembali ke Dashboard Pemilik';
        } elseif ($previousUrl && $previousUrl !== url()->current()) {
            $this->backUrl = $previousUrl;
            $this->backLabel = 'Kembali ke Beranda Utama';
        } else {
            $this->backUrl = route('home');
            $this->backLabel = 'Kembali ke Beranda Utama';
        }
    }

    public function removeFacility(int $facilityId)
    {
        if (auth()->id() !== $this->kost->user_id) {
            abort(403);
        }

        $facility = Facility::find($facilityId);

        if (! $facility) {
            return;
        }

        if ($facility->status !== 'pending' || $facility->user_id !== auth()->id()) {
            session()->flash('success', 'Fasilitas "' . $facility->name . '" tidak dapat dihapus.');
            return;
        }

        $facility->kosts()->detach();
        $facility->delete();

        $this->kost->load(['facilities', 'rules', 'images', 'user', 'prices']);

        session()->flash('success', 'Fasilitas "' . $facility->name . '" telah dihapus dari kost.');
    }

    public function sendInquiry()
    {
        $this->validate();

        $key = 'inquiry_' . request()->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            $this->addError('inquiry_message', 'TERLALU BANYAK MENGIRIM PESAN. TUNGGU ' . $seconds . ' DETIK.');
            return;
        }

        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

        Inquiry::create([
            'kost_id' => $this->kost->id,
            'user_id' => Auth::id(),
            'name' => $this->inquiry_name,
            'phone_number' => $this->inquiry_phone,
            'message' => $this->inquiry_message,
            'status' => 'unread',
        ]);

        $this->reset(['inquiry_message']);
        
        $this->dispatch('inquiry-sent');
        session()->flash('success', 'Pesan Anda berhasil dikirim ke pemilik kost!');
    }

    public function render()
    {
        return view('livewire.kost-detail', [
            'googleMapsApiKey' => config('services.google.maps_api_key'),
        ])->layout('layouts.app');
    }
}
