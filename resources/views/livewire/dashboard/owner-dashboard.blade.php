<div 
    x-data 
    x-init="window.scrollTo({ top: 0, behavior: 'auto' })"
    @scroll-to-list.window="document.getElementById('property-list-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
    class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px]"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-white p-6 md:p-8 border-4 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] rounded-xl">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 bg-yellow-300 text-black border-2 border-black font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        Portal Pemilik
                    </span>
                    @if($owner->role === 'owner')
                        <span class="px-3 py-1 bg-lime-400 text-black border-2 border-black font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            Akun Terverifikasi
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-black tracking-tight uppercase">
                    Dashboard Pemilik
                </h1>
                <p class="text-zinc-700 text-sm md:text-base font-bold">
                    Selamat datang kembali, <span class="bg-yellow-200 border-b-2 border-black px-1">{{ $owner->name }}</span>! Kelola iklan & ketersediaan kost Anda.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.kost.create') }}" class="bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black font-black text-sm uppercase px-6 py-3.5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-1 active:translate-y-1 active:shadow-none transition-all inline-flex items-center gap-2 rounded-lg group">
                    <svg class="w-5 h-5 text-black group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Kost Baru</span>
                </a>
            </div>
        </div>

        <!-- Floating Auto-Dismiss Toast Notification -->
        <div 
            x-data="{
                show: false,
                message: '',
                timer: null,
                trigger(msg) {
                    this.message = msg;
                    this.show = true;
                    if (this.timer) clearTimeout(this.timer);
                    this.timer = setTimeout(() => {
                        this.show = false;
                    }, 3000);
                }
            }"
            x-on:show-toast.window="trigger($event.detail.message)"
            x-show="show" 
            x-cloak
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="fixed bottom-6 right-6 z-50 bg-lime-300 border-3 border-black p-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-black flex items-center gap-3 max-w-md"
        >
            <div class="w-7 h-7 rounded-full bg-black text-lime-300 flex items-center justify-center text-xs font-black shrink-0">
                ✓
            </div>
            <p class="text-xs font-bold text-black leading-relaxed">
                <span x-text="message"></span>
            </p>
            <button type="button" @click="show = false" class="ml-auto text-black hover:bg-black/10 p-1 rounded font-black text-xs cursor-pointer transition-colors">✕</button>
        </div>

        <!-- Quick Stats Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Total Properti -->
            <div class="bg-cyan-300 border-3 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] rounded-xl relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-black">Total Properti</p>
                        <h3 class="text-4xl font-black text-black mt-2 tracking-tighter">{{ $totalProperti }}</h3>
                        <p class="text-xs font-bold text-black/80 mt-1">Kost terdaftar dalam sistem</p>
                    </div>
                    <div class="w-14 h-14 rounded-lg bg-white border-2 border-black flex items-center justify-center text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card 2: Status Kamar / Properti Siap Huni -->
            <div class="bg-lime-300 border-3 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] rounded-xl relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-black">Ketersediaan Kamar</p>
                        <h3 class="text-4xl font-black text-black mt-2 tracking-tighter">{{ $totalKamarTersedia }} <span class="text-sm font-bold text-black/70">/ {{ $totalProperti }} Kost</span></h3>
                        <span class="text-xs font-black text-black bg-white border-2 border-black px-2.5 py-0.5 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-block mt-2 uppercase">Status Siap Huni</span>
                    </div>
                    <div class="w-14 h-14 rounded-lg bg-white border-2 border-black flex items-center justify-center text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card 3: Pesan Masuk / Inquiry -->
            <div class="bg-pink-300 border-3 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] rounded-xl relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-black">Pesan Masuk</p>
                        <h3 class="text-4xl font-black text-black mt-2 tracking-tighter">{{ $pesanMasuk }}</h3>
                        <p class="text-xs font-bold text-black/80 mt-1">Pertanyaan dari calon penyewa</p>
                    </div>
                    <div class="w-14 h-14 rounded-lg bg-white border-2 border-black flex items-center justify-center text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section List Properti -->
        <div id="property-list-section" class="space-y-6 scroll-mt-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border-3 border-black p-5 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <div>
                    <h2 class="text-2xl font-black text-black uppercase tracking-tight">Daftar Properti Kost</h2>
                    <p class="text-xs font-bold text-zinc-600">Kelola status ketersediaan & informasi properti kost Anda.</p>
                </div>

                <!-- Search Filter Input -->
                <div class="relative w-full sm:w-80" x-data>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari nama atau lokasi..." 
                        class="w-full bg-white border-2 border-black rounded-lg pl-10 pr-10 py-2.5 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all"
                    >
                    <svg class="w-5 h-5 text-black absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>

                    <!-- Clear 'X' Button -->
                    <button 
                        type="button" 
                        x-show="$wire.search && $wire.search.length > 0"
                        x-cloak
                        @click="$wire.resetSearch()"
                        class="absolute right-2.5 top-2.5 w-6 h-6 bg-rose-400 hover:bg-rose-300 border-2 border-black rounded text-black font-black text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all flex items-center justify-center cursor-pointer"
                        title="Hapus Pencarian"
                    >
                        ✕
                    </button>
                </div>
            </div>

            <!-- Grid Card Properties -->
            @if($kosts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($kosts as $kost)
                        <div class="bg-white border-3 border-black rounded-xl overflow-hidden shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 hover:shadow-[7px_7px_0px_0px_rgba(0,0,0,1)] transition-all flex flex-col justify-between group">
                            <div>
                                <!-- Image Header -->
                                <div class="aspect-[4/3] bg-zinc-200 relative overflow-hidden border-b-3 border-black">
                                    @if($kost->primaryImage)
                                        <img src="{{ Str::startsWith($kost->primaryImage->image_path, 'http') ? $kost->primaryImage->image_path : Storage::url($kost->primaryImage->image_path) }}" alt="{{ $kost->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-yellow-100 text-black">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif

                                    <!-- Top Left Badges -->
                                    <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
                                        <span class="px-2.5 py-1 bg-pink-400 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                            {{ $kost->gender_type }}
                                        </span>
                                        @if($kost->boosted_at)
                                            <span class="px-2.5 py-1 bg-yellow-400 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 20 20">
                                                    <defs>
                                                        <linearGradient id="bolt-grad-owner-{{ $kost->id }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                                            <stop offset="0%" stop-color="#FBBF24" />
                                                            <stop offset="100%" stop-color="#F97316" />
                                                        </linearGradient>
                                                    </defs>
                                                    <path fill="url(#bolt-grad-owner-{{ $kost->id }})" stroke="#000000" stroke-width="0.8" fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd" />
                                                </svg>
                                                <span>Sundul</span>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Top Right Status Badge -->
                                    <div class="absolute top-3 right-3">
                                        @if($kost->is_available)
                                            <span class="px-3 py-1 bg-lime-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                                Tersedia
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-rose-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                                Penuh
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="p-5 space-y-4">
                                    <div>
                                        <h3 class="text-lg font-black text-black leading-snug line-clamp-1 hover:underline">
                                            <a href="{{ route('kost.show', $kost->slug) }}">
                                                {{ $kost->name }}
                                            </a>
                                        </h3>
                                        <p class="text-xs font-bold text-zinc-600 mt-1 line-clamp-1 inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-zinc-700 shrink-0 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span>{{ $kost->address }}, {{ $kost->district }}</span>
                                        </p>
                                    </div>

                                    <!-- Price & Facilities -->
                                    <div class="pt-3 border-t-2 border-black flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-black uppercase text-zinc-500">Harga Sewa</p>
                                            <span class="bg-yellow-300 border-2 border-black font-black text-black px-2.5 py-0.5 rounded text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-block mt-0.5">
                                                Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}<span class="text-[10px] font-bold">/bln</span>
                                            </span>
                                        </div>

                                        <div class="text-right">
                                            <p class="text-[10px] font-black uppercase text-zinc-500">Pesan Masuk</p>
                                            <span class="bg-cyan-300 border-2 border-black font-black text-black px-2.5 py-0.5 rounded text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-block mt-0.5">
                                                {{ $kost->inquiries->count() }} Pesan
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer Actions -->
                            <div class="px-5 py-4 bg-zinc-100 border-t-3 border-black flex items-center justify-between gap-2">
                                <!-- Toggle Availability Button -->
                                <button 
                                    wire:click="toggleAvailability({{ $kost->id }})" 
                                    wire:loading.attr="disabled"
                                    class="px-3.5 py-2 border-2 border-black text-xs font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer flex items-center gap-1.5 {{ $kost->is_available ? 'bg-rose-400 hover:bg-rose-300 text-black' : 'bg-lime-400 hover:bg-lime-300 text-black' }}"
                                >
                                    <span wire:loading.remove wire:target="toggleAvailability({{ $kost->id }})" class="inline-flex items-center gap-1.5">
                                        @if($kost->is_available)
                                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            <span>Set Status Penuh</span>
                                        @else
                                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Set Status Tersedia</span>
                                        @endif
                                    </span>
                                    <span wire:loading wire:target="toggleAvailability({{ $kost->id }})" class="inline-flex items-center gap-1.5">
                                        <svg class="animate-spin h-3.5 w-3.5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>Memproses...</span>
                                    </span>
                                </button>

                                <!-- Detail Link Button -->
                                <a 
                                    href="{{ route('kost.show', $kost->slug) }}" 
                                    class="px-4 py-2 bg-orange-400 hover:bg-orange-300 text-black border-2 border-black font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded inline-flex items-center gap-1"
                                >
                                    <span>Lihat</span>
                                    <svg class="w-3.5 h-3.5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $kosts->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-yellow-100 border-3 border-black rounded-xl p-12 text-center shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-4">
                    <div class="w-16 h-16 bg-white border-2 border-black rounded-lg flex items-center justify-center mx-auto text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-black uppercase">Belum Ada Properti Kost</h3>
                        <p class="text-sm font-bold text-zinc-700 max-w-md mx-auto mt-1">
                            @if($search)
                                Tidak ada properti kost yang cocok dengan kata kunci "{{ $search }}".
                            @else
                                Anda belum memiliki properti kost yang terdaftar. Mulai tambahkan properti pertama Anda untuk menarik calon penyewa di Bandung.
                            @endif
                        </p>
                    </div>
                    @if($search)
                        <button wire:click="$set('search', '')" class="px-5 py-2.5 bg-white hover:bg-zinc-50 text-black font-black text-xs uppercase border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded">
                            Reset Pencarian
                        </button>
                    @else
                        <a href="{{ route('dashboard.kost.create') }}" class="px-6 py-3 bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all inline-flex items-center gap-2 rounded-lg">
                            <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Properti Pertama</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>

    </div>
</div>
