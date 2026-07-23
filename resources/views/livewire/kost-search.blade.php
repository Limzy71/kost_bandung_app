@php
    $googleMapsApiKey = config('services.google.maps_api_key') ?: env('GOOGLE_MAPS_API_KEY');
    
    $mapItems = $kosts->map(function ($k) {
        $priceFormatted = $k->price_monthly >= 1000000 
            ? round($k->price_monthly / 1000000, 1) . 'Jt'
            : round($k->price_monthly / 1000) . 'K';
            
        $priceFull = 'Rp ' . number_format($k->price_monthly, 0, ',', '.');
        $img = $k->primaryImage 
            ? (Str::startsWith($k->primaryImage->image_path, 'http') ? $k->primaryImage->image_path : Storage::url($k->primaryImage->image_path))
            : 'https://placehold.co/400x300/eeeeee/31343c?text=' . urlencode($k->name);

        return [
            'id' => $k->id,
            'name' => $k->name,
            'slug' => $k->slug,
            'district' => $k->district,
            'address' => $k->address,
            'gender' => $k->gender_type,
            'price_short' => $priceFormatted,
            'price_full' => $priceFull,
            'lat' => (float) $k->latitude,
            'lng' => (float) $k->longitude,
            'image' => $img,
            'url' => route('kost.show', $k->slug),
            'is_boosted' => (bool) $k->boosted_at,
        ];
    })->values()->toArray();
@endphp

<!-- Leaflet JS & CSS Fallback for Catalog Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

@if($googleMapsApiKey)
    <script>
        window.initGoogleCatalogMap = function() {
            window.dispatchEvent(new CustomEvent('google-catalog-map-loaded'));
        };
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initGoogleCatalogMap" async defer></script>
@endif

