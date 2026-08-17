<?php

namespace App\Livewire\Onboarding;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Lengkapi Akun Anda — KostBandung')]
#[Layout('layouts.auth')]
class CompleteOnboarding extends Component
{
    // Public properties kept for test compatibility via Livewire::set().
    // In the browser, these are NOT bound to the form (no wire:model / @entangle).
    // The browser sends data via $wire.complete(...params) directly.
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

    /**
     * Complete the onboarding process.
     *
     * When called from the browser (Alpine), all four parameters are passed directly
     * to avoid intermediate Livewire re-renders (no wire:model / @entangle in the blade).
     *
     * When called from tests via Livewire::call('complete'), no parameters are supplied
     * so the method falls back to reading the public properties set via Livewire::set().
     */
    public function complete(
        ?string $role = null,
        ?string $business_name = null,
        ?string $phone_number = null,
        ?bool $terms = null,
    ): void {
        // Use passed params when called from Alpine, fall back to $this for tests.
        $role          = $role          ?? $this->role;
        $business_name = $business_name ?? $this->business_name;
        $phone_number  = $phone_number  ?? $this->phone_number;
        $terms         = $terms         ?? $this->terms;

        /** @var User $user */
        $user = Auth::user();

        $rules = [
            'role'  => ['required', Rule::in(['user', 'owner'])],
            'terms' => 'required|accepted',
        ];

        $messages = [
            'role.required'  => 'Silakan pilih peran akun Anda.',
            'role.in'        => 'Pilihan peran tidak valid.',
            'terms.required' => 'Anda wajib menyetujui Syarat & Ketentuan.',
            'terms.accepted' => 'Anda wajib menyetujui Syarat & Ketentuan.',
        ];

        if ($role === 'owner') {
            $rules['business_name'] = 'required|string|max:255';
            $rules['phone_number']  = [
                'required',
                'string',
                'regex:/^0[0-9]{9,14}$/',
                Rule::unique('users', 'phone_number')->ignore($user->id),
            ];

            $messages['business_name.required'] = 'Nama properti/usaha kost wajib diisi.';
            $messages['phone_number.required']  = 'Nomor WhatsApp wajib diisi.';
            $messages['phone_number.regex']     = 'Nomor WhatsApp harus berawalan 0 dan terdiri dari 10-15 digit angka.';
            $messages['phone_number.unique']    = 'Nomor WhatsApp sudah terdaftar di akun lain.';
        }

        $data = [
            'role'          => $role,
            'business_name' => $business_name,
            'phone_number'  => $phone_number,
            'terms'         => $terms,
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $user->role             = $role;
        $user->terms_accepted_at = now();

        if ($role === 'owner') {
            $user->business_name = $business_name;
            $user->phone_number  = $phone_number;
        }

        $user->save();

        if ($role === 'owner') {
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
