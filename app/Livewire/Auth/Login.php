<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class Login extends Component
{
    public int $rateLimitSeconds = 0;

    private const MAX_FAILED_ATTEMPTS = 5;

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    /**
     * @var array<string, string>
     */
    protected array $messages = [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'password.required' => 'Kata sandi wajib diisi.',
    ];

    public function mount(): void
    {
        $redirect = request('redirect');

        if (is_string($redirect) && $redirect !== '' && str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
            session()->put('url.intended', $redirect);
        }
    }

    public function login(): Redirector|RedirectResponse|null
    {
        $this->validate();

        // Anti Brute-Force dengan progressive lockout — makin sering gagal, makin lama dikunci.
        // Strikes tersimpan 24 jam; lockout bertahap: 5× → 1 menit, 10× → 15 menit, 15× → 1 jam.
        $key = 'login_'.request()->ip();

        if ($this->isLocked($key)) {
            $this->rateLimitSeconds = RateLimiter::availableIn($key);
            $this->addError('rate_limit', 'TERLALU BANYAK PERCOBAAN LOGIN.');

            return null;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($key);
            session()->regenerate();

            $user = Auth::user();
            if ($user->role === 'owner') {
                return redirect()->intended('/dashboard');
            }

            return redirect()->intended('/');
        }

        $this->registerFailedAttempt($key);

        $this->addError('email', 'Email atau kata sandi yang Anda masukkan salah.');

        return null;
    }

    private function isLocked(string $key): bool
    {
        return RateLimiter::attempts($key) >= self::MAX_FAILED_ATTEMPTS
            && Cache::has($key.':timer');
    }

    private function registerFailedAttempt(string $key): void
    {
        $strikes = RateLimiter::hit($key, 86400); // jendela akumulasi percobaan: 24 jam

        $lockSeconds = $this->lockoutSeconds($strikes);

        // Timer lockout diperpanjang manual karena RateLimiter::hit tidak
        // memperpanjang timer yang sudah ada (ter-anchor ke hit pertama).
        Cache::put($key.':timer', now()->addSeconds($lockSeconds)->getTimestamp(), $lockSeconds);
    }

    private function lockoutSeconds(int $strikes): int
    {
        return match (true) {
            $strikes >= 15 => 3600, // 1 jam
            $strikes >= 10 => 900,  // 15 menit
            default => 60,          // 1 menit
        };
    }

    public function render(): View
    {
        return view('livewire.auth.login')->layout('layouts.auth');
    }
}
