<?php

namespace App\Livewire\Profile;

use App\Concerns\ProfileValidationRules;
use App\Models\Facility;
use App\Models\Inquiry;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Profil Saya — KostBandung.web.id')]
class Index extends Component
{
    use ProfileValidationRules, WithFileUploads;

    public string $name = '';

    public string $email = '';

    public string $phone_number = '';

    public string $business_name = '';

    public bool $editing = false;

    public mixed $avatarUpload = null;

    /**
     * @var array<string, string>
     */
    protected array $messages = [
        'name.required' => 'Nama lengkap wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah terdaftar.',
        'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
        'phone_number.min' => 'Nomor WhatsApp minimal 10 digit.',
        'phone_number.max' => 'Nomor WhatsApp maksimal 15 digit.',
        'business_name.required' => 'Nama properti/usaha kost wajib diisi.',
    ];

    public function mount(): void
    {
        $user = $this->currentUser();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number ?? '';
        $this->business_name = $user->business_name ?? '';
    }

    public function toggleEdit(): void
    {
        $this->editing = ! $this->editing;

        if ($this->editing) {
            $this->mount();
        }
    }

    public function updateProfile(): void
    {
        $user = $this->currentUser();

        $rules = $this->profileRules($user->id);

        if ($user->role === 'owner') {
            $rules['phone_number'] = $this->phoneNumberRules(required: true);
            $rules['business_name'] = $this->businessNameRules(required: true);
        } else {
            $rules['phone_number'] = $this->phoneNumberRules();
        }

        $validated = $this->validate($rules);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->editing = false;

        $this->dispatch('show-toast', message: 'Profil Anda berhasil diperbarui.');
    }

    public function updatedAvatarUpload(): void
    {
        $this->validate([
            'avatarUpload' => 'nullable|image|max:2048',
        ], [
            'avatarUpload.image' => 'File harus berupa gambar (JPG, PNG, WEBP).',
            'avatarUpload.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($this->avatarUpload) {
            $user = $this->currentUser();
            $user->deleteAvatarFile();

            $path = $this->avatarUpload->store('avatars', config('filesystems.default'));
            $user->update(['avatar' => $path]);

            $this->avatarUpload = null;
            $this->dispatch('show-toast', message: 'Foto profil Anda berhasil diperbarui.');
        }
    }

    public function deleteAvatar(): void
    {
        $user = $this->currentUser();

        if ($user->avatar) {
            $user->deleteAvatarFile();
            $user->update(['avatar' => null]);
            $this->dispatch('show-toast', message: 'Foto profil Anda berhasil dihapus.');
        }
    }

    public function render(): View
    {
        $user = $this->currentUser();

        return view('livewire.profile.index', [
            'user' => $user,
            'stats' => $this->statsFor($user),
        ])->layout('layouts.app', [
            'title' => 'Profil Saya — KostBandung.web.id',
        ]);
    }

    private function currentUser(): User
    {
        return Auth::user();
    }

    /**
     * @return array<string, mixed>
     */
    private function statsFor(User $user): array
    {
        return match ($user->role) {
            'owner' => [
                'totalKosts' => $user->kosts()->count(),
                'availableKosts' => $user->kosts()->where('is_available', true)->count(),
                'pendingKosts' => $user->kosts()->where('status', 'pending')->count(),
                'inquiries' => Inquiry::whereHas('kost', fn ($query) => $query->where('user_id', $user->id))->count(),
                'kosts' => $user->kosts()->with('primaryImage')->latest()->get(),
            ],
            'admin' => [
                'pendingKosts' => Kost::where('status', 'pending')->count(),
                'publishedKosts' => Kost::where('status', 'published')->count(),
                'rejectedKosts' => Kost::where('status', 'rejected')->count(),
                'totalUsers' => User::where('role', 'user')->count(),
                'totalOwners' => User::where('role', 'owner')->count(),
                'pendingFacilities' => Facility::where('status', 'pending')->count(),
            ],
            default => [
                'totalInquiries' => $user->inquiries()->count(),
                'unreadInquiries' => $user->inquiries()->where('status', 'unread')->count(),
                'inquiries' => $user->inquiries()->with('kost')->latest()->get(),
            ],
        };
    }
}
