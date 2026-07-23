<?php

use App\Http\Controllers\KostController;
use App\Livewire\KostDetail;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Livewire\Dashboard\OwnerDashboard;
use App\Livewire\Dashboard\CreateKost;
use App\Livewire\Dashboard\InquiryIndex;
use App\Livewire\Admin\ModerationDashboard;

Route::get('/', [KostController::class, 'index'])->name('home');
Route::get('/kost/{kost:slug}', KostDetail::class)->name('kost.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

Route::middleware(['auth', 'owner'])->group(function () {
    Route::get('/dashboard', OwnerDashboard::class)->name('dashboard');
    Route::get('/dashboard/kost/create', CreateKost::class)->name('dashboard.kost.create');
    Route::get('/dashboard/inquiries', InquiryIndex::class)->name('dashboard.inquiries');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/moderation', ModerationDashboard::class)->name('admin.moderation');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});
