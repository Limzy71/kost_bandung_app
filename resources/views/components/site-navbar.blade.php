@php
    $user = auth()->user();
    $role = $user?->role;

    $unreadAdminRepliesCount = 0;
    $adminUnansweredCount = 0;
    $unreadInquiriesCount = 0;
    $newRepliesCount = 0;

    if ($user) {
        $unreadAdminRepliesCount = \App\Models\AdminMessage::where('sender_type', 'admin')
            ->whereNull('read_at')
            ->whereHas('conversation', fn ($q) => $q->where('user_id', $user->id))
            ->count();

        if ($role === 'admin') {
            $adminUnansweredCount = \App\Models\AdminConversation::where('status', 'open')
                ->whereNotNull('awaiting_reply_at')
                ->count();
        }

        if ($role === 'owner') {
            $unreadInquiriesCount = \App\Models\Inquiry::where('status', 'unread')
                ->whereHas('kost', fn ($q) => $q->where('user_id', $user->id))
                ->count();
        }

        if ($role === 'user') {
            $newRepliesCount = \App\Models\Inquiry::where('user_id', $user->id)
                ->whereNotNull('owner_reply')
                ->where('status', '!=', 'archived')
                ->whereNull('seeker_seen_reply_at')
                ->count();
        }
    }

    $navItems = [];
    if ($role === 'admin') {
        $navItems = [
            ['href' => route('home'), 'label' => 'Beranda Utama', 'icon' => 'lucide-house', 'match' => 'home'],
            ['href' => route('admin.moderation'), 'label' => 'Moderasi Admin', 'icon' => 'lucide-circle-check', 'match' => 'admin.moderation'],
            ['href' => route('admin.messages'), 'label' => 'Inbox Bantuan', 'icon' => 'lucide-message-square-text', 'match' => 'admin.messages', 'badge' => $adminUnansweredCount],
        ];
    } elseif ($role === 'owner') {
        $navItems = [
            ['href' => route('home'), 'label' => 'Beranda Utama', 'icon' => 'lucide-house', 'match' => 'home'],
            ['href' => route('dashboard'), 'label' => 'Dashboard Pemilik', 'icon' => 'lucide-layout-grid', 'match' => ['dashboard', 'dashboard.kost.create', 'dashboard.kost.edit']],
            ['href' => route('dashboard.inquiries'), 'label' => 'Inbox Pesan', 'icon' => 'lucide-inbox', 'match' => 'dashboard.inquiries', 'badge' => $unreadInquiriesCount],
            ['href' => route('hubungi.admin'), 'label' => 'Hubungi Admin', 'icon' => 'lucide-message-circle', 'match' => 'hubungi.admin', 'badge' => $unreadAdminRepliesCount],
        ];
    } elseif ($role === 'user') {
        $navItems = [
            ['href' => route('home'), 'label' => 'Beranda Utama', 'icon' => 'lucide-house', 'match' => 'home'],
            ['href' => route('user.inquiries'), 'label' => 'Pesan Terkirim', 'icon' => 'lucide-send', 'match' => 'user.inquiries', 'badge' => $newRepliesCount],
            ['href' => route('hubungi.admin'), 'label' => 'Hubungi Admin', 'icon' => 'lucide-message-circle', 'match' => 'hubungi.admin', 'badge' => $unreadAdminRepliesCount],
        ];
    }

    $guestItems = [
        ['href' => route('login'), 'label' => 'Masuk', 'icon' => 'lucide-log-in'],
        ['href' => route('register', ['role' => 'owner']), 'label' => 'Pasang Iklan', 'icon' => 'lucide-building-2'],
    ];

    $isActive = fn ($item) => request()->routeIs($item['match']);

    $desktopItemClass = fn ($item) => $isActive($item)
        ? 'bg-yellow-300 border-3 shadow-none translate-x-0.5 translate-y-0.5'
        : 'bg-white hover:bg-zinc-100 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none';
@endphp

