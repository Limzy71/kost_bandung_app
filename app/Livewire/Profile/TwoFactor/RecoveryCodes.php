<?php

namespace App\Livewire\Profile\TwoFactor;

use Exception;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RecoveryCodes extends Component
{
    /** @var list<string> */
    #[Locked]
    public array $recoveryCodes = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->loadRecoveryCodes();
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generateNewRecoveryCodes): void
    {
        if (! $this->requirePasswordConfirmation()) {
            return;
        }

        $generateNewRecoveryCodes(auth()->user());

        $this->loadRecoveryCodes();

        $this->dispatch('show-toast', message: 'Kode pemulihan 2FA baru berhasil dibuat.');
    }

    /**
     * Ensure the user has recently confirmed their password before a
     * sensitive action runs. Redirects to the password confirmation page
     * and skips the action when the password has not been confirmed.
     */
    private function requirePasswordConfirmation(): bool
    {
        $confirmedAt = (int) session('auth.password_confirmed_at');

        if ($confirmedAt !== 0 && (time() - $confirmedAt) <= (int) config('auth.password_timeout', 10800)) {
            return true;
        }

        $this->redirect(route('password.confirm'));

        return false;
    }

    /**
     * Load the recovery codes for the user.
     */
    private function loadRecoveryCodes(): void
    {
        $user = auth()->user();

        if ($user->hasEnabledTwoFactorAuthentication() && $user->two_factor_recovery_codes) {
            try {
                $this->recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            } catch (Exception) {
                $this->addError('recoveryCodes', 'Gagal memuat kode pemulihan.');

                $this->recoveryCodes = [];
            }
        }
    }

    public function render()
    {
        return view('livewire.profile.two-factor.recovery-codes');
    }
}
