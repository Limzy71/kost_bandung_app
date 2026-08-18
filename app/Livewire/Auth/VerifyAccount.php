<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Verifikasi Akun — KostBandung')]
class VerifyAccount extends Component
{
    public string $phoneOtp = '';

    public int $otpCooldown = 0;

    public string $otpErrorMessage = '';

    public string $emailStatusMessage = '';

    public string $phoneSuccessMessage = '';

    public bool $isEditingPhone = false;

    public string $newPhoneNumber = '';

    public string $phoneErrorMessage = '';

    public function mount(WhatsAppService $whatsapp): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        // If both email and phone number are already verified, proceed directly
        if ($user->isFullyVerified()) {
            $this->redirectAfterVerification($user);

            return;
        }

        // Pre-fill edit phone number field
        $this->newPhoneNumber = $user->phone_number ?? '';

        // Auto-send email notification if not yet verified
        if (! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        // Check if OTP was recently sent and calculate remaining cooldown
        $rateLimitKey = "phone_otp_limit:{$user->id}";
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $this->otpCooldown = RateLimiter::availableIn($rateLimitKey);
        }
    }

    public function verifyPhoneOtp(WhatsAppService $whatsapp): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->validate([
            'phoneOtp' => 'required|digits:6',
        ], [
            'phoneOtp.required' => 'Kode OTP wajib diisi.',
            'phoneOtp.digits' => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $result = $whatsapp->verifyOtp($user, $this->phoneOtp);

        if ($result['success']) {
            $user->refresh();
            $this->phoneOtp = '';
            $this->otpErrorMessage = '';
            $this->phoneSuccessMessage = 'Nomor WhatsApp berhasil diverifikasi!';

            if ($user->isFullyVerified()) {
                $this->dispatch('show-toast', message: 'Selamat! Akun Anda telah aktif dan terverifikasi.');
                $this->redirectAfterVerification($user);
            } else {
                $this->dispatch('show-toast', message: 'Nomor WhatsApp terverifikasi. Silakan konfirmasi tautan email Anda.');
            }
        } else {
            $this->otpErrorMessage = $result['message'];
        }
    }

    public function resendPhoneOtp(WhatsAppService $whatsapp): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (empty($user->phone_number)) {
            $this->otpErrorMessage = 'Nomor WhatsApp belum terdaftar.';

            return;
        }

        $result = $whatsapp->sendOtp($user);

        if ($result['success']) {
            $this->otpCooldown = $result['cooldown'] ?? 60;
            $this->otpErrorMessage = '';
            $this->dispatch('show-toast', message: $result['message']);
        } else {
            $this->otpErrorMessage = $result['message'];
            if (isset($result['cooldown']) && $result['cooldown'] > 0) {
                $this->otpCooldown = $result['cooldown'];
            }
        }
    }

    public function toggleEditPhone(): void
    {
        $this->isEditingPhone = ! $this->isEditingPhone;
        $this->phoneErrorMessage = '';
        if ($this->isEditingPhone) {
            $this->newPhoneNumber = Auth::user()->phone_number ?? '';
        }
    }

    public function updatePhoneNumber(WhatsAppService $whatsapp): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->validate([
            'newPhoneNumber' => ['required', 'regex:/^08[0-9]{8,13}$/', 'unique:users,phone_number,'.$user->id],
        ], [
            'newPhoneNumber.required' => 'Nomor WhatsApp wajib diisi.',
            'newPhoneNumber.regex' => 'Format nomor tidak valid. Gunakan awalan 08 (10-15 digit).',
            'newPhoneNumber.unique' => 'Nomor WhatsApp ini sudah digunakan oleh akun lain.',
        ]);

        $user->phone_number = $this->newPhoneNumber;
        $user->phone_verified_at = null;
        $user->save();

        // Clear previous limit so new number gets fresh OTP immediately
        RateLimiter::clear("phone_otp_limit:{$user->id}");

        $result = $whatsapp->sendOtp($user);

        $this->isEditingPhone = false;
        $this->phoneSuccessMessage = 'Nomor WhatsApp berhasil diubah. Kode OTP baru telah dikirimkan ke nomor baru Anda.';
        $this->otpCooldown = $result['cooldown'] ?? 60;
        $this->dispatch('show-toast', message: 'Nomor diperbarui & OTP baru dikirim.');
    }

    public function resendEmailVerification(): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            if ($user->isFullyVerified()) {
                $this->redirectAfterVerification($user);

                return;
            }
        } else {
            $user->sendEmailVerificationNotification();
        }

        $this->emailStatusMessage = 'Tautan verifikasi baru telah berhasil dikirimkan ke email Anda.';
        $this->dispatch('show-toast', message: 'Email verifikasi berhasil dikirim ulang.');
    }

    public function checkVerificationStatus(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user && $user->isFullyVerified()) {
            $this->redirectAfterVerification($user);
        }
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirectRoute('login');
    }

    private function redirectAfterVerification(User $user): void
    {
        if ($user->role === 'owner') {
            $this->redirectIntended(route('dashboard'), navigate: true);

            return;
        }

        if ($user->role === 'admin') {
            $this->redirectIntended(route('admin.moderation'), navigate: true);

            return;
        }

        $this->redirectIntended(route('home'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.verify-account', [
            'user' => Auth::user(),
        ])->layout('layouts.auth');
    }
}
