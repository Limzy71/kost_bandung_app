<div
    x-data="catalogMap({ districtBounds: @js($districtBounds) })"
    x-init="init()"
    data-maps-key="{{ $googleMapsApiKey }}"
    @scroll-to-home-list.window="document.getElementById('home-list-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
    class="space-y-8"
>
    <!-- Filter Bar Neo-Brutalist -->
    <div
        x-data="{
            draftSearch: @js($search),
            draftDistrict: @js($district),
            draftGender: @js($gender),
            draftRentPeriod: @js($rent_period),
            draftPriceMin: @js($price_min ?? ''),
            draftPriceMax: @js($price_max ?? ''),
            draftVerifiedOnly: @js($verified_only),
            draftFacilities: @js($facilities),

            get hasFilter() {
                return Boolean(
                    this.draftSearch ||
                    this.draftDistrict ||
                    this.draftGender ||
                    this.draftRentPeriod ||
                    this.draftPriceMin ||
                    this.draftPriceMax ||
                    this.draftVerifiedOnly ||
                    this.draftFacilities.length > 0 ||
                    $wire.search ||
                    $wire.district ||
                    $wire.gender ||
                    $wire.rent_period ||
                    $wire.price_min ||
                    $wire.price_max ||
                    $wire.verified_only ||
                    $wire.facilities.length > 0
                );
            },

            get activeFilterCount() {
                let count = 0;
                if (this.draftSearch) count++;
                if (this.draftGender) count++;
                if (this.draftDistrict) count++;
                if (this.draftRentPeriod) count++;
                if (this.draftPriceMin || this.draftPriceMax) count++;
                if (this.draftVerifiedOnly) count++;
                if (this.draftFacilities.length > 0) count += this.draftFacilities.length;
                return count;
            },

            toggleFacility(name) {
                if (this.draftFacilities.includes(name)) {
                    this.draftFacilities = this.draftFacilities.filter(f => f !== name);
                } else {
                    this.draftFacilities.push(name);
                }
            },

            setPricePreset(preset) {
                const presets = {
                    'under_1m': { min: '', max: '1000000' },
                    '1m_2m': { min: '1000000', max: '2000000' },
                    '2m_3m': { min: '2000000', max: '3000000' },
                    'above_3m': { min: '3000000', max: '' },
                    'all': { min: '', max: '' }
                };
                const p = presets[preset] || presets['all'];
                this.draftPriceMin = p.min;
                this.draftPriceMax = p.max;
            },

            apply() {
                $wire.applyAllFilters(
                    this.draftSearch,
                    this.draftDistrict,
                    this.draftGender,
                    this.draftRentPeriod,
                    this.draftPriceMin,
                    this.draftPriceMax,
                    this.draftVerifiedOnly,
                    this.draftFacilities
                );
            },

            reset() {
                this.draftSearch = '';
                this.draftDistrict = '';
                this.draftGender = '';
                this.draftRentPeriod = '';
                this.draftPriceMin = '';
                this.draftPriceMax = '';
                this.draftVerifiedOnly = false;
                this.draftFacilities = [];
                $wire.resetFilters();
            }
        }"
        @reset-filters.window="reset()"
        class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 p-5 sm:p-7 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] space-y-6"
    >
        <!-- Filter Card Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b-3 border-black dark:border-zinc-700 pb-4 gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-yellow-400 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                    <x-icon name="lucide-sliders-horizontal" class="w-4 h-4 stroke-[2.5]" />
                </div>
                <h2 class="text-base sm:text-lg font-black text-black dark:text-white uppercase tracking-tight">
                    Filter Pencarian Kost
                </h2>
                <template x-if="hasFilter && activeFilterCount > 0">
                    <span class="px-2.5 py-0.5 bg-yellow-300 border-2 border-black dark:border-zinc-700 text-black font-black text-[10px] uppercase rounded-full shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                        <span x-text="activeFilterCount"></span> Dipilih
                    </span>
                </template>
            </div>

            <!-- Action buttons only visible when user has chosen any filter -->
            <div x-show="hasFilter" x-cloak class="flex items-center gap-2.5 shrink-0 self-end sm:self-auto transition-all">
                <button type="button" @click="reset()"
                    class="bg-rose-400 hover:bg-rose-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase px-3.5 py-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                    <x-icon name="lucide-rotate-ccw" class="w-3.5 h-3.5 stroke-[3]" />
                    <span>Reset Filter</span>
                </button>
                <button type="button" @click="apply()"
                    class="bg-lime-400 hover:bg-lime-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase px-4 py-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                    <x-icon name="lucide-search" class="w-3.5 h-3.5 stroke-[3]" />
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </div>

        <!-- Row 1: Search, District, Gender -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
            <!-- Search Query -->
            <div>
                <label class="block text-xs font-black uppercase text-black dark:text-white mb-2 tracking-wide">
                    Cari Nama / Jalan / Area
                </label>
                <div class="relative flex items-center">
                    <input
                        x-model="draftSearch"
                        @keydown.enter="apply()"
                        type="text"
                        placeholder="Contoh: Dago, Dipatiukur, Sekeloa..."
                        class="w-full h-12 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl pl-11 pr-10 text-sm font-black uppercase text-black dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]"
                    >
                    <x-icon name="lucide-search" class="w-5 h-5 text-black dark:text-white absolute left-3.5 pointer-events-none stroke-[2.5]" />
                    <template x-if="draftSearch">
                        <button type="button"
                            @click="draftSearch = ''; if ($wire.search) { apply(); }"
                            class="absolute right-3 w-7 h-7 bg-rose-400 hover:bg-rose-300 border-2 border-black dark:border-zinc-700 rounded-lg text-black font-black text-xs shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all flex items-center justify-center cursor-pointer">
                            &#x2715;
                        </button>
                    </template>
                </div>
            </div>

            <!-- District & Gender -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- District -->
                <div>
                    <label class="block text-xs font-black uppercase text-black dark:text-white mb-2 tracking-wide">
                        Kecamatan (Kota Bandung)
                    </label>
                    <select x-model="draftDistrict"
                        class="w-full h-12 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl px-3 text-sm font-black uppercase tracking-wide text-black dark:text-white focus:outline-none focus:ring-0 cursor-pointer transition-all duration-150 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] dark:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23fff%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] bg-[length:14px_14px] bg-no-repeat bg-[right_12px_center] pr-9">
                        <option value="" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Semua Kecamatan</option>
                        @foreach ($districts as $val => $label)
                            <option value="{{ $val }}" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-xs font-black uppercase text-black dark:text-white mb-2 tracking-wide">
                        Tipe Penghuni
                    </label>
                    <select x-model="draftGender"
                        class="w-full h-12 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl px-3 text-sm font-black uppercase tracking-wide text-black dark:text-white focus:outline-none focus:ring-0 cursor-pointer transition-all duration-150 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] dark:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23fff%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] bg-[length:14px_14px] bg-no-repeat bg-[right_12px_center] pr-9">
                        <option value="" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Semua Tipe</option>
                        <option value="putra" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Putra</option>
                        <option value="putri" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Putri</option>
                        <option value="campur" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Campur</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Row 2: Price Range & Period/Verified -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 pt-1">
            <!-- Price Range -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-black uppercase text-black dark:text-white tracking-wide">
                        Rentang Harga Sewa
                    </label>
                    <!-- Quick Preset Chips -->
                    <div class="hidden sm:flex items-center gap-1.5">
                        <button type="button" @click="setPricePreset('all')"
                            :class="!draftPriceMin && !draftPriceMax ? 'bg-yellow-400 border-black dark:border-zinc-700 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]' : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400'"
                            class="px-2 py-0.5 text-[10px] font-black uppercase border rounded transition-all cursor-pointer">
                            Semua
                        </button>
                        <button type="button" @click="setPricePreset('under_1m')"
                            :class="draftPriceMax === '1000000' && !draftPriceMin ? 'bg-yellow-400 border-black dark:border-zinc-700 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]' : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400'"
                            class="px-2 py-0.5 text-[10px] font-black uppercase border rounded transition-all cursor-pointer">
                            &lt; 1 Jt
                        </button>
                        <button type="button" @click="setPricePreset('1m_2m')"
                            :class="draftPriceMin === '1000000' && draftPriceMax === '2000000' ? 'bg-yellow-400 border-black dark:border-zinc-700 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]' : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400'"
                            class="px-2 py-0.5 text-[10px] font-black uppercase border rounded transition-all cursor-pointer">
                            1–2 Jt
                        </button>
                        <button type="button" @click="setPricePreset('2m_3m')"
                            :class="draftPriceMin === '2000000' && draftPriceMax === '3000000' ? 'bg-yellow-400 border-black dark:border-zinc-700 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]' : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400'"
                            class="px-2 py-0.5 text-[10px] font-black uppercase border rounded transition-all cursor-pointer">
                            2–3 Jt
                        </button>
                        <button type="button" @click="setPricePreset('above_3m')"
                            :class="draftPriceMin === '3000000' && !draftPriceMax ? 'bg-yellow-400 border-black dark:border-zinc-700 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]' : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400'"
                            class="px-2 py-0.5 text-[10px] font-black uppercase border rounded transition-all cursor-pointer">
                            &gt; 3 Jt
                        </button>
                    </div>
                </div>

                <!-- Min / Max Dropdowns -->
                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <select x-model="draftPriceMin"
                        class="w-full h-12 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl px-3 text-sm font-black uppercase tracking-wide text-black dark:text-white focus:outline-none focus:ring-0 cursor-pointer transition-all duration-150 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] dark:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23fff%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] bg-[length:14px_14px] bg-no-repeat bg-[right_10px_center] pr-8">
                        <option value="" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Min Harga</option>
                        <option value="500000" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Rp 500rb</option>
                        <option value="1000000" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Rp 1 Jt</option>
                        <option value="1500000" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Rp 1.5 Jt</option>
                        <option value="2000000" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Rp 2 Jt</option>
                        <option value="3000000" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Rp 3 Jt</option>
                    </select>
                    <select x-model="draftPriceMax"
                        class="w-full h-12 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl px-3 text-sm font-black uppercase tracking-wide text-black dark:text-white focus:outline-none focus:ring-0 cursor-pointer transition-all duration-150 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] dark:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23fff%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] bg-[length:14px_14px] bg-no-repeat bg-[right_10px_center] pr-8">
                        <option value="" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Max Harga</option>
                        <option value="1000000" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Rp 1 Jt</option>
                        <option value="1500000" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Rp 1.5 Jt</option>
                        <option value="2000000" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Rp 2 Jt</option>
                        <option value="3000000" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Rp 3 Jt</option>
                        <option value="5000000" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Rp 5 Jt</option>
                        <option value="7500000" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Rp 7.5 Jt</option>
                        <option value="10000000" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Rp 10 Jt</option>
                    </select>
                </div>
            </div>

            <!-- Rent Period & Verified Toggle -->
            <div>
                <label class="block text-xs font-black uppercase text-black dark:text-white mb-2 tracking-wide">
                    Periode & Verifikasi
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Period -->
                    <select x-model="draftRentPeriod"
                        class="w-full h-12 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl px-3 text-sm font-black uppercase tracking-wide text-black dark:text-white focus:outline-none focus:ring-0 cursor-pointer transition-all duration-150 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23000%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] dark:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%23fff%22%20stroke-width%3D%223%22%20fill%3D%22none%22%2F%3E%3C%2Fsvg%3E')] bg-[length:14px_14px] bg-no-repeat bg-[right_12px_center] pr-9">
                        <option value="" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">Semua Periode</option>
                        @foreach (\App\Models\Kost::rentPeriodLabels() as $val => $label)
                            <option value="{{ $val }}" class="font-bold text-sm normal-case text-zinc-900 dark:text-zinc-300 bg-white dark:bg-zinc-900 py-2">{{ $label }}</option>
                        @endforeach
                    </select>

                    <!-- Verified Only -->
                    <label :class="draftVerifiedOnly
                            ? 'bg-emerald-400 text-black border-3 border-black dark:border-zinc-700 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] -translate-x-0.5 -translate-y-0.5'
                            : 'bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border-3 border-black dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]'"
                        class="w-full h-12 flex justify-center items-center font-black text-xs uppercase rounded-xl transition-all cursor-pointer gap-2 px-3">
                        <input type="checkbox" x-model="draftVerifiedOnly" class="hidden">
                        <x-icon name="lucide-badge-check" class="w-4 h-4 stroke-[3] shrink-0" />
                        <span class="truncate">Kost Verified</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Row 3: Fasilitas Populer (Compact Chip Tags) -->
        <div class="border-t-2 border-dashed border-black/20 dark:border-zinc-700 pt-4 mt-2">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <x-icon name="lucide-armchair" class="w-4 h-4 text-black dark:text-white stroke-[2.5]" />
                    <label class="text-xs font-black uppercase text-black dark:text-white tracking-wide">
                        Fasilitas Kost Populer
                    </label>
                </div>
                <template x-if="draftFacilities.length > 0">
                    <span class="px-2.5 py-0.5 bg-yellow-300 border border-black dark:border-zinc-700 font-black text-[10px] uppercase rounded-full shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] text-black">
                        <span x-text="draftFacilities.length"></span> Dipilih
                    </span>
                </template>
            </div>

            <!-- Compact Chip Tags Group -->
            <div class="flex flex-wrap items-center gap-2">
                @php
                    $facilityFilters = [
                        'AC' => ['icon' => 'lucide-snowflake', 'label' => 'AC'],
                        'Wi-Fi' => ['icon' => 'lucide-wifi', 'label' => 'Wi-Fi'],
                        'Kamar Mandi Dalam' => ['icon' => 'lucide-shower-head', 'label' => 'KM Dalam'],
                        'Water Heater (Air Hangat)' => ['icon' => 'lucide-flame', 'label' => 'Water Heater'],
                        'Kasur' => ['icon' => 'lucide-bed-double', 'label' => 'Kasur'],
                        'Lemari' => ['icon' => 'lucide-door-closed', 'label' => 'Lemari'],
                        'Dapur Bersama' => ['icon' => 'lucide-utensils', 'label' => 'Dapur'],
                        'CCTV' => ['icon' => 'lucide-cctv', 'label' => 'CCTV'],
                        'Parkir Motor' => ['icon' => 'lucide-motorbike', 'label' => 'Parkir Motor'],
                        'Parkir Mobil' => ['icon' => 'lucide-car', 'label' => 'Parkir Mobil'],
                    ];
                @endphp
                @foreach ($facilityFilters as $name => $meta)
                    <button type="button" @click="toggleFacility('{{ $name }}')"
                        :class="draftFacilities.includes('{{ $name }}')
                            ? 'bg-yellow-400 text-black border-2 border-black dark:border-zinc-700 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] -translate-x-0.5 -translate-y-0.5 font-black'
                            : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-2 border-black/30 dark:border-zinc-700 hover:border-black dark:hover:border-zinc-500 shadow-[1px_1px_0px_0px_rgba(0,0,0,0.15)] font-bold'"
                        class="h-9 px-3 rounded-lg text-xs uppercase transition-all cursor-pointer active:translate-x-0.5 active:translate-y-0.5 active:shadow-none inline-flex items-center gap-1.5 whitespace-nowrap">
                        <x-icon name="{{ $meta['icon'] }}" class="w-3.5 h-3.5 shrink-0 stroke-[2.5]" />
                        <span>{{ $meta['label'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Section Title & Controls Bar (Sorting & Layout Switcher) -->
    <div id="home-list-section" class="scroll-mt-20 flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4">
        <!-- Title & Count -->
        <div class="flex items-center gap-3">
            <h3 class="text-base sm:text-xl font-black text-black dark:text-white uppercase tracking-tight">
                Daftar Properti Kost
            </h3>
            <span class="px-2.5 py-1 bg-yellow-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] uppercase whitespace-nowrap">
                {{ $kosts->total() }} Ditemukan
            </span>
        </div>

        <!-- Controls: Sort By & View Switcher -->
        <div class="flex items-center justify-between sm:justify-end gap-3 flex-wrap sm:flex-nowrap">
            <!-- Sort By Dropdown (Full click target with generous spacing and instant live update) -->
            <div x-data="{
                sort: @entangle('sort').live,
                get sortLabel() {
                    const labels = {
                        'recommended': 'Rekomendasi',
                        'price_asc': 'Harga Termurah',
                        'price_desc': 'Harga Termahal',
                        'newest': 'Terbaru'
                    };
                    return labels[this.sort] || 'Rekomendasi';
                }
            }" class="relative flex items-center justify-between gap-3 bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 px-4 py-2.5 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 transition-all cursor-pointer select-none">
                <div class="flex items-center gap-2 pointer-events-none">
                    <x-icon name="lucide-arrow-up-down" class="w-4 h-4 text-black dark:text-white stroke-[2.5] shrink-0" />
                    <span class="text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400">Urutkan:</span>
                    <span class="text-xs font-black uppercase text-black dark:text-white tracking-wide ml-0.5" x-text="sortLabel"></span>
                </div>
                <x-icon name="lucide-chevron-down" class="w-4 h-4 text-black dark:text-white stroke-[2.5] shrink-0 pointer-events-none" />
                <select x-model="sort" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer text-base">
                    <option value="recommended">Rekomendasi</option>
                    <option value="price_asc">Harga Termurah</option>
                    <option value="price_desc">Harga Termahal</option>
                    <option value="newest">Terbaru</option>
                </select>
            </div>

            @if($kosts->count() > 0 || $district)
            <!-- View Switcher -->
            <div wire:key="view-switcher" class="flex items-center gap-1 bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 p-1 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                <button type="button"
                    @click="viewMode = 'list'"
                    :class="viewMode === 'list' ? 'bg-yellow-400 text-black border border-black dark:border-zinc-700 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]' : 'text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                    class="px-3 py-1.5 rounded-lg font-black text-xs uppercase transition-all cursor-pointer flex items-center justify-center gap-1.5">
                    <x-icon name="lucide-list" class="w-3.5 h-3.5 stroke-[3]" />
                    <span>Lihat Daftar</span>
                </button>
                <button type="button"
                    @click="viewMode = 'map'"
                    :class="viewMode === 'map' ? 'bg-yellow-400 text-black border border-black dark:border-zinc-700 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]' : 'text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'"
                    class="px-3 py-1.5 rounded-lg font-black text-xs uppercase transition-all cursor-pointer flex items-center justify-center gap-1.5">
                    <x-icon name="lucide-map" class="w-3.5 h-3.5 stroke-[3]" />
                    <span>Lihat Peta</span>
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="relative">
        <!-- Loading Overlay -->
        <div wire:loading.delay wire:target="applyFilters, resetFilters, gender, district, rent_period, price_min, price_max, sort, facilities, setPricePreset, toggleFacility"
            class="absolute inset-0 bg-white/70 dark:bg-zinc-900/70 backdrop-blur-xs z-30 flex items-center justify-center rounded-2xl border-4 border-black dark:border-zinc-700">
            <div class="bg-yellow-300 border-3 border-black dark:border-zinc-700 px-6 py-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] flex items-center gap-3">
                <x-icon name="lucide-loader-circle" class="animate-spin h-6 w-6 text-black" />
                <span class="font-black text-black text-sm uppercase tracking-wide">Memuat Hunian...</span>
            </div>
        </div>

        @if($kosts->count() > 0)
            <!-- List View (Default Mode: Balanced 3-column grid at 100% container width) -->
            <div wire:key="list-view" x-show="viewMode === 'list'" x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-3 scale-[0.985]"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-[0.99]"
                class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($kosts as $kost)
                        <div class="kost-card bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl overflow-hidden shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-y-1 hover:shadow-[7px_7px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[7px_7px_0px_0px_rgba(255,255,255,0.25)] transition-all duration-300 ease-out will-change-transform flex flex-col justify-between group" style="animation-delay: {{ min($loop->index, 9) * 45 }}ms">
                            <div>
                                <!-- Image -->
                                <div class="aspect-[4/3] bg-zinc-200 dark:bg-zinc-800 relative overflow-hidden border-b-3 border-black dark:border-zinc-700 cursor-pointer"
                                    role="link" tabindex="0" aria-label="Lihat detail {{ $kost->name }}"
                                    onclick="window.location.href='{{ route('kost.show', $kost->slug) }}'"
                                    @keydown.enter.prevent="window.location.href='{{ route('kost.show', $kost->slug) }}'"
                                    @keydown.space.prevent="window.location.href='{{ route('kost.show', $kost->slug) }}'">
                                    @if ($kost->primaryImage)
                                        <img src="{{ Str::startsWith($kost->primaryImage->image_path, 'http') ? $kost->primaryImage->image_path : Storage::url($kost->primaryImage->image_path) }}"
                                            alt="{{ $kost->name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-yellow-100 dark:bg-yellow-950/40 text-black dark:text-white">
                                            <x-icon name="lucide-image" class="w-12 h-12" />
                                        </div>
                                    @endif

                                    <!-- Top Left Badges -->
                                    <div class="absolute top-3 left-3 flex flex-wrap items-center gap-1.5 max-w-[calc(100%-8rem)] pointer-events-none z-10">
                                        <span class="px-2.5 py-1 bg-pink-400 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] tracking-wider inline-flex items-center gap-1">
                                            @if(strtolower($kost->gender_type) === 'campur')
                                                <x-icon name="lucide-users" class="w-3 h-3 shrink-0 stroke-[2.5]" />
                                            @elseif(strtolower($kost->gender_type) === 'putri')
                                                <x-icon name="lucide-user-check" class="w-3 h-3 shrink-0 stroke-[2.5]" />
                                            @else
                                                <x-icon name="lucide-user" class="w-3 h-3 shrink-0 stroke-[2.5]" />
                                            @endif
                                            <span>{{ $kost->gender_type }}</span>
                                        </span>
                                        @if ($kost->isBoostActive() || $kost->boosted_at)
                                            <span class="px-2.5 py-1 bg-yellow-400 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] tracking-wider inline-flex items-center gap-1">
                                                <x-icon name="lucide-zap" fill="#FBBF24" stroke="black" stroke-width="1.8" class="w-3.5 h-3.5 shrink-0" />
                                                <span>Super Boost</span>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Top Right Badge -->
                                    <div class="absolute top-3 right-3 pointer-events-none z-10">
                                        <span class="px-2.5 py-1 bg-cyan-300 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] tracking-wider inline-flex items-center gap-1">
                                            <x-icon name="lucide-map-pin" class="w-3 h-3 shrink-0 stroke-[2.5]" />
                                            <span>{{ $kost->district }}</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-5 space-y-4">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-lg font-black text-black dark:text-white leading-snug line-clamp-1 hover:underline">
                                                <a href="{{ route('kost.show', $kost->slug) }}">{{ $kost->name }}</a>
                                            </h3>
                                            <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-0.5 line-clamp-1">{{ $kost->address }}</p>
                                        </div>
                                        @if ($kost->isVerified())
                                            <span class="px-2 py-0.5 bg-emerald-300 text-black border-2 border-black dark:border-zinc-700 text-[9px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] tracking-wider inline-flex items-center gap-1 shrink-0 mt-0.5" title="Kepemilikan terverifikasi">
                                                <x-icon name="lucide-badge-check" class="w-3 h-3 shrink-0 stroke-[2.5]" />
                                                <span>Terverifikasi</span>
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-rose-400 text-black border-2 border-black dark:border-zinc-700 text-[9px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] tracking-wider inline-flex items-center gap-1 shrink-0 mt-0.5" title="Kost ini belum diverifikasi kepemilikannya">
                                                <x-icon name="lucide-shield-alert" class="w-3 h-3 shrink-0 stroke-[2.5]" />
                                                <span>Belum Terverifikasi</span>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="pt-3 border-t-2 border-black dark:border-zinc-700 flex items-center justify-between gap-2 overflow-hidden min-w-0">
                                        <div class="shrink-0 min-w-0">
                                            <p class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400">Harga Sewa</p>
                                            <span class="bg-yellow-300 border-2 border-black dark:border-zinc-700 font-black text-black px-2.5 py-1 rounded text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center whitespace-nowrap mt-0.5">
                                                Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}<span class="text-[10px] font-bold ml-0.5">{{ \App\Models\Kost::rentPeriodUnit($kost->rent_period) }}</span>
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap justify-end gap-1 overflow-hidden shrink min-w-0">
                                            @if ($kost->facilities && $kost->facilities->count() > 0)
                                                @foreach ($kost->facilities->take(2) as $facility)
                                                    <span class="bg-zinc-100 dark:bg-zinc-800 border-2 border-black dark:border-zinc-700 text-[10px] font-bold text-black dark:text-white px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1 truncate max-w-[110px] min-w-0">
                                                        <span class="truncate">{{ $facility->name }}</span>
                                                    </span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="px-5 py-4 bg-zinc-100 dark:bg-zinc-800 border-t-3 border-black dark:border-zinc-700 flex items-center justify-between">
                                <span class="text-xs font-extrabold text-lime-700 dark:text-lime-400 bg-lime-200 dark:bg-lime-950/50 border-2 border-black dark:border-zinc-700 px-2.5 py-1 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] uppercase">
                                    &#10003; Siap Huni
                                </span>
                                <a href="{{ route('kost.show', $kost->slug) }}"
                                    class="px-4 py-2 bg-orange-400 hover:bg-orange-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1">
                                    <span>Lihat Detail</span>
                                    <x-icon name="lucide-arrow-right" class="w-3.5 h-3.5 stroke-[3]" />
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
            <!-- Empty State: Always show when there are 0 results -->
            <div wire:key="empty-state" x-show="viewMode === 'list' || (viewMode === 'map' && items.length === 0 && !$wire.district)" x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-3"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="bg-yellow-100 dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl p-5 sm:p-12 text-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] sm:shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] space-y-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-2xl flex items-center justify-center mx-auto text-black dark:text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] sm:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] -rotate-3">
                    <x-icon name="lucide-search" class="w-8 h-8 sm:w-10 sm:h-10" />
                </div>
                <div>
                    <h3 class="text-xl sm:text-3xl font-black text-black dark:text-white uppercase leading-tight text-balance">
                        @if($totalKostInDb === 0)
                            Belum Ada Kost Terdaftar
                        @else
                            Tidak Ada Hunian Ditemukan
                        @endif
                    </h3>
                    <p class="text-xs sm:text-sm font-bold text-zinc-700 dark:text-zinc-300 max-w-xs sm:max-w-md mx-auto mt-2 leading-relaxed text-balance">
                        @if($totalKostInDb === 0)
                            Belum ada daftar kost yang terdaftar atau tersedia saat ini.
                        @elseif($hasBothSearchAndFilters)
                            Tidak ada kost yang cocok dengan kata kunci "{{ $search }}" dan kriteria filter Anda. Coba ubah kata kunci atau reset filter pencarian.
                        @elseif($hasSearchOnly)
                            Tidak ada kost yang cocok dengan kata kunci "{{ $search }}". Coba hapus pencarian atau gunakan kata kunci lain.
                        @elseif($hasActiveFilter)
                            Tidak ada kost yang cocok dengan kriteria filter Anda. Coba ubah atau reset filter pencarian.
                        @else
                            Belum ada daftar kost yang terdaftar atau tersedia saat ini.
                        @endif
                    </p>
                </div>
                <button type="button" wire:click="resetFilters"
                    @click="$dispatch('reset-filters'); if(viewMode==='map') { $nextTick(() => window.dispatchEvent(new Event('resize'))); }"
                    class="w-full sm:w-auto px-5 py-3 bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black dark:border-zinc-700 font-black text-xs sm:text-sm uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] sm:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl inline-flex items-center justify-center gap-2 cursor-pointer">
                    <x-icon name="lucide-refresh-cw" class="w-4 h-4 stroke-[3]" />
                    @if($hasActiveFilter)
                        <span>Reset Semua Filter</span>
                    @else
                        <span>Muat Ulang Halaman</span>
                    @endif
                </button>
            </div>
        @endif

        <!-- Full-Width Immersive Map View Mode (Always in DOM to preserve Map instance) -->
        <div wire:key="map-view" wire:ignore x-show="viewMode === 'map' && (items.length > 0 || $wire.district)" x-cloak
            x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-500"
            x-transition:enter-start="opacity-0 translate-y-5 scale-[0.98]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-[0.99]"
            class="w-full" @map-load-error.window="mapFailed = true">
            <!-- Map Container -->
            <div x-show="!mapFailed" class="relative w-full rounded-2xl border-4 border-black dark:border-zinc-700 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] overflow-hidden bg-white dark:bg-zinc-900">
                <div class="p-4 bg-yellow-300 border-b-3 border-black dark:border-zinc-700 flex items-center justify-between z-10 relative">
                    <span class="font-black text-sm uppercase text-black flex items-center gap-2 tracking-tight">
                        <span class="bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 rounded-lg p-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                            <x-icon name="lucide-map-pin" class="w-4 h-4 text-black dark:text-white stroke-[3]" />
                        </span>
                        Peta Interaktif Kost Bandung
                    </span>
                    <span class="text-xs font-black text-black dark:text-white bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 px-3 py-1 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] uppercase">
                        <span x-text="items.length"></span> Kost Tampil Pada Peta
                    </span>
                </div>
                <!-- Map Type Switcher Buttons -->
                <div x-ref="mapTypeSwitcher" class="mt-3 ml-3 flex gap-2"
                    x-show="map !== null" x-cloak style="font-family: 'Inter', system-ui, sans-serif !important;">
                    <button type="button" @click="switchLayer('street')"
                        :class="currentLayer === 'street' ?
                            'bg-yellow-400 border-black dark:border-zinc-700 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]' :
                            'bg-white dark:bg-zinc-900 border-black dark:border-zinc-700 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                        class="px-3.5 py-1.5 text-xs font-black uppercase border-2 rounded-lg text-black dark:text-white transition-all cursor-pointer"
                        title="Peta Standard">Peta</button>
                    <button type="button" @click="switchLayer('satellite')"
                        :class="currentLayer === 'satellite' ?
                            'bg-cyan-300 border-black dark:border-zinc-700 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]' :
                            'bg-white dark:bg-zinc-900 border-black dark:border-zinc-700 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                        class="px-3.5 py-1.5 text-xs font-black uppercase border-2 rounded-lg text-black dark:text-white transition-all cursor-pointer"
                        title="Tampilan Satelit">Satelit</button>
                </div>
                <div x-ref="catalogMapElement" class="w-full h-[450px] lg:h-[500px] bg-zinc-100 dark:bg-zinc-800 z-0"></div>
            </div>

            <!-- Fallback Neo-Brutalist Error Card -->
            <div x-show="mapFailed" x-cloak class="bg-rose-400 border-4 border-black dark:border-zinc-700 rounded-2xl p-10 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] flex flex-col items-center justify-center space-y-4">
                <div class="w-16 h-16 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-full flex items-center justify-center text-black dark:text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                    <x-icon name="lucide-triangle-alert" class="w-8 h-8 stroke-[3]" />
                </div>
                <h3 class="text-2xl font-black text-black uppercase">⚠️ Gagal Memuat Peta Interaktif</h3>
                <p class="text-sm font-bold text-black max-w-md mx-auto">Koneksi ke layanan peta gagal atau terputus. Silakan gunakan mode daftar untuk melihat properti kost.</p>
                <button type="button" @click="viewMode = 'list'" class="mt-2 px-6 py-3 bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-black dark:text-white border-3 border-black dark:border-zinc-700 font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl inline-flex items-center gap-2 cursor-pointer">
                    &#128203; Kembali ke Mode Daftar
                </button>
            </div>
        </div>
    </div>
</div>
