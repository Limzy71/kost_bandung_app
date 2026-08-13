<?php

namespace App\Livewire;

use App\Models\Facility;
use App\Models\Kost;
use App\Models\KostConversation;
use App\Models\KostMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Component;

class KostDetail extends Component
{
    public Kost $kost;

    public bool $kostUnavailable = false;

    public string $message_name = '';

    public string $message_phone = '';

    public string $message_body = '';

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'message_name' => 'required|string|max:255',
            'message_phone' => 'required|string|max:20',
            'message_body' => 'required|string|max:1000',
        ];
    }

    /**
     * @var array<string, string>
     */
    protected array $messages = [
        'message_name.required' => 'Nama lengkap wajib diisi.',
        'message_phone.required' => 'Nomor WhatsApp wajib diisi.',
        'message_body.required' => 'Pesan tidak boleh kosong.',
    ];

    public string $backUrl = '';

    public string $backLabel = '';

    public function mount(Kost $kost): void
    {
        $this->kost = $kost;

        if ($this->kost->status !== 'published') {
            abort_if(! auth()->check() || (auth()->user()->role !== 'admin' && auth()->id() !== $this->kost->user_id), 404);
        }

        if (auth()->check()) {
            $this->message_name = auth()->user()->name;
            $this->message_phone = auth()->user()->phone_number ?? '';
        }

        $this->kost->load(['facilities', 'rules', 'images', 'user', 'prices']);

        // Determine back URL and label dynamically based on origin
        $previousUrl = url()->previous();
        $from = request('from');

        if ($from === 'moderation') {
            $this->backUrl = route('admin.moderation');
            $this->backLabel = 'Kembali ke Panel Moderasi';
        } elseif ($from === 'dashboard' || str_contains($previousUrl, '/dashboard')) {
            $this->backUrl = route('dashboard');
            $this->backLabel = 'Kembali ke Dashboard Pemilik';
        } else {
            $this->backUrl = route('home');
            $this->backLabel = 'Kembali ke Beranda Utama';
        }
    }

    #[Computed]
    public function existingConversation(): ?KostConversation
    {
        if (! auth()->check() || Auth::id() === $this->kost->user_id) {
            return null;
        }

        return KostConversation::where('kost_id', $this->kost->id)
            ->where('seeker_id', Auth::id())
            ->first();
    }

    public function removeFacility(int $facilityId): void
    {
        if (auth()->id() !== $this->kost->user_id) {
            abort(403);
        }

        $facility = Facility::find($facilityId);

        if (! $facility) {
            return;
        }

        if ($facility->status !== 'pending' || $facility->user_id !== auth()->id()) {
            session()->flash('success', 'Fasilitas "'.$facility->name.'" tidak dapat dihapus.');

            return;
        }

        $facility->kosts()->detach();
        $facility->delete();

        $this->kost->load(['facilities', 'rules', 'images', 'user', 'prices']);

        session()->flash('success', 'Fasilitas "'.$facility->name.'" telah dihapus dari kost.');
    }

    public function startChat(): void
    {
        // The $kost property is re-fetched fresh from the database on every
        // request (Livewire lazy proxy). If the owner deleted the kost after
        // this page was loaded, resolving it throws a ModelNotFoundException.
        try {
            $kost = $this->kost;
            $kostId = $kost->id;
            $kostStatus = $kost->status;
            $kostAvailable = $kost->is_available;
        } catch (ModelNotFoundException) {
            $this->kostUnavailable = true;
            $this->dispatch('kost-unavailable');

            return;
        }

        if ($kostStatus !== 'published') {
            $this->addError('message_body', 'Kost ini sedang tidak aktif dan tidak menerima pesan saat ini.');

            return;
        }

        if (! $kostAvailable) {
            $this->addError('message_body', 'Kost ini sedang PENUH dan tidak menerima pesan baru saat ini.');

            return;
        }

        if (auth()->guest()) {
            session()->put('url.intended', route('kost.show', $kost));
            $this->redirect(route('login'));

            return;
        }

        if (Auth::id() === $kost->user_id) {
            $this->addError('message_body', 'Anda tidak dapat mengirim pesan ke kost milik Anda sendiri.');

            return;
        }

        $this->validate();

        $key = 'kost_chat_start_'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('message_body', 'TERLALU BANYAK MENGIRIM PESAN. TUNGGU '.$seconds.' DETIK.');

            return;
        }

        RateLimiter::hit($key, 60);

        $conversation = KostConversation::firstOrCreate(
            ['kost_id' => $kostId, 'seeker_id' => Auth::id()],
            ['status' => KostConversation::STATUS_OPEN],
        );

        KostMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $this->message_body,
        ]);

        $conversation->touch();

        session()->flash('success', 'Pesan Anda berhasil dikirim ke pemilik kost!');

        $this->redirect(route('user.chats', ['conversation' => $conversation->id]));
    }

    public function render(): View
    {
        $title = 'Kost Tidak Tersedia - Kost Bandung';
        $meta = null;

        if (! $this->kostUnavailable) {
            try {
                $kost = $this->kost;
                $title = $kost->name.' - Kost Bandung';
                $meta = view('components.kost-meta', ['kost' => $kost]);
            } catch (ModelNotFoundException) {
                $this->kostUnavailable = true;
            }
        }

        return view('livewire.kost-detail', [
            'googleMapsApiKey' => config('services.google.maps_api_key'),
        ])->layout('layouts.app', [
            'title' => $title,
            'meta' => $meta,
        ]);
    }
}
