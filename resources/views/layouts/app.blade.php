<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'KostBandung - Cari Kost Khusus Kota Bandung' }}</title>
    {!! $meta ?? '' !!}
    {!! $head ?? '' !!}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/catalog-map.js', 'resources/js/echo.js'])
    @include('partials.appearance')
</head>
<body class="h-full flex flex-col font-sans antialiased text-gray-950 bg-[#f8f9fa] dark:text-zinc-100 dark:bg-zinc-950">

    <!-- Header / Navbar -->
    <x-site-navbar />

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot ?? $content }}
    </main>

    <!-- Neo-Brutalist Footer -->
    <footer class="bg-white border-t-4 border-black dark:border-zinc-700 dark:bg-zinc-900 dark:border-zinc-700 mt-auto shadow-[0_-6px_0_0_rgba(0,0,0,1)] dark:shadow-[0_-6px_0_0_rgba(255,255,255,0.25)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8 pb-6 border-b-3 border-black dark:border-zinc-700 dark:border-zinc-700">
                <!-- Col 1: Brand & Tagline -->
                <div class="space-y-3">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5">
                        <span class="text-2xl font-black text-black dark:text-white uppercase tracking-tight flex items-center">
                            KostBandung<span class="bg-yellow-300 border-2 border-black dark:border-zinc-700 px-2 py-0.5 rounded text-base shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] ml-1 font-black text-black">.web.id</span>
                        </span>
                    </a>
                    <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300 max-w-md leading-relaxed">
                        Direktori kost terlengkap khusus Kota Bandung. Temukan kost mahasiswa & karyawan di area Coblong, Dipatiukur, Dago, dan sekitarnya dengan mudah & cepat.
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="px-2.5 py-1 bg-lime-300 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1">
                            <x-icon name="lucide-zap" fill="#FBBF24" stroke="black" stroke-width="1.8" class="w-3.5 h-3.5 shrink-0" />
                            <span>Khusus Kota Bandung</span>
                        </span>
                        <span class="px-2.5 py-1 bg-cyan-300 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1">
                            <x-icon name="lucide-building-2" class="w-3 h-3 text-black shrink-0 stroke-[2.5]" />
                            <span>Pilihan Terlengkap</span>
                        </span>
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black uppercase tracking-wider text-black bg-yellow-300 border-2 border-black dark:border-zinc-700 px-2.5 py-1 inline-block shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                        Navigasi Cepat
                    </h4>
                    <ul class="space-y-2.5 text-xs font-black uppercase">
                        @auth
                            @if(auth()->user()->role === 'owner')
                                    <li>
                                        <a href="{{ route('home') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-house" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Beranda Utama</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('dashboard') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-layout-grid" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Dashboard Pemilik</span>
                                        </a>
                                    </li>

                                    @php
                                        $unreadChatCountMobile = \App\Models\KostMessage::whereNull('read_at')
                                            ->where(function ($q) {
                                                $q->whereNull('sender_id')->orWhere('sender_id', '!=', auth()->id());
                                            })
                                            ->whereHas('conversation', function ($q) {
                                                $q->whereHas('kost', fn ($k) => $k->where('user_id', auth()->id()));
                                            })
                                            ->count();
                                    @endphp
                                    <li>
                                        <a href="{{ route('dashboard.chats') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-message-circle" class="w-4 h-4 text-black dark:text-white group-hover:scale-110 transition-transform stroke-[2.5]" />
                                            <span>Obrolan Kost</span>
                                            @if($unreadChatCountMobile > 0)
                                                <span class="bg-rose-500 text-white rounded-full px-1.5 py-0.5 text-[9px] font-black min-w-[20px] text-center ml-1 border-2 border-black dark:border-zinc-700">{{ $unreadChatCountMobile }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('hubungi.admin') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-message-square-text" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Hubungi Admin</span>
                                        </a>
                                    </li>
                            @elseif(auth()->user()->role === 'admin')
                                    <li>
                                        <a href="{{ route('home') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-house" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Beranda Utama</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.moderation') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-circle-check" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Moderasi Admin</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.messages') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-message-square-text" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Inbox Bantuan</span>
                                        </a>
                                    </li>
                            @else
                                    <li>
                                        <a href="{{ route('home') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-house" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Beranda Utama</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('user.chats') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-message-circle" class="w-4 h-4 text-black dark:text-white group-hover:scale-110 transition-transform stroke-[2.5]" />
                                            <span>Obrolan Kost</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('hubungi.admin') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-message-square-text" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Hubungi Admin</span>
                                        </a>
                                    </li>
                            @endif

                                <li>
                                    <a href="{{ route('profile.show') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                        @if(auth()->user()->avatar_url)
                                            <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-5 h-5 rounded-md object-cover border border-black dark:border-zinc-700 shrink-0" />
                                        @else
                                            <x-icon name="lucide-user" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                        @endif
                                        <span>Profil Saya</span>
                                    </a>
                                </li>
                        @else
                                <li>
                                    <a href="{{ route('home') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                        <x-icon name="lucide-house" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                        <span>Beranda Utama</span>
                                    </a>
                                </li>
                            <li>
                                <a href="{{ route('login') }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                    <x-icon name="lucide-log-in" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                    <span>Masuk Akun</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('register', ['role' => 'user']) }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                    <x-icon name="lucide-user-plus" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                    <span>Daftar Pencari Kost</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('register', ['role' => 'owner']) }}" class="text-black dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                    <x-icon name="lucide-building-2" class="w-4 h-4 text-black dark:text-white group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                    <span>Daftar Pemilik Kost</span>
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright Line -->
            <div class="pt-6 text-center sm:text-left text-xs font-black text-black dark:text-white uppercase tracking-wider">
                <p>&copy; {{ date('Y') }} KostBandung.web.id. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    @persist('app-toast')
        <x-toast />
    @endpersist

    @persist('app-confirm')
        <x-confirm-modal />
    @endpersist

    @fluxScripts
</body>
</html>