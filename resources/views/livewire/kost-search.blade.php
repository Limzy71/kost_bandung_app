<div 
    x-data 
    x-init="window.scrollTo({ top: 0, behavior: 'auto' })"
    @scroll-to-home-list.window="document.getElementById('home-list-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
    class="space-y-8"
>
    <!-- Filter Bar Neo-Brutalist -->
    <div 
        x-data="{ 
            hasFilter: false,
            checkFilter() {
                this.hasFilter = Boolean(
                    (this.$refs.searchInput && this.$refs.searchInput.value.trim() !== '') ||
                    (this.$refs.genderSelect && this.$refs.genderSelect.value !== '') ||
                    (this.$refs.districtSelect && this.$refs.districtSelect.value !== '') ||
                    (this.$refs.minSelect && this.$refs.minSelect.value !== '') ||
                    (this.$refs.maxSelect && this.$refs.maxSelect.value !== '') ||
                    $wire.search || $wire.gender || $wire.district || $wire.price_min || $wire.price_max
                );
            },
            resetFormLocally() {
                if (this.$refs.searchInput) this.$refs.searchInput.value = '';
                if (this.$refs.genderSelect) this.$refs.genderSelect.value = '';
                if (this.$refs.districtSelect) this.$refs.districtSelect.value = '';
                if (this.$refs.minSelect) this.$refs.minSelect.value = '';
                if (this.$refs.maxSelect) this.$refs.maxSelect.value = '';
                $wire.search = '';
                $wire.gender = '';
                $wire.district = '';
                $wire.price_min = '';
                $wire.price_max = '';
                this.checkFilter();
            }
        }"
        x-init="checkFilter()"
        @input="checkFilter()"
        @change="checkFilter()"
        class="bg-white border-4 border-black p-6 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-4"
    >
        <!-- Header Row (Title & Action Buttons Side by Side) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b-3 border-black pb-3.5 gap-3">
            <!-- Header Title -->
            <h2 class="text-base sm:text-lg font-black text-black uppercase tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>Filter Pencarian Kost</span>
            </h2>

            <!-- Header Action Buttons (Aligned with Header Text) -->
            <div class="flex items-center gap-2.5 shrink-0 self-end sm:self-auto">
                <!-- Reset Filter Button (Local-only form reset - NO automatic server request) -->
                <button 
                    x-show="hasFilter"
                    x-cloak
                    type="button" 
                    @click="resetFormLocally()"
                    class="bg-rose-400 hover:bg-rose-300 text-black border-2 border-black font-black text-xs uppercase px-3.5 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap"
                >
                    <svg class="w-3.5 h-3.5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset Filter</span>
                </button>

                <!-- Terapkan Filter Button (Submits deferred filter criteria to server) -->
                <button 
                    type="button" 
                    wire:click="applyFilters" 
                    class="bg-lime-400 hover:bg-lime-300 text-black border-2 border-black font-black text-xs uppercase px-4 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap"
                >
                    <svg class="w-3.5 h-3.5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </div>

        <!-- Filter Inputs Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search Input -->
            <div class="lg:col-span-2 relative">
                <label class="block text-xs font-black uppercase text-black mb-1.5">Cari Nama / Jalan</label>
                <div class="relative flex items-center" x-data="{ query: @entangle('search') }">
                    <input 
                        x-ref="searchInput"
                        wire:model.live.debounce.300ms="search" 
                        wire:keydown.enter="applyFilters"
                        type="text" 
                        placeholder="Contoh: Dago, Cisitu, Setiabudi..."
                        class="w-full bg-white border-3 border-black rounded-xl pl-10 pr-10 py-2.5 text-xs font-black uppercase text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]"
                    >
                    <svg class="w-5 h-5 text-black absolute left-3 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>

                    <!-- Clear Search Input ✕ Button (Clears search locally - NO server request) -->
                    <template x-if="query || ($refs.searchInput && $refs.searchInput.value)">
                        <button 
                            type="button" 
                            @click="$refs.searchInput.value = ''; $wire.search = ''; checkFilter()"
                            class="absolute right-2.5 w-6 h-6 bg-rose-400 hover:bg-rose-300 border-2 border-black rounded text-black font-black text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all flex items-center justify-center cursor-pointer"
                            title="Hapus kata kunci pencarian"
                        >
                            ✕
                        </button>
                    </template>
                </div>
            </div>

            <!-- Gender Select -->
            <div>
                <label class="block text-xs font-black uppercase text-black mb-1.5">Tipe Penghuni</label>
                <select 
                    x-ref="genderSelect"
                    wire:model="gender"
                    class="w-full bg-white border-3 border-black rounded-xl px-3 py-2.5 text-xs font-black uppercase text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] cursor-pointer transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-[length:16px_16px] bg-no-repeat bg-[right_12px_center] pr-9"
                >
                    <option value="" class="font-black uppercase text-black">Semua Tipe</option>
                    <option value="putra" class="font-black uppercase text-black">Putra</option>
                    <option value="putri" class="font-black uppercase text-black">Putri</option>
                    <option value="campur" class="font-black uppercase text-black">Campur</option>
                </select>
            </div>

            <!-- District Select -->
            <div>
                <label class="block text-xs font-black uppercase text-black mb-1.5">Kecamatan</label>
                <select 
                    x-ref="districtSelect"
                    wire:model="district"
                    class="w-full bg-white border-3 border-black rounded-xl px-3 py-2.5 text-xs font-black uppercase text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] cursor-pointer transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-[length:16px_16px] bg-no-repeat bg-[right_12px_center] pr-9"
                >
                    <option value="" class="font-black uppercase text-black">Semua Kecamatan</option>
                    @foreach ($districts as $dist)
                        <option value="{{ $dist }}" class="font-black uppercase text-black">{{ $dist }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Price Min & Max Select -->
            <div>
                <label class="block text-xs font-black uppercase text-black mb-1.5">Batas Harga Sewa</label>
                <div class="grid grid-cols-2 gap-2">
                    <select 
                        x-ref="minSelect"
                        wire:model="price_min"
                        class="w-full bg-white border-3 border-black rounded-xl px-2 py-2.5 text-xs font-black uppercase text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] cursor-pointer transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-[length:14px_14px] bg-no-repeat bg-[right_6px_center] pr-6"
                    >
                        <option value="" class="font-black uppercase text-black">Min</option>
                        <option value="500000" class="font-black uppercase text-black">500rb</option>
                        <option value="1000000" class="font-black uppercase text-black">1 Jt</option>
                        <option value="1500000" class="font-black uppercase text-black">1,5 Jt</option>
                        <option value="2000000" class="font-black uppercase text-black">2 Jt</option>
                        <option value="3000000" class="font-black uppercase text-black">3 Jt</option>
                    </select>

                    <select 
                        x-ref="maxSelect"
                        wire:model="price_max"
                        class="w-full bg-white border-3 border-black rounded-xl px-2 py-2.5 text-xs font-black uppercase text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] cursor-pointer transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-[length:14px_14px] bg-no-repeat bg-[right_6px_center] pr-6"
                    >
                        <option value="" class="font-black uppercase text-black">Max</option>
                        <option value="1000000" class="font-black uppercase text-black">1 Jt</option>
                        <option value="1500000" class="font-black uppercase text-black">1,5 Jt</option>
                        <option value="2000000" class="font-black uppercase text-black">2 Jt</option>
                        <option value="3000000" class="font-black uppercase text-black">3 Jt</option>
                        <option value="5000000" class="font-black uppercase text-black">5 Jt</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid List Kost Neo-Brutalist -->
    <div id="home-list-section" class="relative scroll-mt-20">
        <!-- Loading Overlay Targeted -->
        <div wire:loading.delay wire:target="applyFilters, resetFilters" class="absolute inset-0 bg-white/70 backdrop-blur-xs z-30 flex items-center justify-center rounded-2xl border-4 border-black">
            <div class="bg-yellow-300 border-3 border-black px-6 py-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex items-center gap-3">
                <svg class="animate-spin h-6 w-6 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="font-black text-black text-sm uppercase tracking-wide">Memuat Hunian...</span>
            </div>
        </div>

        @if($kosts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($kosts as $kost)
                    <div class="bg-white border-3 border-black rounded-xl overflow-hidden shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 hover:shadow-[7px_7px_0px_0px_rgba(0,0,0,1)] transition-all flex flex-col justify-between group">
                        <div>
                            <!-- Image Header -->
                            <div 
                                class="aspect-[4/3] bg-zinc-200 relative overflow-hidden border-b-3 border-black cursor-pointer"
                                onclick="window.location.href='{{ route('kost.show', $kost->slug) }}'"
                            >
                                @if ($kost->primaryImage)
                                    <img 
                                        src="{{ Str::startsWith($kost->primaryImage->image_path, 'http') ? $kost->primaryImage->image_path : Storage::url($kost->primaryImage->image_path) }}"
                                        alt="{{ $kost->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-yellow-100 text-black">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Top Left Badges with Gold-Orange Gradient SVG Icon & Black Stroke -->
                                <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
                                    <span class="px-2.5 py-1 bg-pink-400 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                        {{ $kost->gender_type }}
                                    </span>
                                    @if ($kost->boosted_at)
                                        <span class="px-2.5 py-1 bg-yellow-400 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 20 20">
                                                <defs>
                                                    <linearGradient id="bolt-grad-card-{{ $kost->id }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                                        <stop offset="0%" stop-color="#FBBF24" />
                                                        <stop offset="100%" stop-color="#F97316" />
                                                    </linearGradient>
                                                </defs>
                                                <path fill="url(#bolt-grad-card-{{ $kost->id }})" stroke="#000000" stroke-width="0.8" fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Super Boost</span>
                                        </span>
                                    @endif
                                </div>

                                <!-- Top Right District Badge with SVG Location Pin -->
                                <div class="absolute top-3 right-3">
                                    <span class="px-2.5 py-1 bg-cyan-300 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider inline-flex items-center gap-1">
                                        <svg class="w-3 h-3 text-black shrink-0 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $kost->district }}</span>
                                    </span>
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
                                    <p class="text-xs font-bold text-zinc-600 mt-1 line-clamp-1">
                                        {{ $kost->address }}
                                    </p>
                                </div>

                                <!-- Price & Facilities (Single-line Price Fix) -->
                                <div class="pt-3 border-t-2 border-black flex items-center justify-between gap-2 overflow-hidden">
                                    <div class="shrink-0">
                                        <p class="text-[10px] font-black uppercase text-zinc-500">Harga Sewa</p>
                                        <span class="bg-yellow-300 border-2 border-black font-black text-black px-2.5 py-1 rounded text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-flex items-center whitespace-nowrap mt-0.5">
                                            Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}<span class="text-[10px] font-bold ml-0.5">/bln</span>
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap justify-end gap-1 overflow-hidden shrink min-w-0">
                                        @if ($kost->facilities && $kost->facilities->count() > 0)
                                            @foreach ($kost->facilities->take(2) as $facility)
                                                <span class="bg-zinc-100 border-2 border-black text-[10px] font-bold text-black px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] truncate max-w-[110px]">
                                                    {{ $facility->name }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="px-5 py-4 bg-zinc-100 border-t-3 border-black flex items-center justify-between">
                            <span class="text-xs font-extrabold text-lime-700 bg-lime-200 border-2 border-black px-2.5 py-1 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase">
                                ✓ Siap Huni
                            </span>

                            <a 
                                href="{{ route('kost.show', $kost->slug) }}" 
                                class="px-4 py-2 bg-orange-400 hover:bg-orange-300 text-black border-2 border-black font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1"
                            >
                                <span>Lihat Detail</span>
                                <svg class="w-3.5 h-3.5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $kosts->links() }}
            </div>
        @else
            <!-- Empty State Neo-Brutalist -->
            <div class="bg-yellow-100 border-4 border-black rounded-2xl p-12 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-4">
                <div class="w-20 h-20 bg-white border-3 border-black rounded-2xl flex items-center justify-center mx-auto text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] -rotate-3">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-black uppercase">Tidak Ada Hunian Ditemukan</h3>
                    <p class="text-sm font-bold text-zinc-700 max-w-md mx-auto mt-2">
                        @if($search || $gender || $district || $price_min || $price_max)
                            Tidak ada kost yang cocok dengan kriteria filter Anda. Coba reset filter atau ubah kata kunci pencarian.
                        @else
                            Belum ada daftar kost yang terdaftar saat ini.
                        @endif
                    </p>
                </div>
                <button 
                    type="button"
                    wire:click="resetFilters" 
                    @click="if($refs.searchInput) $refs.searchInput.value = ''; setTimeout(() => checkFilter(), 150)"
                    class="px-6 py-3 bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl inline-flex items-center gap-2 cursor-pointer"
                >
                    <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Reset Semua Filter</span>
                </button>
            </div>
        @endif
    </div>
</div>
