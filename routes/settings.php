<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'profil');
    Route::redirect('settings/profile', 'profil');
    Route::redirect('settings/appearance', 'profil');
    Route::redirect('settings/security', 'profil');
});

Route::get('.well-known/passkey-endpoints', function () {
    return response()->json([
        'enroll' => route('profile.show'),
        'manage' => route('profile.show'),
    ]);
})->name('well-known.passkeys');