<div 
    x-data="{
        viewMode: 'split',
        mobileMode: 'list',
        items: {{ json_encode($mapItems) }},
        hasGoogleKey: {{ $googleMapsApiKey ? 'true' : 'false' }},
        map: null,
        markers: [],
        infoWindow: null,
        
        initCatalogMap() {
            const setMapData = () => {
                if (this.hasGoogleKey && window.google && window.google.maps) {
                    return this.setupGoogleMap();
                }
                return this.setupLeafletMap();
            };

            if (this.hasGoogleKey) {
                if (window.google && window.google.maps) {
                    setMapData();
                } else {
                    window.addEventListener('google-catalog-map-loaded', () => setMapData());
                    setTimeout(() => { if (!this.map) setMapData(); }, 3000);
                }
            } else {
                setMapData();
            }
        },

        setupGoogleMap() {
            if (!this.$refs.catalogMapElement) return false;
            if (!window.google || !window.google.maps) return false;
            try {
                if (!this.map) {
                    this.map = new google.maps.Map(this.$refs.catalogMapElement, {
                        center: { lat: -6.917464, lng: 107.619123 },
                        zoom: 13,
                        mapTypeControl: false,
                        streetViewControl: false,
                        fullscreenControl: false,
                    });
                    this.infoWindow = new google.maps.InfoWindow();
                }
                this.renderGoogleMarkers();
                return true;
            } catch (e) {
                console.warn('Google Map Catalog init error:', e);
                return false;
            }
        },

        renderGoogleMarkers() {
            if (!this.map || !window.google) return;
            this.markers.forEach(m => m.setMap(null));
            this.markers = [];

            if (!this.items || this.items.length === 0) return;

            const bounds = new google.maps.LatLngBounds();
            let validCount = 0;

            this.items.forEach(item => {
                if (!item.lat || !item.lng) return;
                const pos = { lat: item.lat, lng: item.lng };
                bounds.extend(pos);
                validCount++;

                const marker = new google.maps.Marker({
                    position: pos,
                    map: this.map,
                    title: item.name,
                    label: {
                        text: item.price_short,
                        color: '#000000',
                        fontWeight: '900',
                        fontSize: '11px',
                    },
                    icon: {
                        path: 'M -25,-12 L 25,-12 L 25,8 L 8,8 L 0,16 L -8,8 L -25,8 Z',
                        fillColor: item.is_boosted ? '#FACC15' : '#FFFFFF',
                        fillOpacity: 1,
                        strokeColor: '#000000',
                        strokeWeight: 2,
                        labelOrigin: new google.maps.Point(0, -2),
                    }
                });

                const popupHtml = `
                    <div style="font-family: inherit; width: 220px; padding: 4px;" class="neo-popup">
                        <div style="aspect-ratio: 16/9; overflow: hidden; border: 2px solid #000; border-radius: 8px; margin-bottom: 8px; background: #eee;">
                            <img src="${item.image}" style="width: 100%; height: 100%; object-fit: cover;" />
                        </div>
                        <div style="display: flex; gap: 4px; margin-bottom: 4px;">
                            <span style="background: #F472B6; color: #000; font-size: 9px; font-weight: 900; text-transform: uppercase; padding: 2px 6px; border: 1.5px solid #000; border-radius: 4px;">${item.gender}</span>
                            <span style="background: #67E8F9; color: #000; font-size: 9px; font-weight: 900; text-transform: uppercase; padding: 2px 6px; border: 1.5px solid #000; border-radius: 4px;">${item.district}</span>
                        </div>
                        <h4 style="font-size: 13px; font-weight: 900; color: #000; text-transform: uppercase; margin: 4px 0 2px; line-height: 1.2;">${item.name}</h4>
                        <p style="font-size: 10px; font-weight: 700; color: #555; margin-bottom: 6px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">${item.address}</p>
                        <div style="background: #FACC15; border: 1.5px solid #000; padding: 4px 8px; border-radius: 6px; font-weight: 900; font-size: 12px; margin-bottom: 8px; box-shadow: 2px 2px 0px #000;">
                            ${item.price_full}<span style="font-size: 9px;">/bln</span>
                        </div>
                        <a href="${item.url}" style="display: block; text-align: center; background: #FB923C; color: #000; border: 1.5px solid #000; padding: 6px; border-radius: 6px; font-weight: 900; font-size: 11px; text-decoration: none; text-transform: uppercase; box-shadow: 2px 2px 0px #000;">
                            Lihat Detail →
                        </a>
                    </div>
                `;

                marker.addListener('click', () => {
                    this.infoWindow.setContent(popupHtml);
                    this.infoWindow.open(this.map, marker);
                });

                this.markers.push(marker);
            });

            if (validCount > 0) {
                this.map.fitBounds(bounds);
                if (validCount === 1) {
                    this.map.setZoom(14);
                }
            } else {
                this.map.setCenter({ lat: -6.917464, lng: 107.619123 });
                this.map.setZoom(13);
            }
        },

        setupLeafletMap() {
            if (!this.$refs.catalogMapElement || typeof L === 'undefined') return;
            if (!this.map) {
                this.map = L.map(this.$refs.catalogMapElement).setView([-6.917464, 107.619123], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(this.map);
            }
            this.renderLeafletMarkers();
        },

        renderLeafletMarkers() {
            if (!this.map || typeof L === 'undefined') return;
            this.markers.forEach(m => this.map.removeLayer(m));
            this.markers = [];

            if (!this.items || this.items.length === 0) return;

            const bounds = L.latLngBounds();
            let validCount = 0;

            this.items.forEach(item => {
                if (!item.lat || !item.lng) return;
                bounds.extend([item.lat, item.lng]);
                validCount++;

                const iconHtml = `
                    <div class="px-2 py-1 border-2 border-black font-black text-[11px] rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase ${item.is_boosted ? 'bg-yellow-300' : 'bg-white'} text-black">
                        ${item.price_short}
                    </div>
                `;
                const customIcon = L.divIcon({
                    html: iconHtml,
                    className: '',
                    iconSize: [54, 26],
                    iconAnchor: [27, 13]
                });

                const popupHtml = `
                    <div class="p-1 font-sans w-52">
                        <img src="${item.image}" class="w-full h-24 object-cover rounded-lg border-2 border-black mb-2" />
                        <span class="px-2 py-0.5 bg-pink-400 border border-black font-black text-[9px] uppercase rounded text-black">${item.gender}</span>
                        <span class="px-2 py-0.5 bg-cyan-300 border border-black font-black text-[9px] uppercase rounded ml-1 text-black">${item.district}</span>
                        <h4 class="font-black text-xs uppercase text-black mt-1 line-clamp-1">${item.name}</h4>
                        <p class="font-bold text-[10px] text-zinc-600 line-clamp-1 mb-2">${item.address}</p>
                        <div class="bg-yellow-300 border-1.5 border-black p-1 rounded font-black text-xs mb-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] text-black">${item.price_full}/bln</div>
                        <a href="${item.url}" class="block text-center bg-orange-400 border-1.5 border-black p-1.5 rounded font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] text-black">Lihat Detail →</a>
                    </div>
                `;

                const marker = L.marker([item.lat, item.lng], { icon: customIcon }).addTo(this.map).bindPopup(popupHtml);
                this.markers.push(marker);
            });

            if (validCount > 0) {
                this.map.fitBounds(bounds);
            }
        }
    }"
    x-init="
        initCatalogMap();
        $watch('items', () => {
            if (hasGoogleKey && window.google && window.google.maps) {
                renderGoogleMarkers();
            } else {
                renderLeafletMarkers();
            }
        });
    "
    x-effect="
        items = {{ json_encode($mapItems) }};
    "
    @scroll-to-home-list.window="document.getElementById('home-list-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
    class="space-y-8"
