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
                    <x-icon name="lucide-plus" class="w-5 h-5 text-black stroke-[2.5] group-hover:rotate-90 transition-transform duration-300" />
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
                        <x-icon name="lucide-building-2" class="w-7 h-7 stroke-[2]" />
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
                        <x-icon name="lucide-circle-check" class="w-7 h-7 stroke-[2]" />
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
                        <x-icon name="lucide-message-square" class="w-7 h-7 stroke-[2]" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Section List Properti -->
        <div id="property-list-section" class="space-y-6 scroll-mt-20">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border-4 border-black p-5 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-black uppercase tracking-tight flex items-center gap-2">
                        <x-icon name="lucide-building-2" class="w-6 h-6 text-black stroke-[2.5]" />
                        <span>Daftar Properti Kost</span>
                    </h2>
                    <p class="text-xs font-bold text-zinc-600 mt-0.5">Kelola status ketersediaan & informasi properti kost Anda.</p>
                </div>

                <!-- Search Input (Direct live search with clear button for Owner Dashboard) -->
                <div class="relative w-full sm:w-80" x-data="{ query: @entangle('search') }">
                    <input 
                        x-ref="searchInput"
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari nama atau lokasi..." 
                        class="w-full bg-white border-3 border-black rounded-xl pl-10 pr-10 py-2.5 text-xs font-black uppercase text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]"
                    >
                    <x-icon name="lucide-search" class="w-5 h-5 text-black absolute left-3 top-2.5 pointer-events-none stroke-[2.5]" />

                    <!-- Clear Search Input ✕ Button -->
                    <template x-if="query || ($refs.searchInput && $refs.searchInput.value)">
                        <button 
                            type="button" 
                            @click="$refs.searchInput.value = ''; $wire.resetSearch()"
                            class="absolute right-2.5 top-2.5 w-6 h-6 bg-rose-400 hover:bg-rose-300 border-2 border-black rounded text-black font-black text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all flex items-center justify-center cursor-pointer"
                            title="Hapus kata kunci pencarian"
                        >
                            ✕
                        </button>
                    </template>
                </div>
            </div>

            <!-- Grid Card Properties -->
            @if($kosts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($kosts as $kost)
                        <div class="bg-white border-3 border-black rounded-xl overflow-hidden shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 hover:shadow-[7px_7px_0px_0px_rgba(0,0,0,1)] transition-[transform,box-shadow] duration-300 ease-out will-change-transform flex flex-col justify-between group">
                            <div>
                                <!-- Image Header -->
                                <div class="aspect-[4/3] bg-zinc-200 relative overflow-hidden border-b-3 border-black">
                                    @if($kost->primaryImage)
                                        <img src="{{ Str::startsWith($kost->primaryImage->image_path, 'http') ? $kost->primaryImage->image_path : Storage::url($kost->primaryImage->image_path) }}" alt="{{ $kost->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-yellow-100 text-black">
                                            <x-icon name="lucide-image" class="w-12 h-12 stroke-[2]" />
                                        </div>
                                    @endif

                                    <!-- Top Left Badges -->
                                    <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
                                        <span class="px-2.5 py-1 bg-pink-400 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                            {{ $kost->gender_type }}
                                        </span>
                                        @if($kost->boosted_at)
                                            <span class="px-2.5 py-1 bg-yellow-400 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider flex items-center gap-1">
                                                <x-icon name="lucide-zap" fill="#FBBF24" stroke-width="0.8" class="w-3.5 h-3.5 shrink-0" />
                                                <span>Super Boost</span>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Top Right Status Badges -->
                                    <div class="absolute top-3 right-3 flex flex-col items-end gap-1.5">
                                        @if($kost->status === 'pending')
                                            <span class="px-3 py-1 bg-amber-300 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-flex items-center gap-1.5 animate-pulse">
                                                <span class="relative flex h-2 w-2 shrink-0">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-600 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-700"></span>
                                                </span>
                                                <x-icon name="lucide-clock" class="w-3.5 h-3.5 text-black stroke-[2.5] shrink-0" />
                                                <span>Menunggu Review</span>
                                            </span>
                                        @elseif($kost->status === 'rejected')
                                            <span class="px-3 py-1 bg-rose-400 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                                ✕ Ditolak Admin
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-emerald-300 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                                ✓ Tayang Publik
                                            </span>
                                        @endif

                                        @if($kost->is_available)
                                            <span class="px-2.5 py-0.5 bg-lime-400 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                                Sisa {{ $kost->available_rooms ?? 1 }} Kamar
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 bg-rose-400 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                                Penuh
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="p-5 space-y-4">
                                    <div>
                                        <h3 class="text-lg font-black text-black leading-snug line-clamp-1 hover:underline">
                                            <a href="{{ route('kost.show', $kost->slug) }}?from=dashboard">
                                                {{ $kost->name }}
                                            </a>
                                        </h3>
                                        <p class="text-xs font-bold text-zinc-600 mt-1 line-clamp-1 inline-flex items-center gap-1">
                                            <x-icon name="lucide-map-pin" class="w-3.5 h-3.5 text-zinc-700 shrink-0 stroke-[2.5]" />
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
                                                {{ $kost->inquiries_count }} Pesan
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer Actions -->
                            <div class="px-5 py-4 bg-zinc-100 border-t-3 border-black flex flex-wrap items-center justify-between gap-2 shrink-0">
                                <!-- Toggle Availability Button -->
                                <button 
                                    wire:click="toggleAvailability({{ $kost->id }})" 
                                    wire:loading.attr="disabled"
                                    class="h-9 px-3.5 border-2 border-black text-xs font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:brightness-110 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg cursor-pointer flex items-center justify-center shrink-0 whitespace-nowrap min-w-[140px] {{ $kost->is_available ? 'bg-rose-400 hover:bg-rose-300 text-black' : 'bg-lime-400 hover:bg-lime-300 text-black' }}"
                                >
                                    <span wire:loading.remove wire:target="toggleAvailability({{ $kost->id }})" class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                        @if($kost->is_available)
                                            <x-icon name="lucide-circle-slash" class="w-3.5 h-3.5 stroke-[2.5] shrink-0" />
                                            <span>Set Status Penuh</span>
                                        @else
                                            <x-icon name="lucide-circle-check" class="w-3.5 h-3.5 stroke-[2.5] shrink-0" />
                                            <span>Set Status Tersedia</span>
                                        @endif
                                    </span>
                                    <span wire:loading wire:target="toggleAvailability({{ $kost->id }})" class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                        <x-icon name="lucide-loader-circle" class="animate-spin h-3.5 w-3.5 text-black shrink-0" />
                                        <span>Memproses...</span>
                                    </span>
                                </button>

                                <!-- Edit Link Button -->
                                <a 
                                    href="{{ route('dashboard.kost.edit', $kost->slug) }}" 
                                    class="h-9 px-4 bg-cyan-400 hover:bg-cyan-300 text-black border-2 border-black font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:brightness-110 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg inline-flex items-center justify-center gap-1 shrink-0 whitespace-nowrap"
                                >
                                    <span>Edit</span>
                                    <x-icon name="lucide-pencil" class="w-3.5 h-3.5 stroke-[3]" />
                                </a>

                                <!-- Detail Link Button -->
                                <a 
                                    href="{{ route('kost.show', $kost->slug) }}?from=dashboard" 
                                    class="h-9 px-4 bg-orange-400 hover:bg-orange-300 text-black border-2 border-black font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:brightness-110 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg inline-flex items-center justify-center gap-1 shrink-0 whitespace-nowrap"
                                >
                                    <span>Lihat</span>
                                    <x-icon name="lucide-arrow-right" class="w-3.5 h-3.5 stroke-[3]" />
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
                        <x-icon name="lucide-building-2" class="w-8 h-8 stroke-[2]" />
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
                            <x-icon name="lucide-plus" class="w-4 h-4 stroke-[3]" />
                            <span>Tambah Properti Pertama</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>

    </div>
</div>
