<?php

namespace App\Livewire\Profile;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Livewire\Actions\Logout;
use App\Models\Facility;
use App\Models\Kost;
use App\Models\KostMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Profil Saya — KostBandung.web.id')]
class Index extends Component
{
    use PasswordValidationRules, ProfileValidationRules, WithFileUploads;

    public string $name = '';

    public string $email = '';

    public string $phone_number = '';

    public string $business_name = '';

    public bool $editing = false;

    public mixed $avatarUpload = null;

    public mixed $identity_doc = null;

    public string $deletePassword = '';

    public bool $deleteAccountModalOpen = false;

    /**
     * @var array<string, string>
     */
    protected array $messages = [
        'name.required' => 'Nama lengkap wajib diisi.',
        'name.min' => 'Nama minimal 2 karakter.',
        'name.max' => 'Nama maksimal 50 karakter.',
        'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, atau titik (.).',
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

        $this->name = Str::squish($this->name);

        $rules = $this->profileRules($user->id);

        if ($user->role === 'admin') {
            unset($rules['email']);
            $this->email = $user->email;
        }

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

        if ($user->wasChanged('email')) {
            $user->sendEmailVerificationNotification();
        }

        $this->editing = false;

        $this->dispatch('show-toast', message: 'Profil Anda berhasil diperbarui.');
    }

    public function updatedAvatarUpload(): void
    {
        $this->validate([
            'avatarUpload' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'avatarUpload.image' => 'File harus berupa gambar.',
            'avatarUpload.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'avatarUpload.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($this->avatarUpload) {
            $user = $this->currentUser();
            $oldAvatar = $user->avatar;

            $path = $this->avatarUpload->store('avatars', config('filesystems.default'));
            $user->update(['avatar' => $path]);

            if ($oldAvatar) {
                Storage::disk(config('filesystems.default'))->delete($oldAvatar);
            }

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

    public function updatedIdentityDoc(): void
    {
        $user = $this->currentUser();

        if ($user->role !== 'owner') {
            $this->identity_doc = null;

            return;
        }

        $this->validate([
            'identity_doc' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'identity_doc.image' => 'File KTP harus berupa gambar.',
            'identity_doc.mimes' => 'File KTP harus berformat JPG, PNG, atau WEBP.',
            'identity_doc.max' => 'Ukuran foto KTP tidak boleh melebihi 2MB.',
        ]);

        if ($this->identity_doc) {
            $path = $this->identity_doc->store('verification-docs/identity', 'verification_docs');

            $user->deleteIdentityDocumentFile();

            $user->forceFill([
                'identity_doc_path' => $path,
                'identity_verification_status' => 'pending',
                'identity_verified_at' => null,
                'identity_rejection_note' => null,
            ])->save();

            $this->identity_doc = null;
            $this->dispatch('show-toast', message: 'Dokumen KTP berhasil diunggah dan sedang ditinjau admin.');
        }
    }

    public function deleteIdentityDocument(): void
    {
        $user = $this->currentUser();

        if ($user->role !== 'owner' || ! $user->identity_doc_path) {
            return;
        }

        $user->deleteIdentityDocumentFile();

        $user->forceFill([
            'identity_doc_path' => null,
            'identity_verification_status' => 'unverified',
            'identity_verified_at' => null,
            'identity_rejection_note' => null,
        ])->save();

        $this->dispatch('show-toast', message: 'Dokumen KTP berhasil dihapus. Anda dapat mengunggah ulang kapan saja.');
    }

    /**
     * Permanently delete the current account along with every uploaded file.
     */
    public function deleteAccount(Logout $logout): void
    {
        $user = $this->currentUser();

        if ($user->role === 'admin') {
            $this->deleteAccountModalOpen = false;

            return;
        }

        $this->validate([
            'deletePassword' => $this->currentPasswordRules(),
        ], [
            'deletePassword.required' => 'Password wajib diisi untuk menghapus akun.',
            'deletePassword.current_password' => 'Password yang Anda masukkan salah.',
        ]);

        $user->purgeAllDataFiles();

        tap($user, $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }

    #[Computed]
    public function showDeleteAccount(): bool
    {
        return $this->currentUser()->role !== 'admin';
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
                'pesanMasuk' => KostMessage::whereNull('read_at')
                    ->where(function ($q) use ($user) {
                        $q->whereNull('sender_id')->orWhere('sender_id', '!=', $user->id);
                    })
                    ->whereHas('conversation', fn ($query) => $query->whereHas('kost', fn ($k) => $k->where('user_id', $user->id)))
                    ->count(),
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
                'totalChats' => $user->kostConversations()->count(),
                'unreadChats' => KostMessage::whereNull('read_at')
                    ->where(function ($q) use ($user) {
                        $q->whereNull('sender_id')->orWhere('sender_id', '!=', $user->id);
                    })
                    ->whereHas('conversation', fn ($query) => $query->where('seeker_id', $user->id))
                    ->count(),
                'chats' => $user->kostConversations()->with(['kost', 'latestMessage'])->latest('updated_at')->get(),
            ],
        };
    }
}
