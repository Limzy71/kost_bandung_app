<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KostController extends Controller
{
    // Halaman Utama (Daftar Kost)
    public function index(Request $request): View|RedirectResponse
    {
        // Redirect hard-refresh with ?page= param to a clean URL (keeping other
        // filters) so Livewire WithPagination handles paging state, not query strings.
        if ($request->has('page')) {
            return redirect()->to($request->fullUrlWithoutQuery('page'));
        }

        return view('kosts.index');
    }
}
