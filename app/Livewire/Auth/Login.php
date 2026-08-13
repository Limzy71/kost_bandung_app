<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class Login extends Component
{
    public int $rateLimitSeconds = 0;

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

        // Anti Brute-Force Rate Limiter — max 5 attempts per IP per minute
        $key = 'login_'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
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

        // Increment rate limiter on failed attempt (5 seconds block)
        RateLimiter::hit($key, 5);

        $this->addError('email', 'Email atau kata sandi yang Anda masukkan salah.');

        return null;
    }

    public function render(): View
    {
        return view('livewire.auth.login')->layout('layouts.auth');
    }
}
