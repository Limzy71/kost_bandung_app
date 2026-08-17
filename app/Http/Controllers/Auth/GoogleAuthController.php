<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and authenticate.
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            try {
                $googleUser = Socialite::driver('google')->user();
            } catch (\Laravel\Socialite\Two\InvalidStateException) {
                $googleUser = Socialite::driver('google')->stateless()->user();
            }
        } catch (\Throwable $e) {
            Log::warning('Google OAuth login failed: '.get_class($e).' - '.$e->getMessage());

            return redirect()->route('login')->with('error', 'Gagal masuk dengan akun Google. Silakan coba kembali atau gunakan email dan kata sandi.');
        }

        if (! $googleUser->getEmail()) {
            return redirect()->route('login')->with('error', 'Akun Google Anda tidak menyediakan alamat email.');
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            if (! $user->google_id) {
                $user->google_id = $googleUser->getId();
            }

            if (! $user->email_verified_at) {
                $user->email_verified_at = now();
            }

            if (! $user->avatar && $googleUser->getAvatar()) {
                $user->avatar = $googleUser->getAvatar();
            }

            if ($user->isDirty()) {
                $user->save();
            }
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'Pengguna'),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'role' => null,
                'terms_accepted_at' => null,
                'email_verified_at' => now(),
                'password' => null,
            ]);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        if (! $user->hasCompletedOnboarding()) {
            session()->put('pending_onboarding', true);

            return redirect()->route('onboarding');
        }

        if ($user->role === 'owner') {
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->intended(route('home'));
    }
}
