<div class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px] pb-16 pt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Section -->
        <div class="bg-white border-4 border-black p-6 sm:p-8 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 bg-yellow-300 text-black border-2 border-black font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        Control Panel Admin
                    </span>
                    <span class="px-3 py-1 bg-lime-400 text-black border-2 border-black font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        Moderasi Iklan Kost
                    </span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-black text-black tracking-tight uppercase leading-none">
                    Panel Moderasi Kost
                </h1>
                <p class="text-zinc-700 text-sm sm:text-base font-bold">
                    Tinjau, disetujui (Approve), atau tolak (Reject) pengajuan iklan kost dari pemilik sebelum ditayangkan secara publik.
                </p>
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
                    }, 4000);
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
            class="fixed bottom-6 right-6 z-50 bg-lime-300 border-4 border-black p-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-black flex items-center gap-3 max-w-md"
        >
            <div class="w-8 h-8 rounded-full bg-black text-lime-300 flex items-center justify-center text-xs font-black shrink-0">
                ✓
            </div>
            <p class="text-xs sm:text-sm font-black text-black leading-snug">
                <span x-text="message"></span>
            </p>
            <button type="button" @click="show = false" class="ml-auto text-black hover:bg-black/10 p-1 rounded font-black text-xs cursor-pointer transition-colors">✕</button>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 sm:gap-6">
            <!-- Pending -->
            <button 
                type="button" 
                wire:click="setTab('pending')" 
                class="text-left p-5 border-3 border-black rounded-xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer {{ $activeTab === 'pending' ? 'bg-amber-300 ring-4 ring-black translate-x-0.5 translate-y-0.5' : 'bg-amber-100 hover:bg-amber-200' }}"
            >
                <p class="text-xs font-black uppercase tracking-wider text-black flex items-center gap-1.5">
                    <span class="relative flex h-2 w-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-600 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-700"></span>
                    </span>
                    <svg class="w-4 h-4 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Menunggu Review</span>
                </p>
                <h3 class="text-3xl sm:text-4xl font-black text-black mt-2 tracking-tight">{{ $pendingCount }}</h3>
                <p class="text-[10px] font-bold text-black/70 mt-1 uppercase">Perlu Tindakan Admin</p>
            </button>

            <!-- Published -->
            <button 
                type="button" 
                wire:click="setTab('published')" 
                class="text-left p-5 border-3 border-black rounded-xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer {{ $activeTab === 'published' ? 'bg-emerald-300 ring-4 ring-black translate-x-0.5 translate-y-0.5' : 'bg-emerald-100 hover:bg-emerald-200' }}"
            >
                <p class="text-xs font-black uppercase tracking-wider text-black">✓ Tayang Publik</p>
                <h3 class="text-3xl sm:text-4xl font-black text-black mt-2 tracking-tight">{{ $publishedCount }}</h3>
                <p class="text-[10px] font-bold text-black/70 mt-1 uppercase">Disetujui Admin</p>
            </button>

            <!-- Rejected -->
            <button 
                type="button" 
                wire:click="setTab('rejected')" 
                class="text-left p-5 border-3 border-black rounded-xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer {{ $activeTab === 'rejected' ? 'bg-rose-300 ring-4 ring-black translate-x-0.5 translate-y-0.5' : 'bg-rose-100 hover:bg-rose-200' }}"
            >
                <p class="text-xs font-black uppercase tracking-wider text-black">✕ Ditolak</p>
                <h3 class="text-3xl sm:text-4xl font-black text-black mt-2 tracking-tight">{{ $rejectedCount }}</h3>
                <p class="text-[10px] font-bold text-black/70 mt-1 uppercase">Tidak Memenuhi Syarat</p>
            </button>

            <!-- Total -->
            <button 
                type="button" 
                wire:click="setTab('all')" 
                class="text-left p-5 border-3 border-black rounded-xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer {{ $activeTab === 'all' ? 'bg-cyan-300 ring-4 ring-black translate-x-0.5 translate-y-0.5' : 'bg-cyan-100 hover:bg-cyan-200' }}"
            >
                <p class="text-xs font-black uppercase tracking-wider text-black">📊 Total Properti</p>
                <h3 class="text-3xl sm:text-4xl font-black text-black mt-2 tracking-tight">{{ $totalCount }}</h3>
                <p class="text-[10px] font-bold text-black/70 mt-1 uppercase">Seluruh Database</p>
            </button>

            <!-- Facilities Pending -->
            <button 
                type="button" 
                wire:click="setTab('facilities')" 
                class="text-left p-5 border-3 border-black rounded-xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer {{ $activeTab === 'facilities' ? 'bg-violet-300 ring-4 ring-black translate-x-0.5 translate-y-0.5' : 'bg-violet-100 hover:bg-violet-200' }}"
            >
                <p class="text-xs font-black uppercase tracking-wider text-black flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>Fasilitas Menunggu</span>
                </p>
                <h3 class="text-3xl sm:text-4xl font-black text-black mt-2 tracking-tight">{{ $pendingFacilityCount }}</h3>
                <p class="text-[10px] font-bold text-black/70 mt-1 uppercase">Perlu Review Admin</p>
            </button>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white border-4 border-black p-5 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex flex-wrap items-center gap-2">
                <button 
                    type="button" 
                    wire:click="setTab('pending')" 
                    class="px-4 py-2 border-2 border-black font-black text-xs uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer {{ $activeTab === 'pending' ? 'bg-amber-400 text-black' : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-700' }}"
                >
                    Menunggu ({{ $pendingCount }})
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('published')" 
                    class="px-4 py-2 border-2 border-black font-black text-xs uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer {{ $activeTab === 'published' ? 'bg-emerald-400 text-black' : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-700' }}"
                >
                    Disetujui ({{ $publishedCount }})
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('rejected')" 
                    class="px-4 py-2 border-2 border-black font-black text-xs uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer {{ $activeTab === 'rejected' ? 'bg-rose-400 text-black' : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-700' }}"
                >
                    Ditolak ({{ $rejectedCount }})
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('all')" 
                    class="px-4 py-2 border-2 border-black font-black text-xs uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer {{ $activeTab === 'all' ? 'bg-cyan-300 text-black' : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-700' }}"
                >
                    Semua ({{ $totalCount }})
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('facilities')" 
                    class="px-4 py-2 border-2 border-black font-black text-xs uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer {{ $activeTab === 'facilities' ? 'bg-violet-400 text-black' : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-700' }}"
                >
                    Fasilitas ({{ $pendingFacilityCount }})
                </button>
            </div>

            <!-- Search Input -->
            <div class="relative w-full sm:w-80" x-data="{ query: @entangle('search') }">
                <input 
                    x-ref="searchInput"
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari nama kost atau pemilik..." 
                    class="w-full bg-white border-3 border-black rounded-xl pl-10 pr-10 py-2 text-xs font-black uppercase text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]"
                >
                <svg class="w-4 h-4 text-black absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>

                <template x-if="query">
                    <button 
                        type="button" 
                        @click="$wire.set('search', '')" 
                        class="absolute right-2.5 top-2 w-5 h-5 bg-rose-400 hover:bg-rose-300 border-2 border-black rounded text-black font-black text-xs flex items-center justify-center cursor-pointer"
                    >
                        ✕
                    </button>
                </template>
            </div>
        </div>

        <!-- Facilities Moderation List -->
        @if($activeTab === 'facilities')
            @if($facilities->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($facilities as $facility)
                        <div class="bg-white border-4 border-black rounded-2xl overflow-hidden shadow-[7px_7px_0px_0px_rgba(0,0,0,1)] flex flex-col justify-between group">
                            <div class="p-5 space-y-4">
                                <!-- Header -->
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-11 h-11 rounded-xl bg-violet-300 border-2 border-black flex items-center justify-center shrink-0 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                            <svg class="w-5 h-5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-lg font-black text-black leading-snug truncate">{{ $facility->name }}</h3>
                                            <span class="text-[10px] font-black uppercase text-zinc-500">Tipe: {{ $facility->type === 'room' ? 'Kamar' : 'Umum' }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 bg-amber-400 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] animate-pulse shrink-0">
                                        ⏳ Pending
                                    </span>
                                </div>

                                <!-- Used By Kosts -->
                                <div class="bg-yellow-50 border-2 border-black p-3 rounded-xl space-y-1.5">
                                    <p class="text-[10px] font-black uppercase text-zinc-500">Diajukan pada {{ $facility->kosts->count() }} kost:</p>
                                    @forelse($facility->kosts->take(3) as $kost)
                                        <div class="flex items-center justify-between gap-2 text-xs font-black text-black">
                                            <span class="truncate">{{ $kost->name }}</span>
                                            <span class="text-[10px] font-bold text-zinc-600 truncate max-w-[120px]">{{ $kost->user->name ?? '-' }}</span>
                                        </div>
                                    @empty
                                        <p class="text-xs font-bold text-zinc-600">Belum dipakai oleh kost mana pun.</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="p-4 bg-zinc-100 border-t-4 border-black grid grid-cols-2 gap-2">
                                <button 
                                    type="button" 
                                    wire:click="approveFacility({{ $facility->id }})" 
                                    wire:loading.attr="disabled"
                                    class="w-full py-3 bg-lime-400 hover:bg-lime-300 active:bg-lime-500 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer"
                                >
                                    <span wire:loading.remove wire:target="approveFacility({{ $facility->id }})">✓ APPROVE</span>
                                    <span wire:loading wire:target="approveFacility({{ $facility->id }})">Memproses...</span>
                                </button>
                                <button 
                                    type="button" 
                                    wire:click="rejectFacility({{ $facility->id }})" 
                                    wire:loading.attr="disabled"
                                    class="w-full py-3 bg-rose-500 hover:bg-rose-400 active:bg-rose-600 text-white border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer"
                                >
                                    <span wire:loading.remove wire:target="rejectFacility({{ $facility->id }})">✕ REJECT</span>
                                    <span wire:loading wire:target="rejectFacility({{ $facility->id }})">Memproses...</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $facilities->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-yellow-100 border-4 border-black rounded-2xl p-12 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-4">
                    <div class="w-20 h-20 bg-white border-3 border-black rounded-2xl flex items-center justify-center mx-auto text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] -rotate-3">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-black text-black uppercase">Tidak Ada Fasilitas Pending</h3>
                        <p class="text-sm font-bold text-zinc-700 max-w-md mx-auto mt-2">
                            Semua fasilitas custom telah selesai ditinjau. Fasilitas baru akan muncul di sini setelah pemilik kost mengajukannya.
                        </p>
                    </div>
                </div>
            @endif
        @else
        <!-- Moderation List Grid -->
        @if($kosts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($kosts as $kost)
                    <div class="bg-white border-4 border-black rounded-2xl overflow-hidden shadow-[7px_7px_0px_0px_rgba(0,0,0,1)] flex flex-col justify-between group">
                        <div>
                            <!-- Thumbnail & Badges Header -->
                            <div class="aspect-[16/9] bg-zinc-200 relative overflow-hidden border-b-4 border-black">
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
                                    <span class="px-2.5 py-1 bg-cyan-300 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                        {{ $kost->district }}
                                    </span>
                                </div>

                                <!-- Top Right Moderation Status Badge -->
                                <div class="absolute top-3 right-3">
                                    @if($kost->status === 'pending')
                                        <span class="px-3 py-1 bg-amber-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] animate-pulse">
                                            ⏳ Pending Review
                                        </span>
                                    @elseif($kost->status === 'published')
                                        <span class="px-3 py-1 bg-emerald-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                            ✓ Published
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-rose-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                            ✕ Rejected
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Body Content -->
                            <div class="p-5 space-y-4">
                                <div>
                                    <h3 class="text-xl font-black text-black leading-snug line-clamp-1 hover:underline">
                                        <a href="{{ route('kost.show', $kost->slug) }}" target="_blank">
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

                                <!-- Owner Info Box -->
                                <div class="bg-yellow-50 border-2 border-black p-3 rounded-xl space-y-1">
                                    <p class="text-[10px] font-black uppercase text-zinc-500">Pemilik Kost (Landlord)</p>
                                    <div class="flex items-center justify-between text-xs font-black text-black">
                                        <span>👤 {{ $kost->user->name ?? 'Pemilik Tanpa Nama' }}</span>
                                        <span class="text-[11px] font-bold text-zinc-600 truncate max-w-[140px]">{{ $kost->user->email ?? '-' }}</span>
                                    </div>
                                </div>

                                <!-- Price & Room Metrics Grid -->
                                <div class="pt-2 border-t-2 border-black grid grid-cols-2 gap-2 text-center">
                                    <div class="bg-yellow-300 border-2 border-black p-2 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                        <p class="text-[9px] font-black uppercase text-black">Harga Sewa</p>
                                        <p class="text-xs font-black text-black mt-0.5">Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}/bln</p>
                                    </div>

                                    <div class="bg-cyan-300 border-2 border-black p-2 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                        <p class="text-[9px] font-black uppercase text-black">Kapasitas Kamar</p>
                                        <p class="text-xs font-black text-black mt-0.5">{{ $kost->available_rooms ?? 0 }} / {{ $kost->total_rooms ?? 0 }} Kamar</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Buttons (Tactile Neo-Brutalist Actions) -->
                        <div class="p-4 bg-zinc-100 border-t-4 border-black flex flex-col gap-2">
                            @if($kost->status === 'pending')
                                <div class="grid grid-cols-2 gap-2">
                                    <!-- Approve Button -->
                                    <button 
                                        type="button" 
                                        wire:click="approve({{ $kost->id }})" 
                                        wire:loading.attr="disabled"
                                        class="w-full py-3 bg-lime-400 hover:bg-lime-300 active:bg-lime-500 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer flex items-center justify-center gap-1.5"
                                    >
                                        <span wire:loading.remove wire:target="approve({{ $kost->id }})" class="inline-flex items-center gap-1">
                                            <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <span>TERIMA & TAYANGKAN</span>
                                        </span>
                                        <span wire:loading wire:target="approve({{ $kost->id }})">Memproses...</span>
                                    </button>

                                    <!-- Reject Button -->
                                    <button 
                                        type="button" 
                                        wire:click="reject({{ $kost->id }})" 
                                        wire:loading.attr="disabled"
                                        class="w-full py-3 bg-rose-500 hover:bg-rose-400 active:bg-rose-600 text-white border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer flex items-center justify-center gap-1.5"
                                    >
                                        <span wire:loading.remove wire:target="reject({{ $kost->id }})" class="inline-flex items-center gap-1">
                                            <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span>TOLAK IKLAN</span>
                                        </span>
                                        <span wire:loading wire:target="reject({{ $kost->id }})">Memproses...</span>
                                    </button>
                                </div>
                            @elseif($kost->status === 'published')
                                <button 
                                    type="button" 
                                    wire:click="reject({{ $kost->id }})" 
                                    class="w-full py-2.5 bg-rose-400 hover:bg-rose-300 text-black border-2 border-black font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] rounded-lg cursor-pointer inline-flex items-center justify-center gap-1.5"
                                >
                                    <svg class="w-3.5 h-3.5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    <span>Tarik Dari Publikasi (Tolak)</span>
                                </button>
                            @else
                                <button 
                                    type="button" 
                                    wire:click="approve({{ $kost->id }})" 
                                    class="w-full py-2.5 bg-lime-400 hover:bg-lime-300 text-black border-2 border-black font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] rounded-lg cursor-pointer inline-flex items-center justify-center gap-1.5"
                                >
                                    <svg class="w-3.5 h-3.5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span>Setujui Kembali (Tayangkan)</span>
                                </button>
                            @endif
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
            <div class="bg-yellow-100 border-4 border-black rounded-2xl p-12 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-4">
                <div class="w-20 h-20 bg-white border-3 border-black rounded-2xl flex items-center justify-center mx-auto text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] -rotate-3">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-black uppercase">Tidak Ada Iklan Dalam Status Ini</h3>
                    <p class="text-sm font-bold text-zinc-700 max-w-md mx-auto mt-2">
                        @if($activeTab === 'pending')
                            Bagus! Semua pengajuan iklan kost telah selesai ditinjau. Tidak ada draf pending saat ini.
                        @else
                            Tidak ditemukan properti kost dalam filter status ini.
                        @endif
                    </p>
                </div>
            </div>
        @endif
        @endif

    </div>
</div>
