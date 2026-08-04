<?php

use App\Http\Controllers\KostController;
use App\Livewire\Admin\ModerationDashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Dashboard\CreateKost;
use App\Livewire\Dashboard\EditKost;
use App\Livewire\Dashboard\InquiryIndex;
use App\Livewire\Dashboard\OwnerDashboard;
use App\Livewire\Dashboard\SeekerInquiries;
use App\Livewire\KostDetail;
use App\Livewire\Profile\Index as ProfileIndex;
use App\Livewire\Profile\PublicOwner as PublicOwnerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [KostController::class, 'index'])->name('home');
Route::get('/kost/{kost:slug}', KostDetail::class)->name('kost.show');
Route::get('/owner/{user}', PublicOwnerProfile::class)->name('profile.owner');

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

Route::middleware(['auth', 'verified', 'owner'])->group(function () {
    Route::get('/dashboard', OwnerDashboard::class)->name('dashboard');
    Route::get('/dashboard/kost/create', CreateKost::class)->name('dashboard.kost.create');
    Route::get('/dashboard/kost/{kost:slug}/edit', EditKost::class)->name('dashboard.kost.edit');
    Route::get('/dashboard/inquiries', InquiryIndex::class)->name('dashboard.inquiries');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin/moderation', ModerationDashboard::class)->name('admin.moderation');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profil', ProfileIndex::class)->name('profile.show');
    Route::get('/dashboard/user/inquiries', SeekerInquiries::class)->name('user.inquiries');
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