>
    <!-- Filter Bar Neo-Brutalist -->
    <div 
        x-data="{ 
            hasFilter: false,
            wasApplied: false,
            checkFilter() {
                const genderVal = this.$refs.genderSelect ? this.$refs.genderSelect.value : '';
                const districtVal = this.$refs.districtSelect ? this.$refs.districtSelect.value : '';
                const minVal = this.$refs.minSelect ? this.$refs.minSelect.value : '';
                const maxVal = this.$refs.maxSelect ? this.$refs.maxSelect.value : '';

                // Header RESET FILTER button ONLY appears when at least one select dropdown is chosen
                this.hasFilter = Boolean(
                    genderVal !== '' ||
                    districtVal !== '' ||
                    minVal !== '' ||
                    maxVal !== ''
                );
            },
            resetFormLocally() {
                const dropdownRefs = ['genderSelect', 'districtSelect', 'minSelect', 'maxSelect'];
                dropdownRefs.forEach(ref => {
                    if (this.$refs[ref]) {
                        this.$refs[ref].value = '';
                        // Dispatch a change event so Livewire's wire:model deferred state
                        // picks up the new empty value (prevents stale filter on next apply)
                        this.$refs[ref].dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
                this.checkFilter();
                // Only call server reset if user had previously applied the filters
                if (this.wasApplied) {
                    $wire.resetFilters();
                    this.wasApplied = false;
                }
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
                <!-- Reset Filter Button -->
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

                <!-- Terapkan Filter Button -->
                <button 
                    type="button" 
                    wire:click="applyFilters" 
                    @click="wasApplied = true"
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

                    <!-- Clear Search Input ✕ Button -->
                    <template x-if="query || ($refs.searchInput && $refs.searchInput.value)">
                        <button 
                            type="button" 
                            @click="
                                $refs.searchInput.value = '';
                                $wire.search = '';
                                checkFilter();
                                if (wasApplied) { $wire.applyFilters(); }
                            "
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

    <!-- Section Title & Layout Switcher Controls (Desktop & Mobile) -->
    <div id="home-list-section" class="scroll-mt-20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h3 class="text-xl font-black text-black uppercase tracking-tight">Daftar Properti Kost</h3>
            <span class="px-3 py-1 bg-yellow-300 border-2 border-black font-black text-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] uppercase">
                {{ $kosts->total() }} Ditemukan
            </span>
        </div>

        <!-- Layout Controls -->
        <div class="flex items-center gap-2">
            <!-- Desktop Layout Buttons (lg screens) -->
            <div class="hidden lg:flex items-center gap-1.5 bg-white border-3 border-black p-1 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <button 
                    type="button" 
                    @click="viewMode = 'split'; setTimeout(() => { if(map && map.invalidateSize) map.invalidateSize(); }, 150)" 
                    class="px-3 py-1.5 rounded-lg font-black text-xs uppercase transition-all cursor-pointer flex items-center gap-1.5"
                    :class="viewMode === 'split' ? 'bg-yellow-400 text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'text-zinc-600 hover:text-black'"
                >
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    <span>Daftar + Peta</span>
                </button>
                <button 
                    type="button" 
                    @click="viewMode = 'list'" 
                    class="px-3 py-1.5 rounded-lg font-black text-xs uppercase transition-all cursor-pointer flex items-center gap-1.5"
                    :class="viewMode === 'list' ? 'bg-yellow-400 text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'text-zinc-600 hover:text-black'"
                >
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span>Hanya Daftar</span>
                </button>
                <button 
                    type="button" 
                    @click="viewMode = 'map'; setTimeout(() => { if(map && map.invalidateSize) map.invalidateSize(); }, 150)" 
                    class="px-3 py-1.5 rounded-lg font-black text-xs uppercase transition-all cursor-pointer flex items-center gap-1.5"
                    :class="viewMode === 'map' ? 'bg-yellow-400 text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'text-zinc-600 hover:text-black'"
                >
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.818V8.044a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span>Hanya Peta</span>
                </button>
            </div>

            <!-- Mobile Layout Buttons (< lg screens) -->
            <div class="flex lg:hidden items-center gap-1.5 bg-white border-3 border-black p-1 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] w-full">
                <button 
                    type="button" 
                    @click="mobileMode = 'list'" 
                    class="flex-1 py-2 rounded-lg font-black text-xs uppercase transition-all cursor-pointer flex items-center justify-center gap-1.5"
                    :class="mobileMode === 'list' ? 'bg-yellow-400 text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'text-zinc-600 hover:text-black'"
                >
                    <span>📋 Lihat Daftar</span>
                </button>
                <button 
                    type="button" 
                    @click="mobileMode = 'map'; setTimeout(() => { if(map && map.invalidateSize) map.invalidateSize(); }, 150)" 
                    class="flex-1 py-2 rounded-lg font-black text-xs uppercase transition-all cursor-pointer flex items-center justify-center gap-1.5"
                    :class="mobileMode === 'map' ? 'bg-yellow-400 text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'text-zinc-600 hover:text-black'"
                >
                    <span>🗺️ Lihat Peta</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Listing & Map Layout Container -->
    <div class="relative">
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
            <div 
                class="grid grid-cols-1 gap-6"
                :class="{
                    'lg:grid-cols-12': viewMode === 'split',
                    'lg:grid-cols-1': viewMode === 'list' || viewMode === 'map'
                }"
            >
                <!-- Cards List Column -->
                <div 
                    x-show="(viewMode === 'split' || viewMode === 'list') && (mobileMode === 'list')"
                    class="space-y-6"
                    :class="{
                        'lg:col-span-7': viewMode === 'split',
                        'lg:col-span-12': viewMode === 'list'
                    }"
                >
                    <div 
                        class="grid grid-cols-1 md:grid-cols-2 gap-6"
                        :class="{
                            'lg:grid-cols-2': viewMode === 'split',
                            'lg:grid-cols-3': viewMode === 'list'
                        }"
                    >
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

                                        <!-- Top Left Badges -->
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

                                        <!-- Top Right District Badge -->
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

                                        <!-- Price & Facilities -->
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
                </div>

                <!-- Sticky Map Column -->
                <div 
                    x-show="(viewMode === 'split' || viewMode === 'map') || (mobileMode === 'map')"
                    class="space-y-4"
                    :class="{
                        'lg:col-span-5': viewMode === 'split',
                        'lg:col-span-12': viewMode === 'map'
                    }"
                >
                    <div class="sticky top-24 rounded-2xl border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden bg-white">
                        <div class="p-3.5 bg-yellow-300 border-b-3 border-black flex items-center justify-between">
                            <span class="font-black text-xs uppercase text-black flex items-center gap-1.5 tracking-tight">
                                📍 Peta Interaktif Kost Bandung
                            </span>
                            <span class="text-[10px] font-black text-black bg-white border border-black px-2.5 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] uppercase">
                                <span x-text="items.length"></span> Kost Tampil
                            </span>
                        </div>

                        <!-- Map Canvas Container -->
                        <div x-ref="catalogMapElement" class="w-full h-[520px] lg:h-[620px] bg-zinc-100 z-0"></div>
                    </div>
                </div>
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
                        @if($search && !$gender && !$district && !$price_min && !$price_max)
                            Tidak ada kost yang cocok dengan kata kunci "{{ $search }}". Coba hapus pencarian atau gunakan kata kunci lain.
                        @elseif($search || $gender || $district || $price_min || $price_max)
                            Tidak ada kost yang cocok dengan kriteria filter Anda. Coba ubah atau reset filter pencarian.
                        @else
                            Belum ada daftar kost yang terdaftar saat ini.
                        @endif
                    </p>
                </div>
                <button 
                    type="button"
                    wire:click="resetFilters" 
                    @click="if($refs.searchInput) $refs.searchInput.value = ''; wasApplied = false; setTimeout(() => checkFilter(), 150)"
                    class="px-6 py-3 bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl inline-flex items-center gap-2 cursor-pointer"
                >
                    <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    @if($search && !$gender && !$district && !$price_min && !$price_max)
                        <span>Hapus Pencarian</span>
                    @else
                        <span>Tampilkan Semua Kost</span>
                    @endif
                </button>
            </div>
        @endif
    </div>
</div>
