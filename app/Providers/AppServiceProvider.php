<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureAuthEmails();
        $this->configureVitePreloads();
    }

    /**
     * Disable CSS preload tags emitted by Laravel 13's Vite integration.
     *
     * When navigating via Livewire's SPA mode (wire:navigate), the new page's
     * <head> is merged into the current document, re-injecting the CSS preload
     * even though the stylesheet is already loaded. The browser then warns that
     * the preloaded resource was never used. JS modulepreload stays enabled.
     */
    protected function configureVitePreloads(): void
    {
        Vite::usePreloadTagAttributes(function ($src, $url, $chunk, $manifest) {
            if (str_ends_with($url, '.css')) {
                return false;
            }

            return [];
        });
    }

    /**
     * Configure customized authentication emails with KostBandung branding.
     */
    protected function configureAuthEmails(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email Anda — KostBandung')
                ->greeting('Halo ' . $notifiable->name . ',')
                ->line('Terima kasih telah mendaftar di **KostBandung** — Direktori Kost Khusus Kota Bandung.')
                ->line('Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda dan mengaktifkan akun:')
                ->action('Verifikasi Email Saya', $url)
                ->line('Tautan verifikasi ini berlaku selama 60 menit.')
                ->line('Jika Anda tidak merasa mendaftar di KostBandung, Anda dapat mengabaikan email ini dengan aman.')
                ->salutation("Salam hangat,\nTim KostBandung");
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);

            return (new MailMessage)
                ->subject('Permintaan Reset Kata Sandi — KostBandung')
                ->greeting('Halo ' . $notifiable->name . ',')
                ->line('Kami menerima permintaan untuk mereset kata sandi akun **KostBandung** Anda.')
                ->line('Klik tombol di bawah ini untuk membuat kata sandi baru:')
                ->action('Reset Kata Sandi', $resetUrl)
                ->line('Tautan reset kata sandi ini akan kedaluwarsa dalam ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60) . ' menit.')
                ->line('Jika Anda tidak meminta reset kata sandi, tidak ada tindakan lebih lanjut yang diperlukan dan akun Anda tetap aman.')
                ->salutation("Salam hangat,\nTim KostBandung");
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