<header class="bg-white border-b-3 border-black sticky top-0 z-50 shadow-[0_4px_0_0_rgba(0,0,0,1)]"
    x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-3">
        {{-- Logo --}}
        <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2 shrink-0" aria-label="KostBandung.web.id - Beranda">
            <span class="text-lg sm:text-xl font-black text-black uppercase tracking-tight flex items-center leading-none">
                KostBandung<span class="bg-yellow-300 border-2 border-black px-1.5 py-0.5 rounded text-xs sm:text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] ml-1 font-black text-black">.web.id</span>
            </span>
        </a>

        {{-- Desktop Navigation --}}
        <nav class="hidden lg:flex items-center gap-2" aria-label="Navigasi utama">
            @auth
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}" wire:navigate @if ($isActive($item)) aria-current="page" @endif
                        class="inline-flex items-center gap-1.5 text-xs font-black uppercase text-black px-3.5 py-2 border-2 border-black rounded transition-all cursor-pointer {{ $desktopItemClass($item) }}">
                        <x-icon name="{{ $item['icon'] }}" class="w-4 h-4 stroke-[2.5]" />
                        <span>{{ $item['label'] }}</span>
                        @if (isset($item['badge']))
                            <span x-data="{ count: {{ $item['badge'] }} }"
                                @if($item['match'] === 'dashboard.inquiries')
                                    @inquiries-updated.window="count = $event.detail.count"
                                @endif
                                x-show="count > 0"
                                x-text="count"
                                class="bg-rose-500 text-white border-2 border-black rounded-full px-1.5 py-0.5 text-[9px] min-w-[20px] text-center ml-1"></span>
                        @endif
                    </a>
                @endforeach

                <a href="{{ route('profile.show') }}" wire:navigate @if (request()->routeIs('profile.show')) aria-current="page" @endif
                    class="inline-flex items-center gap-1.5 text-xs font-black uppercase text-black border-2 border-black px-3.5 py-2 rounded transition-all cursor-pointer {{ request()->routeIs('profile.show') ? 'bg-yellow-300 border-3 shadow-none translate-x-0.5 translate-y-0.5' : 'bg-white hover:bg-zinc-100 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none' }}"
                    title="Profil Saya" aria-label="Profil Saya">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="" class="w-5 h-5 rounded-md object-cover border border-black shrink-0" />
                    @else
                        <x-icon name="lucide-user" class="w-4 h-4 stroke-[2.5]" />
                    @endif
                    <span class="max-xl:hidden">Profil Saya</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 text-xs font-black uppercase text-black bg-rose-400 hover:bg-rose-300 px-3.5 py-2 border-2 border-black rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer"
                        title="Keluar" aria-label="Keluar">
                        <x-icon name="lucide-log-out" class="w-4 h-4 stroke-[2.5]" />
                        <span class="max-xl:hidden">Keluar</span>
                    </button>
                </form>
            @else
                @foreach ($guestItems as $item)
                    <a href="{{ $item['href'] }}" wire:navigate
                        class="inline-flex items-center gap-1.5 text-xs md:text-sm font-black uppercase text-black px-3.5 py-2 border-2 border-black rounded transition-all cursor-pointer hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none {{ $loop->first ? 'bg-white hover:bg-zinc-100 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]' : 'bg-yellow-400 hover:bg-yellow-300 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]' }}">
                        <x-icon name="{{ $item['icon'] }}" class="w-4 h-4 stroke-[2.5]" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            @endauth
        </nav>

        {{-- Mobile Menu Toggle --}}
        <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="mobile-nav"
            class="lg:hidden inline-flex items-center justify-center w-10 h-10 shrink-0 bg-white hover:bg-zinc-100 border-2 border-black rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer"
            aria-label="Buka menu navigasi">
            <x-icon name="lucide-menu" x-show="!open" class="w-5 h-5 stroke-[3]" />
            <x-icon name="lucide-x" x-show="open" x-cloak class="w-5 h-5 stroke-[3]" />
        </button>
    </div>

    {{-- Mobile Drawer (dropdown di bawah header) --}}
    <div id="mobile-nav" x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="lg:hidden absolute top-full inset-x-0 bg-white border-b-4 border-black shadow-[0_4px_0_0_rgba(0,0,0,1)] max-h-[calc(100vh-4rem)] overflow-y-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 space-y-2">
            @auth
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}" wire:navigate @click="open = false"
                        class="flex items-center gap-3 px-3.5 py-3 text-sm font-black uppercase text-black border-2 border-black rounded transition-colors cursor-pointer {{ $isActive($item) ? 'bg-yellow-300' : 'bg-white hover:bg-zinc-100' }}">
                        <x-icon name="{{ $item['icon'] }}" class="w-5 h-5 shrink-0 stroke-[2.5]" />
                        <span>{{ $item['label'] }}</span>
                        @if (isset($item['badge']))
                            <span x-data="{ count: {{ $item['badge'] }} }"
                                @if($item['match'] === 'dashboard.inquiries')
                                    @inquiries-updated.window="count = $event.detail.count"
                                @endif
                                x-show="count > 0"
                                x-text="count"
                                class="ml-auto bg-rose-500 text-white border-2 border-black rounded-full px-2 py-0.5 text-[10px] min-w-[24px] text-center"></span>
                        @endif
                    </a>
                @endforeach

                <a href="{{ route('profile.show') }}" wire:navigate @click="open = false"
                    class="flex items-center gap-3 px-3.5 py-3 text-sm font-black uppercase text-black border-2 border-black rounded transition-colors cursor-pointer {{ request()->routeIs('profile.show') ? 'bg-yellow-300' : 'bg-white hover:bg-zinc-100' }}">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="" class="w-5 h-5 rounded-md object-cover border border-black shrink-0" />
                    @else
                        <x-icon name="lucide-user" class="w-5 h-5 shrink-0 stroke-[2.5]" />
                    @endif
                    <span>Profil Saya</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" @click="open = false">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3.5 py-3 text-sm font-black uppercase text-black bg-rose-400 hover:bg-rose-300 border-2 border-black rounded transition-colors cursor-pointer">
                        <x-icon name="lucide-log-out" class="w-5 h-5 shrink-0 stroke-[2.5]" />
                        <span>Keluar</span>
                    </button>
                </form>
            @else
                @foreach ($guestItems as $item)
                    <a href="{{ $item['href'] }}" wire:navigate @click="open = false"
                        class="flex items-center gap-3 px-3.5 py-3 text-sm font-black uppercase text-black border-2 border-black rounded transition-colors cursor-pointer {{ $loop->first ? 'bg-white hover:bg-zinc-100' : 'bg-yellow-400 hover:bg-yellow-300' }}">
                        <x-icon name="{{ $item['icon'] }}" class="w-5 h-5 shrink-0 stroke-[2.5]" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            @endauth
        </div>
    </div>
</header>
