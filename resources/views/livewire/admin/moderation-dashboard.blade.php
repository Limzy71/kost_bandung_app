<div class="min-h-screen bg-[#f8f9fa] dark:bg-zinc-950 bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)] bg-[size:24px_24px] pb-16 pt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Section -->
        <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 p-6 sm:p-8 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 bg-yellow-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                        Control Panel Admin
                    </span>
                    <span class="px-3 py-1 bg-lime-400 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                        Moderasi Iklan Kost
                    </span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-black text-black dark:text-white tracking-tight uppercase leading-none">
                    Panel Moderasi Kost
                </h1>
                <p class="text-zinc-700 dark:text-zinc-300 text-sm sm:text-base font-bold">
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
            class="fixed bottom-6 right-6 z-50 bg-lime-300 border-4 border-black dark:border-zinc-700 p-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] text-black flex items-center gap-3 max-w-md"
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
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 sm:gap-6">
            <!-- Pending -->
            <button 
                type="button" 
                wire:click="setTab('pending')" 
                class="text-left p-5 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $activeTab === 'pending' ? 'bg-amber-300 ring-4 ring-black dark:bg-amber-950/40 dark:ring-amber-300 translate-x-0.5 translate-y-0.5' : 'bg-amber-100 dark:bg-amber-950/40 hover:bg-amber-200 dark:hover:bg-amber-950/40' }}"
            >
                <p class="text-xs font-black uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                    <span class="relative flex h-2 w-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-600 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-700"></span>
                    </span>
                    <x-icon name="lucide-hourglass" class="w-4 h-4 text-black stroke-[2.5]" />
                    <span>Menunggu Review</span>
                </p>
                <h3 class="text-3xl sm:text-4xl font-black text-black dark:text-white mt-2 tracking-tight">{{ $pendingCount }}</h3>
                <p class="text-[10px] font-bold text-black/70 dark:text-zinc-300 mt-1 uppercase">Perlu Tindakan Admin</p>
            </button>

            <!-- Published -->
            <button 
                type="button" 
                wire:click="setTab('published')" 
                class="text-left p-5 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $activeTab === 'published' ? 'bg-emerald-300 ring-4 ring-black dark:bg-emerald-950/40 dark:ring-emerald-300 translate-x-0.5 translate-y-0.5' : 'bg-emerald-100 dark:bg-emerald-950/40 hover:bg-emerald-200 dark:hover:bg-emerald-950/40' }}"
            >
                <p class="text-xs font-black uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                    <x-icon name="lucide-check-circle-2" class="w-4 h-4 text-black stroke-[2.5]" />
                    <span>Tayang Publik</span>
                </p>
                <h3 class="text-3xl sm:text-4xl font-black text-black dark:text-white mt-2 tracking-tight">{{ $publishedCount }}</h3>
                <p class="text-[10px] font-bold text-black/70 dark:text-zinc-300 mt-1 uppercase">Disetujui Admin</p>
            </button>

            <!-- Rejected -->
            <button 
                type="button" 
                wire:click="setTab('rejected')" 
                class="text-left p-5 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $activeTab === 'rejected' ? 'bg-rose-300 ring-4 ring-black dark:bg-rose-950/40 dark:ring-rose-300 translate-x-0.5 translate-y-0.5' : 'bg-rose-100 dark:bg-rose-950/40 hover:bg-rose-200 dark:hover:bg-rose-950/40' }}"
            >
                <p class="text-xs font-black uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                    <x-icon name="lucide-x-circle" class="w-4 h-4 text-black stroke-[2.5]" />
                    <span>Ditolak</span>
                </p>
                <h3 class="text-3xl sm:text-4xl font-black text-black dark:text-white mt-2 tracking-tight">{{ $rejectedCount }}</h3>
                <p class="text-[10px] font-bold text-black/70 dark:text-zinc-300 mt-1 uppercase">Tidak Memenuhi Syarat</p>
            </button>

            <!-- Total -->
            <button 
                type="button" 
                wire:click="setTab('all')" 
                class="text-left p-5 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $activeTab === 'all' ? 'bg-cyan-300 ring-4 ring-black dark:bg-cyan-950/40 dark:ring-cyan-300 translate-x-0.5 translate-y-0.5' : 'bg-cyan-100 dark:bg-cyan-950/40 hover:bg-cyan-200 dark:hover:bg-cyan-950/40' }}"
            >
                <p class="text-xs font-black uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                    <x-icon name="lucide-building-2" class="w-4 h-4 text-black stroke-[2.5]" />
                    <span>Total Properti</span>
                </p>
                <h3 class="text-3xl sm:text-4xl font-black text-black dark:text-white mt-2 tracking-tight">{{ $totalCount }}</h3>
                <p class="text-[10px] font-bold text-black/70 dark:text-zinc-300 mt-1 uppercase">Seluruh Database</p>
            </button>

            <!-- Facilities Pending -->
            <button 
                type="button" 
                wire:click="setTab('facilities')" 
                class="text-left p-5 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $activeTab === 'facilities' ? 'bg-violet-300 ring-4 ring-black dark:bg-violet-950/40 dark:ring-violet-300 translate-x-0.5 translate-y-0.5' : 'bg-violet-100 dark:bg-violet-950/40 hover:bg-violet-200 dark:hover:bg-violet-950/40' }}"
            >
                <p class="text-xs font-black uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                    <x-icon name="lucide-layers" class="w-4 h-4 text-black stroke-[2.5]" />
                    <span>Fasilitas Menunggu</span>
                </p>
                <h3 class="text-3xl sm:text-4xl font-black text-black dark:text-white mt-2 tracking-tight">{{ $pendingFacilityCount }}</h3>
                <p class="text-[10px] font-bold text-black/70 dark:text-zinc-300 mt-1 uppercase">Perlu Review Admin</p>
            </button>

            <!-- Verification Pending -->
            <button 
                type="button" 
                wire:click="setTab('verification')" 
                class="text-left p-5 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $activeTab === 'verification' ? 'bg-teal-300 ring-4 ring-black dark:bg-teal-950/40 dark:ring-teal-300 translate-x-0.5 translate-y-0.5' : 'bg-teal-100 dark:bg-teal-950/40 hover:bg-teal-200 dark:hover:bg-teal-950/40' }}"
            >
                <p class="text-xs font-black uppercase tracking-wider text-black dark:text-white flex items-center gap-1.5">
                    <span class="relative flex h-2 w-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-600 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-700"></span>
                    </span>
                    <x-icon name="lucide-shield-check" class="w-4 h-4 text-black stroke-[2.5]" />
                    <span>Verifikasi Dokumen</span>
                </p>
                <h3 class="text-3xl sm:text-4xl font-black text-black dark:text-white mt-2 tracking-tight">{{ $verificationCount }}</h3>
                <p class="text-[10px] font-bold text-black/70 dark:text-zinc-300 mt-1 uppercase">KTP &amp; Bukti Kepemilikan</p>
            </button>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 p-5 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex flex-wrap items-center gap-2">
                <button 
                    type="button" 
                    wire:click="setTab('pending')" 
                    class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $activeTab === 'pending' ? 'bg-amber-400 text-black' : 'bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}"
                >
                    Menunggu ({{ $pendingCount }})
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('published')" 
                    class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $activeTab === 'published' ? 'bg-emerald-400 text-black' : 'bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}"
                >
                    Disetujui ({{ $publishedCount }})
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('rejected')" 
                    class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $activeTab === 'rejected' ? 'bg-rose-400 text-black' : 'bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}"
                >
                    Ditolak ({{ $rejectedCount }})
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('all')" 
                    class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $activeTab === 'all' ? 'bg-cyan-300 text-black' : 'bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}"
                >
                    Semua ({{ $totalCount }})
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('facilities')" 
                    class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $activeTab === 'facilities' ? 'bg-violet-400 text-black' : 'bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}"
                >
                    Fasilitas ({{ $pendingFacilityCount }})
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('verification')" 
                    class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $activeTab === 'verification' ? 'bg-teal-400 text-black' : 'bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}"
                >
                    Verifikasi ({{ $verificationCount }})
                </button>
            </div>

            <!-- Search Input -->
            <div class="relative w-full sm:w-80" x-data="{ query: @entangle('search') }">
                <input 
                    x-ref="searchInput"
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari nama kost atau pemilik..." 
                    class="w-full bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl pl-10 pr-10 py-2 text-xs font-black uppercase text-black dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]"
                >
                <x-icon name="lucide-search" class="w-4 h-4 text-black dark:text-white absolute left-3 top-2.5 pointer-events-none" />

                <template x-if="query">
                    <button 
                        type="button" 
                        @click="$wire.set('search', '')" 
                        class="absolute right-2.5 top-2 w-5 h-5 bg-rose-400 hover:bg-rose-300 border-2 border-black dark:border-zinc-700 rounded text-black font-black text-xs flex items-center justify-center cursor-pointer"
                    >
                        ✕
                    </button>
                </template>
            </div>
        </div>

        <!-- Verification Review List -->
        @if($activeTab === 'verification')
            <div x-data="{
                rejectOpen: false,
                rejectType: '',
                rejectId: null,
                rejectReason: '',
                openReject(type, id) {
                    this.rejectType = type;
                    this.rejectId = id;
                    this.rejectReason = '';
                    this.rejectOpen = true;
                },
                confirmReject() {
                    if (this.rejectId === null) return;
                    $wire.submitReject(this.rejectType, this.rejectId, this.rejectReason);
                    this.rejectOpen = false;
                }
            }" @keydown.escape.window="rejectOpen = false">
                <!-- Info Banner -->
                <div class="bg-teal-100 dark:bg-teal-950/40 border-4 border-black dark:border-zinc-700 p-5 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] space-y-2">
                    <p class="text-xs font-black uppercase text-black dark:text-white flex items-center gap-2">
                        <x-icon name="lucide-shield-check" class="w-5 h-5 stroke-[2.5]" />
                        Verifikasi Dokumen Kepemilikan
                    </p>
                    <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300 leading-relaxed">
                        Tinjau dokumen <span class="font-black text-black dark:text-white">KTP pemilik</span> dan <span class="font-black text-black dark:text-white">bukti kepemilikan properti</span> yang diajukan pemilik untuk badge "Terverifikasi".
                        Dokumen hanya dapat diakses oleh admin dan tidak pernah ditampilkan ke publik. Kost tetap dapat ditayangkan tanpa dokumen — verifikasi ini bersifat sukarela.
                    </p>
                </div>

                <!-- Pending Identity (KTP) -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-2xl font-black text-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                            <x-icon name="lucide-id-card" class="w-6 h-6 stroke-[2.5]" />
                            Identitas Pemilik (KTP) Menunggu
                        </h2>
                        <span class="px-3 py-1 bg-amber-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                            {{ $pendingIdentities->count() }} Dokumen
                        </span>
                    </div>

                    @if($pendingIdentities->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($pendingIdentities as $verUser)
                                <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl overflow-hidden shadow-[7px_7px_0px_0px_rgba(0,0,0,1)] dark:shadow-[7px_7px_0px_0px_rgba(255,255,255,0.25)] flex flex-col justify-between">
                                    <div class="p-5 space-y-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-11 h-11 rounded-xl bg-teal-300 border-2 border-black dark:border-zinc-700 flex items-center justify-center shrink-0 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                                    <x-icon name="lucide-user-check" class="w-5 h-5 text-black stroke-[2.5]" />
                                                </div>
                                                <div class="min-w-0">
                                                    <h3 class="text-lg font-black text-black dark:text-white leading-snug truncate">{{ $verUser->name }}</h3>
                                                    <p class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400">Pemilik Kost</p>
                                                </div>
                                            </div>
                                            <span class="px-2.5 py-1 bg-amber-400 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1 animate-pulse shrink-0">
                                                <x-icon name="lucide-hourglass" class="w-3 h-3 stroke-[2.5]" />
                                                Pending
                                            </span>
                                        </div>

                                        <div class="bg-zinc-50 dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 p-3 rounded-xl space-y-1.5 text-xs font-bold text-black dark:text-white">
                                            <p class="truncate flex items-center gap-1.5">
                                                <x-icon name="lucide-mail" class="w-3.5 h-3.5 stroke-[2.5] shrink-0" />
                                                <span class="truncate">{{ $verUser->email }}</span>
                                            </p>
                                            <p class="truncate flex items-center gap-1.5">
                                                <x-icon name="lucide-phone" class="w-3.5 h-3.5 stroke-[2.5] shrink-0" />
                                                <span class="truncate">{{ $verUser->phone_number ?? '-' }}</span>
                                            </p>
                                            @if($verUser->business_name)
                                                <p class="truncate flex items-center gap-1.5">
                                                    <x-icon name="lucide-building-2" class="w-3.5 h-3.5 stroke-[2.5] shrink-0" />
                                                    <span class="truncate">{{ $verUser->business_name }}</span>
                                                </p>
                                            @endif
                                        </div>

                                        <a href="{{ route('admin.verification.document', ['kind' => 'identity', 'id' => $verUser->id]) }}" target="_blank"
                                            class="w-full py-3 bg-cyan-300 hover:bg-cyan-200 dark:hover:bg-cyan-950/40 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2 cursor-pointer">
                                            <x-icon name="lucide-eye" class="w-4 h-4 stroke-[2.5]" />
                                            Lihat Dokumen KTP
                                        </a>
                                    </div>

                                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border-t-4 border-black dark:border-zinc-700 grid grid-cols-2 gap-2">
                                        <button type="button" wire:click="approveIdentity({{ $verUser->id }})"
                                            class="w-full py-3 bg-lime-400 hover:bg-lime-300 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                                            ✓ SETUJUI
                                        </button>
                                        <button type="button" @click="openReject('identity', {{ $verUser->id }})"
                                            class="w-full py-3 bg-rose-500 hover:bg-rose-400 text-white border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                                            ✕ TOLAK
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-emerald-100 dark:bg-emerald-950/40 border-4 border-black dark:border-zinc-700 rounded-2xl p-8 text-center shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]">
                            <div class="w-14 h-14 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl flex items-center justify-center mx-auto text-black dark:text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                                <x-icon name="lucide-badge-check" class="w-7 h-7" />
                            </div>
                            <h3 class="text-xl font-black text-black dark:text-white uppercase mt-3">Tidak Ada KTP Menunggu</h3>
                            <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300 mt-1">Semua dokumen identitas pemilik telah selesai ditinjau.</p>
                        </div>
                    @endif
                </div>

                <!-- Pending Ownership Documents -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-2xl font-black text-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                            <x-icon name="lucide-file-text" class="w-6 h-6 stroke-[2.5]" />
                            Bukti Kepemilikan Menunggu
                        </h2>
                        <span class="px-3 py-1 bg-amber-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                            {{ $pendingOwnerships->count() }} Dokumen
                        </span>
                    </div>

                    @if($pendingOwnerships->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($pendingOwnerships as $verKost)
                                <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl overflow-hidden shadow-[7px_7px_0px_0px_rgba(0,0,0,1)] dark:shadow-[7px_7px_0px_0px_rgba(255,255,255,0.25)] flex flex-col justify-between">
                                    <div>
                                        <div class="aspect-[16/9] bg-zinc-200 dark:bg-zinc-800 relative overflow-hidden border-b-4 border-black dark:border-zinc-700">
                                            @if($verKost->primaryImage)
                                                <img src="{{ Str::startsWith($verKost->primaryImage->image_path, 'http') ? $verKost->primaryImage->image_path : Storage::url($verKost->primaryImage->image_path) }}" alt="{{ $verKost->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-teal-100 dark:bg-teal-950/40 text-black dark:text-white">
                                                    <x-icon name="lucide-image" class="w-12 h-12" />
                                                </div>
                                            @endif
                                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-amber-400 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1 animate-pulse">
                                                <x-icon name="lucide-hourglass" class="w-3 h-3 stroke-[2.5]" />
                                                Pending
                                            </span>
                                        </div>
                                        <div class="p-5 space-y-3">
                                            <div>
                                                <h3 class="text-lg font-black text-black dark:text-white leading-snug line-clamp-1">{{ $verKost->name }}</h3>
                                                <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-1 inline-flex items-center gap-1">
                                                    <x-icon name="lucide-map-pin" class="w-3.5 h-3.5 text-zinc-700 dark:text-zinc-300 shrink-0 stroke-[2.5]" />
                                                    {{ $verKost->district }} &middot; {{ $verKost->user->name ?? 'Pemilik Tanpa Nama' }}
                                                </p>
                                            </div>
                                            <div class="bg-yellow-50 dark:bg-zinc-800 border-2 border-black dark:border-zinc-700 p-3 rounded-xl flex items-center gap-2">
                                                <x-icon name="lucide-file-check" class="w-4 h-4 text-black stroke-[2.5] shrink-0" />
                                                <span class="text-xs font-black text-black dark:text-white">{{ $verKost->ownershipDocTypeLabel() }}</span>
                                            </div>
                                            <a href="{{ route('admin.verification.document', ['kind' => 'ownership', 'id' => $verKost->id]) }}" target="_blank"
                                                class="w-full py-3 bg-cyan-300 hover:bg-cyan-200 dark:hover:bg-cyan-950/40 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2 cursor-pointer">
                                                <x-icon name="lucide-eye" class="w-4 h-4 stroke-[2.5]" />
                                                Lihat Dokumen Kepemilikan
                                            </a>
                                        </div>
                                    </div>

                                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border-t-4 border-black dark:border-zinc-700 grid grid-cols-2 gap-2">
                                        <button type="button" wire:click="approveOwnership({{ $verKost->id }})"
                                            class="w-full py-3 bg-lime-400 hover:bg-lime-300 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                                            ✓ SETUJUI
                                        </button>
                                        <button type="button" @click="openReject('ownership', {{ $verKost->id }})"
                                            class="w-full py-3 bg-rose-500 hover:bg-rose-400 text-white border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                                            ✕ TOLAK
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-emerald-100 dark:bg-emerald-950/40 border-4 border-black dark:border-zinc-700 rounded-2xl p-8 text-center shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]">
                            <div class="w-14 h-14 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl flex items-center justify-center mx-auto text-black dark:text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                                <x-icon name="lucide-badge-check" class="w-7 h-7" />
                            </div>
                            <h3 class="text-xl font-black text-black dark:text-white uppercase mt-3">Tidak Ada Dokumen Menunggu</h3>
                            <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300 mt-1">Semua bukti kepemilikan telah selesai ditinjau.</p>
                        </div>
                    @endif
                </div>

                <!-- Reject Reason Modal -->
                <div x-show="rejectOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" @click.self="rejectOpen = false">
                    <div class="absolute inset-0 bg-black/60"></div>
                    <div class="relative bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] w-full max-w-md space-y-4"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-lg font-black text-black dark:text-white uppercase flex items-center gap-2">
                                <x-icon name="lucide-x-circle" class="w-5 h-5 text-rose-600 dark:text-rose-400 stroke-[2.5]" />
                                Tolak Verifikasi Dokumen
                            </h3>
                            <button type="button" @click="rejectOpen = false"
                                class="w-8 h-8 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-black dark:text-white border-2 border-black dark:border-zinc-700 rounded font-black text-sm cursor-pointer">✕</button>
                        </div>
                        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400">Alasan penolakan akan ditampilkan kepada pemilik sebagai panduan untuk memperbaiki dokumen.</p>
                        <textarea x-model="rejectReason" rows="3" maxlength="300"
                            placeholder="Contoh: Foto KTP buram / tidak terbaca. Nama pada dokumen tidak sesuai. Sertakan dokumen yang lebih jelas..."
                            class="w-full bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl px-4 py-3 text-sm font-bold text-black dark:text-white focus:outline-none focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all"></textarea>
                        <p class="text-[10px] font-bold italic text-zinc-500 dark:text-zinc-400">Kosongkan untuk menggunakan alasan default.</p>
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <button type="button" @click="rejectOpen = false"
                                class="py-3 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-black dark:text-white border-3 border-black dark:border-zinc-700 font-black text-xs uppercase rounded-xl cursor-pointer">Batal</button>
                            <button type="button" @click="confirmReject()"
                                class="py-3 bg-rose-500 hover:bg-rose-400 text-white border-3 border-black dark:border-zinc-700 font-black text-xs uppercase rounded-xl cursor-pointer shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                                Konfirmasi Tolak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
        <!-- Facilities Moderation List -->
        @if($activeTab === 'facilities')
            @if($facilities->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($facilities as $facility)
                        <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl overflow-hidden shadow-[7px_7px_0px_0px_rgba(0,0,0,1)] dark:shadow-[7px_7px_0px_0px_rgba(255,255,255,0.25)] flex flex-col justify-between group">
                            <div class="p-5 space-y-4">
                                <!-- Header -->
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-11 h-11 rounded-xl bg-violet-300 border-2 border-black dark:border-zinc-700 flex items-center justify-center shrink-0 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                            <x-icon name="lucide-{{ $facility->icon ?: 'box' }}" class="w-5 h-5 text-black stroke-[2.5]" />
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-lg font-black text-black dark:text-white leading-snug truncate">{{ $facility->name }}</h3>
                                            <span class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400">Tipe: {{ $facility->type === 'room' ? 'Kamar' : 'Umum' }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 bg-amber-400 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1 animate-pulse shrink-0">
                                        <x-icon name="lucide-hourglass" class="w-3 h-3 stroke-[2.5]" />
                                        <span>Pending</span>
                                    </span>
                                </div>

                                <!-- Used By Kosts -->
                                <div class="bg-yellow-50 dark:bg-zinc-800 border-2 border-black dark:border-zinc-700 p-3 rounded-xl space-y-1.5">
                                    <p class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400">Diajukan pada {{ $facility->kosts->count() }} kost:</p>
                                    @forelse($facility->kosts->take(3) as $kost)
                                        <div class="flex items-center justify-between gap-2 text-xs font-black text-black dark:text-white">
                                            <span class="truncate">{{ $kost->name }}</span>
                                            <span class="text-[10px] font-bold text-zinc-600 dark:text-zinc-400 truncate max-w-[120px]">{{ $kost->user->name ?? '-' }}</span>
                                        </div>
                                    @empty
                                        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400">Belum dipakai oleh kost mana pun.</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border-t-4 border-black dark:border-zinc-700 grid grid-cols-2 gap-2">
                                <button 
                                    type="button" 
                                    wire:click="approveFacility({{ $facility->id }})" 
                                    wire:loading.attr="disabled"
                                    class="w-full py-3 bg-lime-400 hover:bg-lime-300 active:bg-lime-500 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer"
                                >
                                    <span wire:loading.remove wire:target="approveFacility({{ $facility->id }})">✓ APPROVE</span>
                                    <span wire:loading wire:target="approveFacility({{ $facility->id }})">Memproses...</span>
                                </button>
                                <button 
                                    type="button" 
                                    wire:click="rejectFacility({{ $facility->id }})" 
                                    wire:loading.attr="disabled"
                                    class="w-full py-3 bg-rose-500 hover:bg-rose-400 active:bg-rose-600 text-white border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer"
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
                <div class="bg-yellow-100 dark:bg-yellow-950/40 border-4 border-black dark:border-zinc-700 rounded-2xl p-12 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] space-y-4">
                    <div class="w-20 h-20 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-2xl flex items-center justify-center mx-auto text-black dark:text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] -rotate-3">
                        <x-icon name="lucide-circle-check" class="w-10 h-10" />
                    </div>
                    <div>
                        <h3 class="text-3xl font-black text-black dark:text-white uppercase">Tidak Ada Fasilitas Pending</h3>
                        <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300 max-w-md mx-auto mt-2">
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
                    <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl overflow-hidden shadow-[7px_7px_0px_0px_rgba(0,0,0,1)] dark:shadow-[7px_7px_0px_0px_rgba(255,255,255,0.25)] flex flex-col justify-between group">
                        <div>
                            <!-- Thumbnail & Badges Header -->
                            <div class="aspect-[16/9] bg-zinc-200 dark:bg-zinc-800 relative overflow-hidden border-b-4 border-black dark:border-zinc-700">
                                @if($kost->primaryImage)
                                    <img src="{{ Str::startsWith($kost->primaryImage->image_path, 'http') ? $kost->primaryImage->image_path : Storage::url($kost->primaryImage->image_path) }}" alt="{{ $kost->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-yellow-100 dark:bg-yellow-950/40 text-black dark:text-white">
                                        <x-icon name="lucide-image" class="w-12 h-12" />
                                    </div>
                                @endif

                                <!-- Top Left Badges -->
                                <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
                                    <span class="px-2.5 py-1 bg-pink-400 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] tracking-wider">
                                        {{ $kost->gender_type }}
                                    </span>
                                    <span class="px-2.5 py-1 bg-cyan-300 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] tracking-wider">
                                        {{ $kost->district }}
                                    </span>
                                </div>

                                <!-- Top Right Moderation Status Badge -->
                                <div class="absolute top-3 right-3">
                                    @if($kost->status === 'pending')
                                        <span class="px-3 py-1 bg-amber-400 text-black border-2 border-black dark:border-zinc-700 text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1.5 animate-pulse">
                                            <x-icon name="lucide-hourglass" class="w-3.5 h-3.5 stroke-[2.5]" />
                                            <span>Pending Review</span>
                                        </span>
                                    @elseif($kost->status === 'published')
                                        <span class="px-3 py-1 bg-emerald-400 text-black border-2 border-black dark:border-zinc-700 text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1.5">
                                            <x-icon name="lucide-check-circle-2" class="w-3.5 h-3.5 stroke-[2.5]" />
                                            <span>Disetujui</span>
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-rose-400 text-black border-2 border-black dark:border-zinc-700 text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1.5">
                                            <x-icon name="lucide-x-circle" class="w-3.5 h-3.5 stroke-[2.5]" />
                                            <span>Ditolak</span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Body Content -->
                            <div class="p-5 space-y-4">
                                <div>
                                    <h3 class="text-xl font-black text-black dark:text-white leading-snug line-clamp-1 hover:underline">
                                        <a href="{{ route('kost.show', $kost->slug) }}?from=moderation">
                                            {{ $kost->name }}
                                        </a>
                                    </h3>
                                    <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-1 line-clamp-1 inline-flex items-center gap-1">
                                        <x-icon name="lucide-map-pin" class="w-3.5 h-3.5 text-zinc-700 dark:text-zinc-300 shrink-0 stroke-[2.5]" />
                                        <span>{{ $kost->address }}, {{ $kost->district }}</span>
                                    </p>
                                </div>

                                <!-- Owner Info Box -->
                                <div class="bg-yellow-50 dark:bg-zinc-800 border-2 border-black dark:border-zinc-700 p-3 rounded-xl space-y-1">
                                    <p class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400">Pemilik Kost (Landlord)</p>
                                    <div class="flex items-center justify-between text-xs font-black text-black dark:text-white">
                                        <span class="inline-flex items-center gap-1.5">
                                            <x-icon name="lucide-user" class="w-3.5 h-3.5 stroke-[2.5]" />
                                            {{ $kost->user->name ?? 'Pemilik Tanpa Nama' }}
                                        </span>
                                        <span class="text-[11px] font-bold text-zinc-600 dark:text-zinc-400 truncate max-w-[140px]">{{ $kost->user->email ?? '-' }}</span>
                                    </div>
                                </div>

                                <!-- Price & Room Metrics Grid -->
                                <div class="pt-2 border-t-2 border-black dark:border-zinc-700 grid grid-cols-2 gap-2 text-center">
                                    <div class="bg-yellow-300 border-2 border-black dark:border-zinc-700 p-2 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                        <p class="text-[9px] font-black uppercase text-black">Harga Sewa</p>
                                        <p class="text-xs font-black text-black mt-0.5">Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}{{ \App\Models\Kost::rentPeriodUnit($kost->rent_period) }}</p>
                                    </div>

                                    <div class="bg-cyan-300 border-2 border-black dark:border-zinc-700 p-2 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                        <p class="text-[9px] font-black uppercase text-black">Kapasitas Kamar</p>
                                        <p class="text-xs font-black text-black mt-0.5">{{ $kost->available_rooms ?? 0 }} / {{ $kost->total_rooms ?? 0 }} Kamar</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Buttons (Tactile Neo-Brutalist Actions) -->
                        <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border-t-4 border-black dark:border-zinc-700 flex flex-col gap-2.5">
                            <!-- View Full Detail Button for Admin -->
                            <a 
                                href="{{ route('kost.show', $kost->slug) }}?from=moderation" 
                                class="w-full py-2.5 bg-cyan-300 hover:bg-cyan-200 dark:hover:bg-cyan-950/40 active:bg-cyan-400 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer flex items-center justify-center gap-1.5"
                            >
                                <x-icon name="lucide-eye" class="w-4 h-4 stroke-[2.5]" />
                                <span>Lihat Detail & Pratinjau Lengkap</span>
                            </a>

                            @if($kost->status === 'pending')
                                <div class="grid grid-cols-2 gap-2">
                                    <!-- Approve Button -->
                                    <button 
                                        type="button" 
                                        wire:click="approve({{ $kost->id }})" 
                                        wire:loading.attr="disabled"
                                        class="w-full py-3 bg-lime-400 hover:bg-lime-300 active:bg-lime-500 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer flex items-center justify-center gap-1.5"
                                    >
                                        <span wire:loading.remove wire:target="approve({{ $kost->id }})" class="inline-flex items-center gap-1">
                                            <x-icon name="lucide-check" class="w-4 h-4 stroke-[3]" />
                                            <span>TERIMA & TAYANGKAN</span>
                                        </span>
                                        <span wire:loading wire:target="approve({{ $kost->id }})">Memproses...</span>
                                    </button>

                                    <!-- Reject Button -->
                                    <button 
                                        type="button" 
                                        wire:click="reject({{ $kost->id }})" 
                                        wire:loading.attr="disabled"
                                        class="w-full py-3 bg-rose-500 hover:bg-rose-400 active:bg-rose-600 text-white border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer flex items-center justify-center gap-1.5"
                                    >
                                        <span wire:loading.remove wire:target="reject({{ $kost->id }})" class="inline-flex items-center gap-1">
                                            <x-icon name="lucide-x" class="w-4 h-4 stroke-[3]" />
                                            <span>TOLAK IKLAN</span>
                                        </span>
                                        <span wire:loading wire:target="reject({{ $kost->id }})">Memproses...</span>
                                    </button>
                                </div>
                            @elseif($kost->status === 'published')
                                <button 
                                    type="button" 
                                    wire:click="reject({{ $kost->id }})" 
                                    class="w-full py-2.5 bg-rose-400 hover:bg-rose-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] rounded-lg cursor-pointer inline-flex items-center justify-center gap-1.5"
                                >
                                    <x-icon name="lucide-circle-slash" class="w-3.5 h-3.5 stroke-[3]" />
                                    <span>Tarik Dari Publikasi (Tolak)</span>
                                </button>
                            @else
                                <button 
                                    type="button" 
                                    wire:click="approve({{ $kost->id }})" 
                                    class="w-full py-2.5 bg-lime-400 hover:bg-lime-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] rounded-lg cursor-pointer inline-flex items-center justify-center gap-1.5"
                                >
                                    <x-icon name="lucide-check" class="w-3.5 h-3.5 stroke-[3]" />
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
            <div class="bg-yellow-100 dark:bg-yellow-950/40 border-4 border-black dark:border-zinc-700 rounded-2xl p-12 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] space-y-4">
                <div class="w-20 h-20 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-2xl flex items-center justify-center mx-auto text-black dark:text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] -rotate-3">
                    <x-icon name="lucide-circle-check" class="w-10 h-10" />
                </div>
                <div>
                    <h3 class="text-3xl font-black text-black dark:text-white uppercase">Tidak Ada Iklan Dalam Status Ini</h3>
                    <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300 max-w-md mx-auto mt-2">
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
        @endif

    </div>
</div>
