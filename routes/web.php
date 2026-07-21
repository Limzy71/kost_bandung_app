<?php

use App\Http\Controllers\KostController;
use App\Livewire\KostDetail;
use Illuminate\Support\Facades\Route;

Route::get('/', [KostController::class, 'index'])->name('home');
Route::get('/kost/{kost:slug}', KostDetail::class)->name('kost.show');
