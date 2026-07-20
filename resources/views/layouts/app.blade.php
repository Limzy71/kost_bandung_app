<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kost Bandung - Cari Kost Hyper-Local' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans antialiased text-slate-800">

    <!-- Header / Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">
                    KostBandung.id
                </span>
            </a>
            <div class="flex items-center gap-4">
                <a href="#" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition">Sewa Kostan</a>
                <a href="#" class="text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl shadow-sm transition">
                    Pasang Iklan
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot ?? $content }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-12 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm text-slate-500">
            &copy; {{ date('Y') }} KostBandung.id — Direkayasa untuk kemudahan pencarian kost di area Bandung.
        </div>
    </footer>

</body>
</html>