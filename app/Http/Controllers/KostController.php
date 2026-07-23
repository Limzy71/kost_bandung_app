<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\Http\Request;

class KostController extends Controller
{
    // Halaman Utama (Daftar Kost)
    public function index(Request $request)
    {
        // Redirect hard-refresh with ?page= param to clean URL so Livewire
        // WithPagination handles paging state instead of query strings.
        if ($request->has('page')) {
            return redirect()->to(url('/'));
        }

        return view('kosts.index');
    }

    // Halaman Detail Kost
    public function show(Kost $kost)
    {
        $kost->load(['facilities', 'rules', 'images', 'owner']);

        return view('kosts.show', compact('kost'));
    }
}