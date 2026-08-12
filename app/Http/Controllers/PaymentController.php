<?php

namespace App\Http\Controllers;

use App\Mail\BoostPaid\SuccessMail;
use App\Models\BoostTransaction;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function createPayment(Request $request, Kost $kost): JsonResponse
    {
        $user = Auth::user();

        if ($kost->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $orderId = 'BOOST-'.$kost->id.'-'.time().'-'.Str::random(5);
        $amount = config('midtrans.boost_price', 50000);

        // Record pending transaction
        $transaction = BoostTransaction::create([
            'order_id' => $orderId,
            'user_id' => $user->id,
            'kost_id' => $kost->id,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        $serverKey = config('midtrans.server_key');

        if (blank($serverKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi pembayaran belum tersedia.',
            ], 500);
        }

        $isProduction = config('midtrans.is_production');
        $apiUrl = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number,
            ],
            'item_details' => [
                [
                    'id' => 'BOOST-30',
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Boost Kost '.config('midtrans.boost_duration_days', 30).' Hari',
                ],
            ],
        ];

        $response = Http::withBasicAuth($serverKey, '')
            ->post($apiUrl, $payload);

        if ($response->successful()) {
            $snapToken = $response->json('token');
            $transaction->update(['snap_token' => $snapToken]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal menghubungi server pembayaran.',
        ], 500);
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';

        // Validate Signature
        $serverKey = config('midtrans.server_key');
        $calculatedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        if ($calculatedSignature !== $signatureKey) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transaction = BoostTransaction::where('order_id', $orderId)->first();

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Idempotency Check: if already paid, do nothing
        if ($transaction->status === 'paid') {
            return response()->json(['message' => 'Transaction already paid'], 200);
        }

        $transaction->update([
            'midtrans_status' => $transactionStatus,
            'payment_type' => $payload['payment_type'] ?? null,
            'midtrans_response' => $payload,
        ]);

        if (in_array($transactionStatus, ['settlement', 'capture'])) {
            $transaction->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $kost = Kost::find($transaction->kost_id);
            $user = User::find($transaction->user_id);

            if (! $kost || ! $user) {
                return response()->json(['message' => 'Transaction relation not found'], 404);
            }

            $durationDays = config('midtrans.boost_duration_days', 30);

            $currentExpiry = $kost->boost_expires_at?->isFuture()
                ? $kost->boost_expires_at
                : now();

            $newExpiry = $currentExpiry->copy()->addDays($durationDays);

            $kost->update([
                'boost_type' => 'paid',
                'boosted_at' => now(),
                'boost_expires_at' => $newExpiry,
            ]);

            Mail::to($user->email)->send(new SuccessMail($kost, $transaction));
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'])) {
            $transaction->update([
                'status' => $transactionStatus === 'expire' ? 'expired' : 'failed',
            ]);
        }

        return response()->json(['message' => 'OK'], 200);
    }
}
