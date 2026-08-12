<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/midtrans/callback', [PaymentController::class, 'handleWebhook'])->name('midtrans.callback');
