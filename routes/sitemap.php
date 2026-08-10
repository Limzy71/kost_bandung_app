<?php

use App\Models\Kost;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', function () {
    $kosts = Kost::where('status', 'active')->latest()->get();

    $content = view('sitemap', ['kosts' => $kosts])->render();

    return response($content, 200)
        ->header('Content-Type', 'text/xml');
})->name('sitemap');
