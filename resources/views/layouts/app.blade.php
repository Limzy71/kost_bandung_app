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
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                <span>Lihat Website</span>
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

    <!-- Footer -->
    <footer class="bg-white border-t-3 border-black py-8 shadow-[0_-4px_0_0_rgba(0,0,0,1)]">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-black font-black uppercase tracking-wider">
            &copy; {{ date('Y') }} KostBandung.id — Direkayasa untuk kemudahan pencarian kost di area Bandung.
        </div>
    </footer>

</body>
</html>