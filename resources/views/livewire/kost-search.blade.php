<div
    x-data="catalogMap()"
    x-init="init()"
    data-maps-key="{{ $googleMapsApiKey }}"
    @scroll-to-home-list.window="document.getElementById('home-list-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
    class="space-y-8"
>
    <!-- Filter Bar Neo-Brutalist -->
    <div
        x-data="{
            hasFilter: false,
            wasApplied: false,
            checkFilter() {
                const g = this.$refs.genderSelect   ? this.$refs.genderSelect.value   : '';
                const d = this.$refs.districtSelect ? this.$refs.districtSelect.value : '';
                const n = this.$refs.minSelect      ? this.$refs.minSelect.value      : '';
                const x = this.$refs.maxSelect      ? this.$refs.maxSelect.value      : '';
                const s = this.$refs.searchInput    ? this.$refs.searchInput.value    : '';
                this.hasFilter = Boolean(g || d || n || x || s);
            }
        }"
        x-init="checkFilter()"
        @input="checkFilter()"
        @change="checkFilter()"
        class="bg-white border-4 border-black p-6 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-4"
    >
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b-3 border-black pb-3.5 gap-3">
            <h2 class="text-base sm:text-lg font-black text-black uppercase tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>Filter Pencarian Kost</span>
            </h2>
            <div class="flex items-center gap-2.5 shrink-0 self-end sm:self-auto">
                <button x-show="hasFilter" x-cloak type="button" wire:click="resetFilters" @click="hasFilter = false; wasApplied = false"
                    class="bg-rose-400 hover:bg-rose-300 text-black border-2 border-black font-black text-xs uppercase px-3.5 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset Filter</span>
                </button>
                <button type="button" wire:click="applyFilters" @click="wasApplied = true"
                    class="bg-lime-400 hover:bg-lime-300 text-black border-2 border-black font-black text-xs uppercase px-4 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </div>

        <!-- Filter Inputs Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4">
            <!-- Search Text -->
            <div class="lg:col-span-5 relative">
                <label class="block text-xs font-black uppercase text-black mb-1.5">Cari Nama / Jalan</label>
                <div class="relative flex items-center" x-data="{ query: @entangle('search') }">
                    <input
                        x-ref="searchInput"
                        wire:model="search"
                        wire:keydown.enter="applyFilters"
                        @keydown.enter="wasApplied = true"
                        @input="checkFilter()"
                        type="text"
                        placeholder="Contoh: Dago, Cisitu, Setiabudi..."
                        class="w-full bg-white border-3 border-black rounded-xl pl-10 pr-10 py-2.5 text-xs font-black uppercase text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]"
                    >
                    <svg class="w-5 h-5 text-black absolute left-3 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <template x-if="query || ($refs.searchInput && $refs.searchInput.value)">
                        <button type="button"
                            @click="$refs.searchInput.value=''; $wire.search=''; checkFilter(); if(wasApplied){$wire.applyFilters();}"
                            class="absolute right-2.5 w-6 h-6 bg-rose-400 hover:bg-rose-300 border-2 border-black rounded text-black font-black text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all flex items-center justify-center cursor-pointer">
                            &#x2715;
                        </button>
                    </template>
                </div>
            </div>

            <!-- Gender -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-black uppercase text-black mb-1.5 tracking-wide">Tipe Penghuni</label>
                <select x-ref="genderSelect" wire:model="gender"
                    class="w-full bg-white border-3 border-black rounded-xl px-2.5 py-2.5 text-xs font-black uppercase tracking-wide text-black focus:outline-none focus:ring-0 cursor-pointer transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] bg-[length:14px_14px] bg-no-repeat bg-[right_8px_center] pr-7">
                    <option value="" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Semua Tipe</option>
                    <option value="putra" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Putra</option>
                    <option value="putri" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Putri</option>
                    <option value="campur" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Campur</option>
                </select>
            </div>

            <!-- District -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-black uppercase text-black mb-1.5 tracking-wide">Kecamatan</label>
                <select x-ref="districtSelect" wire:model="district"
                    class="w-full bg-white border-3 border-black rounded-xl px-2.5 py-2.5 text-xs font-black uppercase tracking-wide text-black focus:outline-none focus:ring-0 cursor-pointer transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] bg-[length:14px_14px] bg-no-repeat bg-[right_8px_center] pr-7">
                    <option value="" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Semua Kecamatan</option>
                    @foreach ($districts as $dist)
                        <option value="{{ $dist }}" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">{{ $dist }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Price Range -->
            <div class="lg:col-span-3">
                <label class="block text-xs font-black uppercase text-black mb-1.5 tracking-wide">Batas Harga Sewa</label>
                <div class="grid grid-cols-2 gap-2">
                    <select x-ref="minSelect" wire:model="price_min"
                        class="w-full bg-white border-3 border-black rounded-xl px-2 py-2.5 text-xs font-black uppercase tracking-wide text-black focus:outline-none focus:ring-0 cursor-pointer transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-no-repeat bg-[right_6px_center] pr-5">
                        <option value="" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Min (Semua)</option>
                        <option value="500000" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Rp 500rb</option>
                        <option value="1000000" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Rp 1 Jt</option>
                        <option value="1500000" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Rp 1.5 Jt</option>
                        <option value="2000000" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Rp 2 Jt</option>
                        <option value="3000000" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Rp 3 Jt</option>
                    </select>
                    <select x-ref="maxSelect" wire:model="price_max"
                        class="w-full bg-white border-3 border-black rounded-xl px-2 py-2.5 text-xs font-black uppercase tracking-wide text-black focus:outline-none focus:ring-0 cursor-pointer transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-no-repeat bg-[right_6px_center] pr-5">
                        <option value="" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Max (Semua)</option>
                        <option value="1000000" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Rp 1 Jt</option>
                        <option value="1500000" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Rp 1.5 Jt</option>
                        <option value="2000000" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Rp 2 Jt</option>
                        <option value="3000000" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Rp 3 Jt</option>
                        <option value="5000000" class="font-bold text-sm normal-case text-zinc-900 bg-white py-2">Rp 5 Jt</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Title & Layout Switcher -->
    <div id="home-list-section" class="scroll-mt-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h3 class="text-xl font-black text-black uppercase tracking-tight">Daftar Properti Kost</h3>
            <span class="px-3 py-1 bg-yellow-300 border-2 border-black font-black text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase">
                {{ $kosts->total() }} Ditemukan
            </span>
        </div>

        @if($kosts->count() > 0)
        <!-- 2-Mode View Switcher — only shown when there are results -->
        <div wire:key="view-switcher" class="flex items-center gap-1.5 bg-white border-3 border-black p-1 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] w-full sm:w-auto">
            <button type="button"
                @click="viewMode = 'list'"
                :class="viewMode === 'list' ? 'bg-yellow-400 text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'text-zinc-600 hover:text-black'"
                class="flex-1 sm:flex-initial px-4 py-2 rounded-lg font-black text-xs uppercase transition-all cursor-pointer flex items-center justify-center gap-2">
                <span>&#128203; Lihat Daftar</span>
            </button>
            <button type="button"
                @click="viewMode = 'map'; $nextTick(() => { window.dispatchEvent(new Event('resize')); })"
                :class="viewMode === 'map' ? 'bg-yellow-400 text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'text-zinc-600 hover:text-black'"
                class="flex-1 sm:flex-initial px-4 py-2 rounded-lg font-black text-xs uppercase transition-all cursor-pointer flex items-center justify-center gap-2">
                <span>&#128506; Lihat Peta</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <div class="relative">
        <!-- Loading Overlay -->
        <div wire:loading.delay wire:target="applyFilters, resetFilters"
            class="absolute inset-0 bg-white/70 backdrop-blur-xs z-30 flex items-center justify-center rounded-2xl border-4 border-black">
            <div class="bg-yellow-300 border-3 border-black px-6 py-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex items-center gap-3">
                <svg class="animate-spin h-6 w-6 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="font-black text-black text-sm uppercase tracking-wide">Memuat Hunian...</span>
            </div>
        </div>

        @if($kosts->count() > 0)
            <!-- List View (Default Mode: Balanced 3-column grid at 100% container width) -->
            <div wire:key="list-view" x-show="viewMode === 'list'" x-cloak class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($kosts as $kost)
                        <div class="bg-white border-3 border-black rounded-xl overflow-hidden shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 hover:shadow-[7px_7px_0px_0px_rgba(0,0,0,1)] transition-all flex flex-col justify-between group">
                            <div>
                                <!-- Image -->
                                <div class="aspect-[4/3] bg-zinc-200 relative overflow-hidden border-b-3 border-black cursor-pointer"
                                    onclick="window.location.href='{{ route('kost.show', $kost->slug) }}'">
                                    @if ($kost->primaryImage)
                                        <img src="{{ Str::startsWith($kost->primaryImage->image_path, 'http') ? $kost->primaryImage->image_path : Storage::url($kost->primaryImage->image_path) }}"
                                            alt="{{ $kost->name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-yellow-100 text-black">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <!-- Top Left Badges -->
                                    <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
                                        <span class="px-2.5 py-1 bg-pink-400 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                            {{ $kost->gender_type }}
                                        </span>
                                        @if ($kost->boosted_at)
                                            <span class="px-2.5 py-1 bg-yellow-400 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider flex items-center gap-1">
                                                &#9889; Super Boost
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Top Right District -->
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2.5 py-1 bg-cyan-300 text-black border-2 border-black text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 shrink-0 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span>{{ $kost->district }}</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-5 space-y-4">
                                    <div>
                                        <h3 class="text-lg font-black text-black leading-snug line-clamp-1 hover:underline">
                                            <a href="{{ route('kost.show', $kost->slug) }}">{{ $kost->name }}</a>
                                        </h3>
                                        <p class="text-xs font-bold text-zinc-600 mt-1 line-clamp-1">{{ $kost->address }}</p>
                                    </div>
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

                            <!-- Footer -->
                            <div class="px-5 py-4 bg-zinc-100 border-t-3 border-black flex items-center justify-between">
                                <span class="text-xs font-extrabold text-lime-700 bg-lime-200 border-2 border-black px-2.5 py-1 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase">
                                    &#10003; Siap Huni
                                </span>
                                <a href="{{ route('kost.show', $kost->slug) }}"
                                    class="px-4 py-2 bg-orange-400 hover:bg-orange-300 text-black border-2 border-black font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1">
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
            </div>
        @else
            <!-- Empty State: Reset viewMode to list so stale Alpine state doesn't cause overlap on next search -->
            <div wire:key="empty-state" x-init="viewMode = 'list'" class="bg-yellow-100 border-4 border-black rounded-2xl p-12 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-4">
                <div class="w-20 h-20 bg-white border-3 border-black rounded-2xl flex items-center justify-center mx-auto text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] -rotate-3">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-black uppercase">Tidak Ada Hunian Ditemukan</h3>
                    <p class="text-sm font-bold text-zinc-700 max-w-md mx-auto mt-2">
                        @if($search && !$gender && !$district && !$price_min && !$price_max)
                            Tidak ada kost yang cocok dengan kata kunci "{{ $search }}". Coba hapus pencarian atau gunakan kata kunci lain.
                        @elseif($search || $gender || $district || $price_min || $price_max)
                            Tidak ada kost yang cocok dengan kriteria filter Anda. Coba ubah atau reset filter pencarian.
                        @else
                            Belum ada daftar kost yang terdaftar saat ini.
                        @endif
                    </p>
                </div>
                <button type="button" wire:click="resetFilters"
                    @click="viewMode = 'list'; hasFilter=false; wasApplied=false"
                    class="px-6 py-3 bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    @if($search && !$gender && !$district && !$price_min && !$price_max)
                        <span>Hapus Pencarian</span>
                    @else
                        <span>Tampilkan Semua Kost</span>
                    @endif
                </button>
            </div>
        @endif

        <!-- Full-Width Immersive Map View Mode (Always in DOM to preserve Map instance) -->
        <div wire:key="map-view" wire:ignore x-show="viewMode === 'map' && items.length > 0" x-cloak class="w-full" x-data="{ mapFailed: false }" @map-load-error.window="mapFailed = true">
            <!-- Map Container -->
            <div x-show="!mapFailed" class="w-full rounded-2xl border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden bg-white">
                <div class="p-4 bg-yellow-300 border-b-3 border-black flex items-center justify-between">
                    <span class="font-black text-sm uppercase text-black flex items-center gap-2 tracking-tight">
                        &#128205; Peta Interaktif Kost Bandung
                    </span>
                    <span class="text-xs font-black text-black bg-white border-2 border-black px-3 py-1 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase">
                        <span x-text="items.length"></span> Kost Tampil Pada Peta
                    </span>
                </div>
                <div x-ref="catalogMapElement" class="w-full h-[450px] lg:h-[500px] bg-zinc-100 z-0"></div>
            </div>

            <!-- Fallback Neo-Brutalist Error Card -->
            <div x-show="mapFailed" x-cloak class="bg-rose-400 border-4 border-black rounded-2xl p-10 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] flex flex-col items-center justify-center space-y-4">
                <div class="w-16 h-16 bg-white border-3 border-black rounded-full flex items-center justify-center text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <svg class="w-8 h-8 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-black uppercase">⚠️ Gagal Memuat Peta Interaktif</h3>
                <p class="text-sm font-bold text-black max-w-md mx-auto">Koneksi ke layanan peta gagal atau terputus. Silakan gunakan mode daftar untuk melihat properti kost.</p>
                <button type="button" @click="viewMode = 'list'" class="mt-2 px-6 py-3 bg-white hover:bg-zinc-100 text-black border-3 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl inline-flex items-center gap-2 cursor-pointer">
                    &#128203; Kembali ke Mode Daftar
                </button>
            </div>
        </div>
    </div>
</div>
