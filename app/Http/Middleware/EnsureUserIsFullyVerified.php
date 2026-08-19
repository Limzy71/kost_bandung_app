<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsFullyVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string|null  $redirectToRoute
     */
    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Admins bypass standard email/phone verification; users and owners must verify both
        if ($user->role !== 'admin' && ! $user->isFullyVerified()) {
            return $request->expectsJson()
                ? abort(403, 'Akun Anda belum terverifikasi secara penuh (email dan nomor WhatsApp wajib diverifikasi).')
                : redirect()->route($redirectToRoute ?: 'verify.account');
        }

        return $next($request);
    }
}
