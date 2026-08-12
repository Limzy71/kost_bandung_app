<?php

namespace App\Http\Controllers;

use App\Models\BoostTrial;
use App\Models\Kost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoostController extends Controller
{
    public function claimFreeTrial(Request $request, Kost $kost): JsonResponse
    {
        $user = Auth::user();

        // 1. Pastikan kost milik user
        if ($kost->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Pastikan user memiliki nomor HP
        if (empty($user->phone_number)) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan lengkapi nomor HP di profil Anda sebelum mengklaim trial.',
            ], 422);
        }

        // 3. Validasi device_fingerprint wajib ada
        $request->validate([
            'device_fingerprint' => 'required|string',
        ]);

        // 4. Normalisasi nomor HP
        $normalizedPhone = preg_replace('/[^0-9]/', '', $user->phone_number);
        if (str_starts_with($normalizedPhone, '0')) {
            $normalizedPhone = '62'.substr($normalizedPhone, 1);
        } elseif (str_starts_with($normalizedPhone, '+62')) {
            $normalizedPhone = '62'.substr($normalizedPhone, 3);
        }

        // 5. Hash fingerprint
        $hash = hash('sha256', $request->device_fingerprint);

        // 6. Cek boost_trials (Anti-Abuse)
        $trialExists = BoostTrial::where('user_id', $user->id)
            ->orWhere('owner_email', $user->email)
            ->orWhere('owner_phone', $normalizedPhone)
            ->orWhere('device_fingerprint_hash', $hash)
            ->exists();

        if ($trialExists) {
            return response()->json([
                'success' => false,
                'message' => 'Free trial Boost Kost hanya berlaku satu kali untuk setiap pemilik.',
            ], 422);
        }

        // 7. Lolos validasi, klaim trial
        BoostTrial::create([
            'user_id' => $user->id,
            'kost_id' => $kost->id,
            'owner_email' => $user->email,
            'owner_phone' => $normalizedPhone,
            'device_fingerprint_hash' => $hash,
        ]);

        $trialDays = config('midtrans.boost_trial_days', 3);

        $kost->update([
            'boost_type' => 'free_trial',
            'boosted_at' => now(),
            'boost_expires_at' => now()->addDays($trialDays),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Boost gratis berhasil diaktifkan selama {$trialDays} hari.",
        ]);
    }
}
