<?php

namespace App\Livewire\Profile;

use App\Concerns\PasswordValidationRules;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Passkeys\Actions\DeletePasskey;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Security extends Component
{
    use PasswordValidationRules;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    #[Locked]
    public bool $canManageTwoFactor;

    #[Locked]
    public bool $twoFactorEnabled;

    #[Locked]
    public bool $requiresConfirmation;

    #[Locked]
    public string $qrCodeSvg = '';

    #[Locked]
    public string $manualSetupKey = '';

    public bool $showModal = false;

    public bool $showVerificationStep = false;

    public bool $showRecoveryStep = false;

    #[Validate('required|digits:6', onUpdate: false)]
    public string $code = '';

    /**
     * @var list<string>
     */
    #[Locked]
    public array $recoveryCodes = [];

    #[Locked]
    public bool $canManagePasskeys;

    /**
     * @var array<int, array{id: int, name: string, authenticator: string|null, created_at_diff: string, last_used_at_diff: string|null}>
     */
    #[Locked]
    public array $passkeys = [];

    public bool $showDeleteModal = false;

    #[Locked]
    public ?int $deletingPasskeyId = null;

    #[Locked]
    public string $deletingPasskeyName = '';

    /**
     * Mount the component.
     */
    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $this->canManageTwoFactor = Features::canManageTwoFactorAuthentication();

        if ($this->canManageTwoFactor) {
            if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
                $disableTwoFactorAuthentication(auth()->user());
            }

            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
            $this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
        }

        $this->canManagePasskeys = Features::canManagePasskeys();

        if ($this->canManagePasskeys) {
            $this->loadPasskeys();
        }
    }

    /**
     * Whether the current user has a password set (not OAuth-only).
     */
    #[Computed]
    public function hasPassword(): bool
    {
        return auth()->user()->password !== null;
    }

    /**
     * Update the password for the currently authenticated user.
     *
     * OAuth-only users (no password) can set a new password directly
     * without confirming the current one.
     */
    public function updatePassword(): void
    {
        $user = Auth::user();
        $wasOAuthOnly = $user->password === null;

        if (! $wasOAuthOnly && ! $this->requirePasswordConfirmation()) {
            return;
        }

        $rules = ['password' => $this->passwordRules()];

        if (! $wasOAuthOnly) {
            $rules['current_password'] = $this->currentPasswordRules();
        }

        $messages = [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.max' => 'Kata sandi maksimal 32 karakter.',
            'password.letters' => 'Kata sandi harus mengandung huruf.',
            'password.numbers' => 'Kata sandi harus mengandung angka.',
            'password.mixed' => 'Kata sandi harus mengandung huruf besar dan huruf kecil.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
        ];

        try {
            $validated = $this->validate($rules, $messages);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        $user->update([
            'password' => $validated['password'],
        ]);

        // Regenerate session ID after password change for security
        session()->regenerate();

        $this->reset('current_password', 'password', 'password_confirmation');

        $message = $wasOAuthOnly
            ? 'Kata sandi berhasil dibuat. Anda sekarang bisa login dengan email dan kata sandi.'
            : 'Kata sandi berhasil diperbarui.';

        $this->dispatch('show-toast', message: $message);
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

        session()->put('url.intended', route('profile.show'));

        $this->redirect(route('password.confirm'));

        return false;
    }

    /**
     * Load the user's passkeys.
     */
    public function loadPasskeys(): void
    {
        $this->passkeys = Auth::user()->passkeys()
            ->select(['id', 'name', 'credential', 'created_at', 'last_used_at'])
            ->latest()
            ->get()
            ->map(fn ($passkey) => [
                'id' => $passkey->id,
                'name' => $passkey->name,
                'authenticator' => $passkey->authenticator,
                'created_at_diff' => $passkey->created_at->diffForHumans(),
                'last_used_at_diff' => $passkey->last_used_at?->diffForHumans(),
            ])
            ->all();
    }

    /**
     * Show the delete confirmation modal.
     */
    public function confirmDelete(int $passkeyId): void
    {
        $passkey = Auth::user()->passkeys()->findOrFail($passkeyId);

        $this->deletingPasskeyId = $passkey->id;
        $this->deletingPasskeyName = $passkey->name;
        $this->showDeleteModal = true;
    }

    /**
     * Delete the passkey.
     */
    public function deletePasskey(DeletePasskey $deletePasskey): void
    {
        if (! $this->requirePasswordConfirmation()) {
            return;
        }

        if (! $this->deletingPasskeyId) {
            return;
        }

        $user = Auth::user();
        $passkey = $user->passkeys()->findOrFail($this->deletingPasskeyId);

        $deletePasskey($user, $passkey);

        $this->closeDeleteModal();
        $this->loadPasskeys();

        $this->dispatch('show-toast', message: 'Passkey berhasil dihapus.');
    }

    /**
     * Close the delete confirmation modal.
     */
    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingPasskeyId = null;
        $this->deletingPasskeyName = '';
    }

    /**
     * Enable two-factor authentication for the user.
     */
    public function enable(EnableTwoFactorAuthentication $enableTwoFactorAuthentication): void
    {
        if (! $this->requirePasswordConfirmation()) {
            return;
        }

        $enableTwoFactorAuthentication(auth()->user());

        if (! $this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }

        $this->loadSetupData();

        $this->showModal = true;
    }

    /**
     * Load the two-factor authentication setup data for the user.
     */
    private function loadSetupData(): void
    {
        $user = auth()->user();

        try {
            $this->qrCodeSvg = $user?->twoFactorQrCodeSvg();
            $this->manualSetupKey = decrypt($user->two_factor_secret);
        } catch (Exception) {
            $this->addError('setupData', 'Gagal mengambil data setup 2FA.');

            $this->reset('qrCodeSvg', 'manualSetupKey');
        }
    }

    /**
     * Load the two-factor recovery codes for the user.
     */
    private function loadRecoveryCodes(): void
    {
        $user = auth()->user();

        if ($user?->hasEnabledTwoFactorAuthentication() && $user->two_factor_recovery_codes) {
            try {
                $this->recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            } catch (Exception) {
                $this->addError('code', 'Gagal memuat kode pemulihan.');

                $this->recoveryCodes = [];
            }
        }
    }

    /**
     * Show the two-factor verification step if necessary.
     */
    public function showVerificationIfNecessary(): void
    {
        if ($this->requiresConfirmation) {
            $this->showVerificationStep = true;

            $this->resetErrorBag();

            return;
        }

        $this->closeModal();
    }

    /**
     * Confirm two-factor authentication for the user.
     */
    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): void
    {
        $this->validate(rules: null, messages: [
            'code.required' => 'Kode 2FA wajib diisi.',
            'code.digits' => 'Kode 2FA harus terdiri dari 6 digit angka.',
        ]);

        try {
            $confirmTwoFactorAuthentication(auth()->user(), $this->code);
        } catch (ValidationException) {
            $this->addError('code', 'Kode 2FA yang Anda masukkan salah. Silakan periksa kembali dan coba lagi.');

            return;
        }

        $this->loadRecoveryCodes();

        $this->reset('code', 'showVerificationStep');

        $this->showRecoveryStep = true;
        $this->twoFactorEnabled = true;

        $this->resetErrorBag();

        $this->dispatch('show-toast', message: 'Autentikasi dua faktor berhasil diaktifkan.');
    }

    /**
     * Reset two-factor verification state.
     */
    public function resetVerification(): void
    {
        $this->reset('code', 'showVerificationStep');

        $this->resetErrorBag();
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disable(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        if (! $this->requirePasswordConfirmation()) {
            return;
        }

        $disableTwoFactorAuthentication(auth()->user());

        $this->twoFactorEnabled = false;

        $this->dispatch('show-toast', message: 'Autentikasi dua faktor berhasil dinonaktifkan.');
    }

    /**
     * Close the two-factor authentication modal.
     */
    public function closeModal(): void
    {
        $this->reset(
            'code',
            'manualSetupKey',
            'qrCodeSvg',
            'recoveryCodes',
            'showModal',
            'showVerificationStep',
            'showRecoveryStep',
        );

        $this->resetErrorBag();

        if (! $this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }
    }

    /**
     * Get the current modal configuration state.
     *
     * @return array{title: string, description: string, buttonText: string}
     */
    #[Computed]
    public function modalConfig(): array
    {
        if ($this->showVerificationStep) {
            return [
                'title' => 'Verifikasi Kode Otentikasi',
                'description' => 'Masukkan 6 digit kode dari aplikasi otentikator Anda.',
                'buttonText' => 'Lanjutkan',
            ];
        }

        if ($this->showRecoveryStep) {
            return [
                'title' => '2FA Berhasil Diaktifkan',
                'description' => 'Simpan kode pemulihan berikut di tempat yang aman. Setiap kode hanya dapat digunakan satu kali untuk masuk jika Anda kehilangan akses ke aplikasi otentikator.',
                'buttonText' => 'Selesai',
            ];
        }

        if ($this->twoFactorEnabled) {
            return [
                'title' => 'Autentikasi Dua Faktor Aktif',
                'description' => 'Autentikasi dua faktor telah aktif. Pindai kode QR atau masukkan kunci penyiapan manual di aplikasi otentikator Anda.',
                'buttonText' => 'Tutup',
            ];
        }

        return [
            'title' => 'Aktifkan Autentikasi Dua Faktor',
            'description' => 'Untuk menyelesaikan pendaftaran 2FA, pindai kode QR atau masukkan kunci penyiapan manual di aplikasi otentikator Anda.',
            'buttonText' => 'Lanjutkan',
        ];
    }

    public function render(): View
    {
        return view('livewire.profile.security');
    }
}
