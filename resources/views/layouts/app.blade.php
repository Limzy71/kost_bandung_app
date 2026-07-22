<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kost Bandung - Cari Kost Hyper-Local' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans antialiased text-gray-950 bg-gray-50">

    <!-- Header / Navbar -->
    <header class="bg-white border-b-3 border-black sticky top-0 z-50 shadow-[0_4px_0_0_rgba(0,0,0,1)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="text-xl font-black text-black uppercase tracking-tight flex items-center">
                    KostBandung<span class="bg-yellow-300 border-2 border-black px-1.5 py-0.5 rounded text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] ml-1 font-black text-black">.id</span>
                </span>
            </a>
            <div class="flex items-center gap-3 md:gap-4">
                @auth
                    @if(auth()->user()->role === 'owner')
                        @if(request()->routeIs('dashboard'))
                            <a href="{{ route('home') }}" class="text-xs font-black uppercase text-black bg-cyan-300 hover:bg-cyan-200 px-4 py-2 border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded inline-flex items-center gap-1.5 group">
                                <svg class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span>Beranda Utama</span>
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="text-xs font-black uppercase text-black bg-yellow-300 hover:bg-yellow-200 px-4 py-2 border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded inline-flex items-center gap-1.5 group">
                                <svg class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                <span>Dashboard Pemilik</span>
                            </a>
                        @endif
                    @endif
                    <span class="text-xs font-black uppercase text-black bg-zinc-100 border-2 border-black px-3 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] rounded hidden sm:inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>{{ auth()->user()->name }}</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-black uppercase text-black bg-rose-400 hover:bg-rose-300 px-3.5 py-2 border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded cursor-pointer">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-xs md:text-sm font-black uppercase text-black bg-white hover:bg-zinc-100 px-3.5 py-2 border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="text-xs md:text-sm font-black uppercase text-black bg-yellow-400 hover:bg-yellow-300 px-4 py-2 border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded">
                        Pasang Iklan
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot ?? $content }}
    </main>

    <!-- Neo-Brutalist Footer -->
    <footer class="bg-white border-t-4 border-black mt-auto shadow-[0_-6px_0_0_rgba(0,0,0,1)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8 pb-6 border-b-3 border-black">
                <!-- Col 1: Brand & Tagline -->
                <div class="space-y-3">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5">
                        <span class="text-2xl font-black text-black uppercase tracking-tight flex items-center">
                            KostBandung<span class="bg-yellow-300 border-2 border-black px-2 py-0.5 rounded text-base shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] ml-1 font-black text-black">.id</span>
                        </span>
                    </a>
                    <p class="text-xs font-bold text-zinc-700 max-w-md leading-relaxed">
                        Platform Direktori Kost Hyper-Local Kota Bandung. Temukan kost mahasiswa & karyawan di area Coblong, Dipatiukur, Dago, dan sekitarnya dengan cepat & akurat.
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="px-2.5 py-1 bg-lime-300 border-2 border-black text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 20 20">
                                <defs>
                                    <linearGradient id="bolt-grad-footer" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#FBBF24" />
                                        <stop offset="100%" stop-color="#F97316" />
                                    </linearGradient>
                                </defs>
                                <path fill="url(#bolt-grad-footer)" stroke="#000000" stroke-width="0.8" fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd" />
                            </svg>
                            <span>Hyper-Local Bandung</span>
                        </span>
                        <span class="px-2.5 py-1 bg-cyan-300 border-2 border-black text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-flex items-center gap-1">
                            <svg class="w-3 h-3 text-black shrink-0 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"/>
                            </svg>
                            <span>100% Terverifikasi</span>
                        </span>
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black uppercase tracking-wider text-black bg-yellow-300 border-2 border-black px-2.5 py-1 inline-block shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        Navigasi Cepat
                    </h4>
                    <ul class="space-y-2.5 text-xs font-black uppercase">
                        <li>
                            <a href="{{ route('home') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                <svg class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span>Beranda Utama</span>
                            </a>
                        </li>
                        @auth
                            @if(auth()->user()->role === 'owner')
                                <li>
                                    <a href="{{ route('dashboard') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                        <svg class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                        <span>Dashboard Pemilik</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard.kost.create') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                        <svg class="w-4 h-4 text-black group-hover:rotate-90 transition-transform stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>Pasang Iklan Kost</span>
                                    </a>
                                </li>
                            @endif
                        @else
                            <li>
                                <a href="{{ route('login') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                    <svg class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Masuk Akun</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('register') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                    <svg class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                    <span>Daftar Pemilik Kost</span>
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright Line -->
            <div class="pt-6 text-center sm:text-left text-xs font-black text-black uppercase tracking-wider">
                <p>&copy; {{ date('Y') }} KostBandung.id. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

</body>
</html>