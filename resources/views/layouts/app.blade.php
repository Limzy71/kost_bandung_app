<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kost Bandung - Cari Kost Hyper-Local' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/catalog-map.js'])
</head>
<body class="h-full flex flex-col font-sans antialiased text-gray-950 bg-[#f8f9fa]">

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
                    @if(auth()->user()->role === 'admin')
                        @if(request()->routeIs('admin*') || request()->routeIs('profile.show'))
                            <a href="{{ route('home') }}" class="text-xs font-black uppercase text-black bg-cyan-300 hover:bg-cyan-200 px-4 py-2 border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded inline-flex items-center gap-1.5 group">
                                <x-icon name="lucide-house" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                <span class="max-sm:hidden">Beranda Utama</span>
                            </a>
                        @endif
                        @unless(request()->routeIs('admin.moderation'))
                            <a href="{{ route('admin.moderation') }}" class="text-xs font-black uppercase text-black bg-lime-300 hover:bg-lime-200 px-3.5 py-2 border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded inline-flex items-center gap-1.5 group">
                                <x-icon name="lucide-circle-check" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                <span class="max-sm:hidden">Moderasi Admin</span>
                            </a>
                        @endunless
                    @endif

                    @if(auth()->user()->role === 'owner')
                        @if(request()->routeIs('dashboard*') || request()->routeIs('profile.show'))
                            <a href="{{ route('home') }}" class="text-xs font-black uppercase text-black bg-cyan-300 hover:bg-cyan-200 px-4 py-2 border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded inline-flex items-center gap-1.5 group">
                                <x-icon name="lucide-house" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                <span class="max-sm:hidden">Beranda Utama</span>
                            </a>
                        @endif

                        @unless(request()->routeIs('dashboard'))
                            <a href="{{ route('dashboard') }}" class="text-xs font-black uppercase text-black bg-yellow-300 hover:bg-yellow-200 px-4 py-2 border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded inline-flex items-center gap-1.5 group">
                                <x-icon name="lucide-layout-grid" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                <span class="max-sm:hidden">Dashboard Pemilik</span>
                            </a>
                        @endunless

                        @php
                            $unreadInquiriesCount = \App\Models\Inquiry::where('status', 'unread')->whereHas('kost', function($q) {
                                $q->where('user_id', auth()->id());
                            })->count();
                        @endphp
                        <a href="{{ route('dashboard.inquiries') }}" class="text-xs font-black uppercase text-black bg-white hover:bg-zinc-100 px-4 py-2 border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded inline-flex items-center gap-1.5 group">
                            <x-icon name="lucide-inbox" class="w-4 h-4 text-black group-hover:scale-110 transition-transform stroke-[2.5]" />
                            <span class="max-sm:hidden">Inbox Pesan</span>
                            @if($unreadInquiriesCount > 0)
                                <span class="bg-rose-500 text-white border-2 border-black rounded-full px-1.5 py-0.5 text-[9px] min-w-[20px] text-center ml-1">{{ $unreadInquiriesCount }}</span>
                            @endif
                        </a>
                    @endif

                    @if(auth()->user()->role === 'user')
                        @if(request()->routeIs('profile.show'))
                            <a href="{{ route('home') }}" class="text-xs font-black uppercase text-black bg-cyan-300 hover:bg-cyan-200 px-4 py-2 border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded inline-flex items-center gap-1.5 group">
                                <x-icon name="lucide-house" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                <span class="max-sm:hidden">Beranda Utama</span>
                            </a>
                        @endif
                    @endif

                    @unless(request()->routeIs('profile.show'))
                        <a href="{{ route('profile.show') }}" class="text-xs font-black uppercase text-black bg-zinc-100 hover:bg-zinc-200 border-2 border-black px-3 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] rounded max-sm:hidden inline-flex items-center gap-1.5 cursor-pointer transition-all" title="Profil Saya">
                            <x-icon name="lucide-user" class="w-4 h-4 text-black stroke-[2.5]" />
                            <span>{{ auth()->user()->name }}</span>
                        </a>
                    @endunless

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
                            <x-icon name="lucide-zap" fill="#FBBF24" stroke-width="0.8" class="w-3.5 h-3.5 shrink-0" />
                            <span>Hyper-Local Bandung</span>
                        </span>
                        <span class="px-2.5 py-1 bg-cyan-300 border-2 border-black text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-flex items-center gap-1">
                            <x-icon name="lucide-building-2" class="w-3 h-3 text-black shrink-0 stroke-[2.5]" />
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
                        @auth
                            @if(auth()->user()->role === 'owner')
                                @unless(request()->routeIs('home'))
                                    <li>
                                        <a href="{{ route('home') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-house" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Beranda Utama</span>
                                        </a>
                                    </li>
                                @endunless
                                @unless(request()->routeIs('dashboard'))
                                    <li>
                                        <a href="{{ route('dashboard') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-layout-grid" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Dashboard Pemilik</span>
                                        </a>
                                    </li>
                                @endunless

                                @unless(request()->routeIs('dashboard.inquiries'))
                                    @php
                                        $unreadInquiriesCountMobile = \App\Models\Inquiry::where('status', 'unread')->whereHas('kost', function($q) {
                                            $q->where('user_id', auth()->id());
                                        })->count();
                                    @endphp
                                    <li>
                                        <a href="{{ route('dashboard.inquiries') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-inbox" class="w-4 h-4 text-black group-hover:scale-110 transition-transform stroke-[2.5]" />
                                            <span>Inbox Pesan</span>
                                            @if($unreadInquiriesCountMobile > 0)
                                                <span class="bg-rose-500 text-white rounded-full px-1.5 py-0.5 text-[9px] font-black min-w-[20px] text-center ml-1 border-2 border-black">{{ $unreadInquiriesCountMobile }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endunless
                            @elseif(auth()->user()->role === 'admin')
                                @unless(request()->routeIs('home'))
                                    <li>
                                        <a href="{{ route('home') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-house" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Beranda Utama</span>
                                        </a>
                                    </li>
                                @endunless
                                @unless(request()->routeIs('admin.moderation'))
                                    <li>
                                        <a href="{{ route('admin.moderation') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-circle-check" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Moderasi Admin</span>
                                        </a>
                                    </li>
                                @endunless
                            @else
                                @unless(request()->routeIs('home'))
                                    <li>
                                        <a href="{{ route('home') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                            <x-icon name="lucide-house" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                            <span>Beranda Utama</span>
                                        </a>
                                    </li>
                                @endunless
                            @endif

                            @unless(request()->routeIs('profile.show'))
                                <li>
                                    <a href="{{ route('profile.show') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                        <x-icon name="lucide-user" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                        <span>Profil Saya</span>
                                    </a>
                                </li>
                            @endunless
                        @else
                            @unless(request()->routeIs('home'))
                                <li>
                                    <a href="{{ route('home') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                        <x-icon name="lucide-house" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                        <span>Beranda Utama</span>
                                    </a>
                                </li>
                            @endunless
                            <li>
                                <a href="{{ route('login') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                    <x-icon name="lucide-log-in" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
                                    <span>Masuk Akun</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('register') }}" class="text-black hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all inline-flex items-center gap-2 group">
                                    <x-icon name="lucide-user-plus" class="w-4 h-4 text-black group-hover:rotate-12 transition-transform stroke-[2.5]" />
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