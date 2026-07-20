<?php

use App\Http\Controllers\KostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KostController::class, 'index'])->name('home');
Route::get('/kost/{kost:slug}', [KostController::class, 'show'])->name('kost.show');
