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
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="text-xl font-extrabold text-gray-950 tracking-tight">
                    KostBandung.id
                </span>
            </a>
            <div class="flex items-center gap-3 md:gap-4">
                @auth
                    @if(auth()->user()->role === 'owner')
                        <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-gray-700 hover:text-gray-950 px-3 py-2 rounded-full border border-gray-200 transition">
                            Dashboard
                        </a>
                    @endif
                    <span class="text-xs font-medium text-gray-500 hidden sm:inline">
                        {{ auth()->user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-gray-600 hover:text-gray-950 px-3 py-2 transition">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-xs md:text-sm font-semibold text-gray-700 hover:text-gray-950 px-3 py-2 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="text-xs md:text-sm font-semibold bg-gray-950 hover:bg-gray-800 text-white px-4 py-2.5 rounded-full shadow-sm transition">
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
    <footer class="bg-white border-t border-gray-200 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-gray-500 font-medium">
            &copy; {{ date('Y') }} KostBandung.id — Direkayasa untuk kemudahan pencarian kost di area Bandung.
        </div>
    </footer>

</body>
</html>