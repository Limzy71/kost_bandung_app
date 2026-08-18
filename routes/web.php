<?php

use App\Http\Controllers\KostController;
use App\Http\Controllers\VerificationDocumentController;
use App\Livewire\Admin\AdminMessages;
use App\Livewire\Admin\ModerationDashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Contact\AdminChat;
use App\Livewire\Dashboard\CreateKost;
use App\Livewire\Dashboard\EditKost;
use App\Livewire\Dashboard\OwnerChat;
use App\Livewire\Dashboard\OwnerDashboard;
use App\Livewire\Dashboard\SeekerChat;
use App\Livewire\KostDetail;
use App\Livewire\Profile\Index as ProfileIndex;
use App\Livewire\Profile\PublicOwner as PublicOwnerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [KostController::class, 'index'])->name('home');
Route::get('/kost/{kost:slug}', KostDetail::class)->name('kost.show');
Route::get('/syarat-ketentuan', function () {
    return view('terms');
})->name('terms');
Route::get('/owner/{user}', PublicOwnerProfile::class)->name('profile.owner');

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Livewire\Auth\VerifyAccount;
use App\Livewire\Onboarding\CompleteOnboarding;

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

Route::get('/verify-account', VerifyAccount::class)
    ->middleware(['auth'])
    ->name('verification.notice');

Route::get('/onboarding', CompleteOnboarding::class)
    ->middleware(['auth'])
    ->name('onboarding');

use App\Http\Controllers\BoostController;
use App\Http\Controllers\PaymentController;

Route::middleware(['auth', 'verified', 'owner'])->group(function () {
    Route::get('/dashboard', OwnerDashboard::class)->name('dashboard');
    Route::get('/dashboard/kost/create', CreateKost::class)->name('dashboard.kost.create');
    Route::get('/dashboard/kost/{kost:slug}/edit', EditKost::class)->name('dashboard.kost.edit');
    Route::get('/dashboard/chats', OwnerChat::class)->name('dashboard.chats');

    // Boost Routes
    Route::post('/dashboard/kost/{kost:slug}/boost/free-trial', [BoostController::class, 'claimFreeTrial'])->name('boost.trial');
    Route::post('/dashboard/kost/{kost:slug}/boost/payment', [PaymentController::class, 'createPayment'])->name('boost.payment');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin/moderation', ModerationDashboard::class)->name('admin.moderation');
    Route::get('/admin/messages', AdminMessages::class)->name('admin.messages');
    Route::get('/admin/verification-document/{kind}/{id}', VerificationDocumentController::class)->name('admin.verification.document');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profil', ProfileIndex::class)->name('profile.show');
    Route::get('/dashboard/user/chats', SeekerChat::class)->name('user.chats');
    Route::get('/hubungi-admin', AdminChat::class)->name('hubungi.admin');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

require __DIR__.'/settings.php';
require __DIR__.'/sitemap.php';
