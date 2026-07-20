<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\Http\Request;

class KostController extends Controller
{
    // Halaman Utama (Daftar Kost)
    public function index()
    {
        $kosts = Kost::with(['facilities', 'images'])
            ->where('is_available', true)
            ->latest('boosted_at')
            ->paginate(9);

        return view('kosts.index', compact('kosts'));
    }

    // Halaman Detail Kost
    public function show(Kost $kost)
    {
        $kost->load(['facilities', 'rules', 'images', 'owner']);

        return view('kosts.show', compact('kost'));
    }
}