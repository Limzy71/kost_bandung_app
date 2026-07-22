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
                    <span class="text-xs font-black uppercase text-black bg-zinc-100 border-2 border-black px-3 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] rounded hidden sm:inline-block">
                        👤 {{ auth()->user()->name }}
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8 border-b-3 border-black">
                <!-- Col 1: Brand & Tagline -->
                <div class="md:col-span-2 space-y-3">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5">
                        <span class="text-2xl font-black text-black uppercase tracking-tight flex items-center">
                            KostBandung<span class="bg-yellow-300 border-2 border-black px-2 py-0.5 rounded text-base shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] ml-1 font-black text-black">.id</span>
                        </span>
                    </a>
                    <p class="text-xs font-bold text-zinc-700 max-w-sm leading-relaxed">
                        Platform Direktori Kost Hyper-Local Kota Bandung. Temukan kost mahasiswa & karyawan di area Coblong, Dipatiukur, Dago, dan sekitarnya dengan cepat & akurat.
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="px-2.5 py-1 bg-lime-300 border-2 border-black text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            ⚡ Hyper-Local Bandung
                        </span>
                        <span class="px-2.5 py-1 bg-cyan-300 border-2 border-black text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            🏢 100% Terverifikasi
                        </span>
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black uppercase tracking-wider text-black bg-yellow-300 border-2 border-black px-2 py-1 inline-block shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        Navigasi Cepat
                    </h4>
                    <ul class="space-y-2 text-xs font-black uppercase">
                        <li>
                            <a href="{{ route('home') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-block">
                                🏠 Beranda Utama
                            </a>
                        </li>
                        @auth
                            @if(auth()->user()->role === 'owner')
                                <li>
                                    <a href="{{ route('dashboard') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-block">
                                        📊 Dashboard Pemilik
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard.kost.create') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-block">
                                        ➕ Pasang Iklan Kost
                                    </a>
                                </li>
                            @endif
                        @else
                            <li>
                                <a href="{{ route('login') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-block">
                                    🔑 Masuk Akun
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('register') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-block">
                                    📝 Daftar Pemilik Kost
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>

                <!-- Col 3: Tech Stack & System Info -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black uppercase tracking-wider text-black bg-pink-300 border-2 border-black px-2 py-1 inline-block shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        Teknologi Platform
                    </h4>
                    <ul class="space-y-2 text-xs font-bold text-zinc-700">
                        <li class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-rose-500 rounded-full border border-black shrink-0"></span>
                            <span>Laravel 12 Framework</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-sky-500 rounded-full border border-black shrink-0"></span>
                            <span>Livewire 3 & Alpine.js</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-teal-500 rounded-full border border-black shrink-0"></span>
                            <span>Tailwind CSS v4.1 (Neo-Brutalist)</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright Line -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-black text-black uppercase tracking-wider text-center sm:text-left">
                <p>
                    &copy; 2026 KostBandung.id — Dikembangkan dengan ❤️ oleh Mahasiswa Teknik Informatika '23.
                </p>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-zinc-100 border-2 border-black text-[10px] shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] font-black">
                        v2.0.0 Stable
                    </span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>