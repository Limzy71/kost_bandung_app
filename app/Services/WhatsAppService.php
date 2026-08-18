<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class WhatsAppService
{
    /**
     * Normalize an Indonesian phone number to E.164 without plus (e.g. 6281234567890).
     */
    public static function normalizePhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Strip non-digits
        $digits = preg_replace('/\D/', '', $phone);

        if (empty($digits)) {
            return null;
        }

        // Convert leading 0 to 62 (e.g. 0812... -> 62812...)
        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        // If it starts with 8 without country code (e.g. 812...), prepend 62
        if (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return $digits;
    }

    /**
     * Generate and send a 6-digit OTP code to the user's WhatsApp number.
     *
     * @return array{success: bool, message: string, cooldown?: int}
     */
    public function sendOtp(User $user): array
    {
        if (empty($user->phone_number)) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp belum diisi pada profil Anda.',
            ];
        }

        $normalizedPhone = self::normalizePhoneNumber($user->phone_number);
        if (empty($normalizedPhone) || strlen($normalizedPhone) < 10) {
            return [
                'success' => false,
                'message' => 'Format nomor WhatsApp tidak valid.',
            ];
        }

        // Rate limiting: Max 3 attempts per 10 minutes (600 seconds)
        $rateLimitKey = "phone_otp_limit:{$user->id}";
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            return [
                'success' => false,
                'message' => "Terlalu banyak permintaan OTP. Silakan tunggu {$seconds} detik lagi.",
                'cooldown' => $seconds,
            ];
        }

        // Generate 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        // Store hashed OTP in Cache for 5 minutes
        Cache::put("phone_otp:{$user->id}", Hash::make($otp), now()->addMinutes(5));
        Cache::put("phone_otp_number:{$user->id}", $user->phone_number, now()->addMinutes(5));

        // Record rate limit attempt
        RateLimiter::hit($rateLimitKey, 600);

        // Construct message
        $appName = config('app.name', 'KostBandung');
        $message = "Halo *{$user->name}*!\n\nKode verifikasi akun {$appName} Anda adalah:\n\n*{$otp}*\n\nKode ini berlaku selama 5 menit. Jangan bagikan kode ini kepada siapa pun.";

        // Send via configured driver
        $driver = config('services.whatsapp.driver', 'log');
        $sent = match ($driver) {
            'baileys' => $this->sendViaBaileys($normalizedPhone, $message),
            'meta_cloud' => $this->sendViaMetaCloud($normalizedPhone, $otp),
            default => $this->sendViaLog($normalizedPhone, $otp, $message),
        };

        if (! $sent) {
            return [
                'success' => false,
                'message' => 'Gagal mengirim pesan WhatsApp. Pastikan gateway layanan sedang aktif atau coba lagi beberapa saat lagi.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Kode OTP verifikasi berhasil dikirim ke nomor WhatsApp Anda.',
            'cooldown' => 60,
        ];
    }

    /**
     * Verify the OTP code provided by the user.
     *
     * @return array{success: bool, message: string}
     */
    public function verifyOtp(User $user, string $otp): array
    {
        $cachedHash = Cache::get("phone_otp:{$user->id}");
        $cachedPhone = Cache::get("phone_otp_number:{$user->id}");

        if (! $cachedHash || ! $cachedPhone) {
            return [
                'success' => false,
                'message' => 'Kode OTP sudah kedaluwarsa atau belum diminta. Silakan klik kirim ulang kode.',
            ];
        }

        // Brute-force protection: max 5 failed attempts per OTP cycle (300s window)
        $attemptsKey = "phone_otp_attempts:{$user->id}";
        if (RateLimiter::tooManyAttempts($attemptsKey, 5)) {
            Cache::forget("phone_otp:{$user->id}");
            Cache::forget("phone_otp_number:{$user->id}");
            RateLimiter::clear($attemptsKey);

            return [
                'success' => false,
                'message' => 'Terlalu banyak percobaan salah. Kode OTP telah dibatalkan. Silakan minta kode baru.',
            ];
        }

        // Ensure user hasn't modified phone number in the background
        if ($cachedPhone !== $user->phone_number) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp telah berubah sejak OTP dikirim. Silakan minta kode baru.',
            ];
        }

        $otpInput = trim($otp);
        if (! Hash::check($otpInput, $cachedHash)) {
            RateLimiter::hit($attemptsKey, 300);

            return [
                'success' => false,
                'message' => 'Kode OTP yang Anda masukkan salah. Silakan periksa kembali.',
            ];
        }

        // OTP is valid: clean cache and mark user as verified
        Cache::forget("phone_otp:{$user->id}");
        Cache::forget("phone_otp_number:{$user->id}");
        RateLimiter::clear($attemptsKey);

        $user->update([
            'phone_verified_at' => now(),
        ]);

        return [
            'success' => true,
            'message' => 'Selamat! Nomor WhatsApp Anda berhasil diverifikasi.',
        ];
    }

    /**
     * Check if the WhatsApp gateway is healthy and ready.
     */
    public function isGatewayConnected(): bool
    {
        $driver = config('services.whatsapp.driver', 'log');

        if ($driver === 'log') {
            return true;
        }

        if ($driver === 'baileys') {
            try {
                $response = Http::withToken(config('services.whatsapp.secret'))
                    ->timeout(3)
                    ->get(rtrim(config('services.whatsapp.gateway_url', 'http://127.0.0.1:3001'), '/').'/status');

                return $response->successful() && ($response->json('connected') === true);
            } catch (\Throwable $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Driver: Send message via self-hosted Baileys Node.js gateway on VPS.
     */
    protected function sendViaBaileys(string $phone, string $message): bool
    {
        try {
            $url = rtrim(config('services.whatsapp.gateway_url', 'http://127.0.0.1:3001'), '/').'/send-message';
            $secret = config('services.whatsapp.secret');

            $request = Http::timeout(8);
            if (! empty($secret)) {
                $request = $request->withToken($secret);
            }

            $response = $request->post($url, [
                'phone' => $phone,
                'message' => $message,
            ]);

            if (! $response->successful()) {
                Log::error('WhatsApp Baileys Gateway failed to send message', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp Baileys Gateway connection error', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Driver: Send message via official Meta WhatsApp Cloud API.
     */
    protected function sendViaMetaCloud(string $phone, string $otp): bool
    {
        $token = config('services.whatsapp.meta_token');
        $phoneNumberId = config('services.whatsapp.meta_phone_number_id');

        if (empty($token) || empty($phoneNumberId)) {
            Log::error('WhatsApp Meta Cloud credentials missing');

            return false;
        }

        try {
            $response = Http::withToken($token)
                ->timeout(8)
                ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'text',
                    'text' => [
                        'body' => "Kode verifikasi KostBandung Anda: {$otp}",
                    ],
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('WhatsApp Meta Cloud connection error', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Driver: Log message for testing and local development.
     */
    protected function sendViaLog(string $phone, string $otp, string $message): bool
    {
        Log::info("WhatsApp OTP [LOG DRIVER] sent to {$phone}: {$otp}\nMessage: {$message}");

        return true;
    }
}
