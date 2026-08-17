<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (! $user->hasCompletedOnboarding()) {
                if (! $request->routeIs('onboarding', 'logout', 'terms') && ! $request->is('livewire/*')) {
                    return redirect()->route('onboarding');
                }
            } else {
                if ($request->routeIs('onboarding')) {
                    return redirect()->to($user->role === 'owner' ? route('dashboard') : route('home'));
                }
            }
        }

        return $next($request);
    }
}
