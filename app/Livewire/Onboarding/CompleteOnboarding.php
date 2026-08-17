<?php

namespace App\Livewire\Onboarding;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Lengkapi Akun Anda — KostBandung')]
#[Layout('layouts.auth')]
class CompleteOnboarding extends Component
{
    use ProfileValidationRules;

    public string $role = 'user';

    public string $business_name = '';

    public string $phone_number = '';

    public bool $terms = false;

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        if ($user->hasCompletedOnboarding()) {
            if ($user->role === 'owner') {
                $this->redirectRoute('dashboard', navigate: true);
            } else {
                $this->redirectRoute('home', navigate: true);
            }

            return;
        }
    }

    public function selectRole(string $role): void
    {
        if (in_array($role, ['user', 'owner'], true)) {
            $this->role = $role;
            $this->resetValidation();
        }
    }

    /**
     * Complete the onboarding process.
     */
    public function complete(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $rules = [
            'role' => ['required', Rule::in(['user', 'owner'])],
            'terms' => 'required|accepted',
        ];

        $messages = [
            'role.required' => 'Silakan pilih peran akun Anda.',
            'role.in' => 'Pilihan peran tidak valid.',
            'terms.required' => 'Anda wajib menyetujui Syarat & Ketentuan.',
            'terms.accepted' => 'Anda wajib menyetujui Syarat & Ketentuan.',
        ];

        if ($this->role === 'owner') {
            $rules['business_name'] = 'required|string|max:255';
            $rules['phone_number'] = [
                'required',
                'string',
                'regex:/^0[0-9]{9,14}$/',
                Rule::unique('users', 'phone_number')->ignore($user->id),
            ];

            $messages['business_name.required'] = 'Nama properti/usaha kost wajib diisi.';
            $messages['phone_number.required'] = 'Nomor WhatsApp wajib diisi.';
            $messages['phone_number.regex'] = 'Nomor WhatsApp harus berawalan 0 dan terdiri dari 10-15 digit angka.';
            $messages['phone_number.unique'] = 'Nomor WhatsApp sudah terdaftar di akun lain.';
        }

        $this->validate($rules, $messages);

        $user->role = $this->role;
        $user->terms_accepted_at = now();

        if ($this->role === 'owner') {
            $user->business_name = $this->business_name;
            $user->phone_number = $this->phone_number;
        }

        $user->save();

        if ($user->role === 'owner') {
            session()->flash('success', 'Selamat datang di KostBandung! Akun pemilik kost Anda telah aktif.');
            $this->redirectRoute('dashboard', navigate: true);

            return;
        }

        session()->flash('success', 'Selamat datang di KostBandung! Akun Anda telah siap digunakan.');
        $this->redirectRoute('home', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.onboarding.complete-onboarding');
    }
}
