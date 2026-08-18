<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Verifikasi Akun — KostBandung')]
class VerifyAccount extends Component
{
    public string $phoneOtp = '';

    public int $otpCooldown = 0;

    public string $otpErrorMessage = '';

    public string $emailStatusMessage = '';

    public function mount(WhatsAppService $whatsapp): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        // If email is already verified, proceed directly
        if ($user->hasVerifiedEmail()) {
            $this->redirectAfterVerification($user);

            return;
        }

        // Auto-send email verification notification on first visit so user doesn't need to click "Kirim Ulang" manually
        $user->sendEmailVerificationNotification();

        // Check if OTP was recently sent and get remaining cooldown if any
        $rateLimitKey = "phone_otp_limit:{$user->id}";
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $this->otpCooldown = \Illuminate\Support\Facades\RateLimiter::availableIn($rateLimitKey);
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
            // Also verify email since user successfully proved ownership via verified phone OTP
            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            $this->dispatch('show-toast', message: 'Akun Anda berhasil diverifikasi!');

            $this->redirectAfterVerification($user);
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

    public function resendEmailVerification(): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectAfterVerification($user);

            return;
        }

        $user->sendEmailVerificationNotification();

        $this->emailStatusMessage = 'Tautan verifikasi baru telah berhasil dikirimkan ke email Anda.';
        $this->dispatch('show-toast', message: 'Email verifikasi berhasil dikirim ulang.');
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
