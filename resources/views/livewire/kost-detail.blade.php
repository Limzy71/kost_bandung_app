<div
    class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px] pb-28 lg:pb-16 pt-8"
    x-data="{ showModal: false }"
    @inquiry-sent.window="showModal = false"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Navigation Back Button -->
        <div class="flex items-center justify-between">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center gap-2 bg-white text-black border-3 border-black font-black text-xs uppercase px-4 py-2.5 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all"
            >
                <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Kembali ke Pencarian</span>
            </a>
        </div>

        <!-- ================================================================
             MAIN TWO-COLUMN LAYOUT — eliminates all overlap issues
             Left (lg:col-span-2): Gallery + Content + Map
             Right (lg:col-span-1): Sticky Price Card
             ================================================================ -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            <!-- ============================================================
                 LEFT COLUMN — Photo Gallery + All Content Sections
                 ============================================================ -->
            <div class="lg:col-span-2 space-y-8">

                <!-- PHOTO GALLERY — Clean sub-grid, no bleed into content below -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Primary / Hero Image — spans 2 columns on md+ -->
                    <div class="md:col-span-2 relative group rounded-2xl overflow-hidden border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] bg-zinc-200 aspect-video md:aspect-auto md:h-80">
                        @php
                            $primaryImg = $kost->primaryImage;
                            $primarySrc = $primaryImg
                                ? (Str::startsWith($primaryImg->image_path ?? '', 'http') ? $primaryImg->image_path : Storage::url($primaryImg->image_path))
                                : 'https://placehold.co/800x500/eeeeee/31343c?text=Foto+Utama';
                        @endphp
                        <img
                            src="{{ $primarySrc }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            alt="{{ $kost->name }}"
                        >
                        <button class="absolute bottom-4 right-4 px-4 py-2 bg-yellow-300 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg flex items-center gap-2">
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            <span>Lihat Semua Foto</span>
                        </button>
                    </div>

                    <!-- Thumbnail Stack — 3rd column, stacks vertically -->
                    <div class="flex flex-col gap-4 h-80 md:h-auto">
                        @php
                            $thumbnails = $kost->images->where('is_primary', false)->take(3)->values();
                        @endphp
                        @foreach(range(0, 2) as $i)
                            @php
                                $thumb = $thumbnails->get($i);
                                $thumbSrc = $thumb
                                    ? (Str::startsWith($thumb->image_path ?? '', 'http') ? $thumb->image_path : Storage::url($thumb->image_path))
                                    : "https://placehold.co/400x300/e5e7eb/31343c?text=Foto+".($i+2);
                            @endphp
                            <div class="flex-1 min-h-0 rounded-xl overflow-hidden border-3 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] bg-zinc-200 relative group">
                                <img
                                    src="{{ $thumbSrc }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    alt="Foto {{ $i + 2 }}"
                                >
                                @if($i === 2 && $kost->images->count() > 4)
                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                        <span class="text-white font-black text-2xl">+{{ $kost->images->count() - 4 }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- END PHOTO GALLERY -->

                <!-- MAIN CONTENT CARD -->
                <div class="bg-white border-4 border-black p-6 sm:p-8 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-8">

                    <!-- Badges & Title -->
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3.5 py-1 bg-pink-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                Tipe {{ $kost->gender_type }}
                            </span>

                            @if($kost->is_available)
                                <span class="px-3.5 py-1 bg-lime-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                    ✓ Kamar Tersedia
                                </span>
                            @else
                                <span class="px-3.5 py-1 bg-rose-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                    ✕ Kamar Penuh
                                </span>
                            @endif

                            @if($kost->boosted_at)
                                <span class="px-3.5 py-1 bg-yellow-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4 shrink-0 drop-shadow-[1px_1px_0px_rgba(0,0,0,1)]" viewBox="0 0 20 20">
                                        <defs>
                                            <linearGradient id="bolt-grad-detail" x1="0%" y1="0%" x2="100%" y2="100%">
                                                <stop offset="0%" stop-color="#FBBF24" />
                                                <stop offset="100%" stop-color="#F97316" />
                                            </linearGradient>
                                        </defs>
                                        <path fill="url(#bolt-grad-detail)" stroke="#000000" stroke-width="0.8" fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Properti Rekomendasi</span>
                                </span>
                            @endif
                        </div>

                        <h1 class="text-3xl sm:text-5xl font-black text-black tracking-tight uppercase leading-tight">
                            {{ $kost->name }}
                        </h1>

                        <div class="flex items-start gap-2 text-zinc-700 text-sm sm:text-base font-bold">
                            <svg class="w-5 h-5 text-black shrink-0 stroke-[2.5] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $kost->address }}, Kecamatan {{ $kost->district }}, Kota Bandung</span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t-4 border-black"></div>

                    <!-- Deskripsi -->
                    <div class="space-y-3">
                        <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2">
                            <svg class="w-5 h-5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Tentang Kost Ini</span>
                        </h2>
                        <p class="leading-relaxed text-zinc-700 font-medium text-base sm:text-lg">
                            {{ $kost->description ?? 'Kost nyaman dengan fasilitas modern dan lokasi strategis di Bandung. Cocok untuk Anda yang memiliki mobilitas tinggi namun tetap menginginkan hunian yang tenang dan asri.' }}
                        </p>
                    </div>

                    <!-- Divider -->
                    <div class="border-t-4 border-black"></div>

                    <!-- Fasilitas Utama -->
                    <div class="space-y-4">
                        <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2">
                            <svg class="w-5 h-5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                            <span>Fasilitas Properti</span>
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @forelse($kost->facilities as $facility)
                                <div class="flex items-center gap-2.5 bg-yellow-100 border-2 border-black px-3.5 py-2.5 rounded-xl text-sm font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <span class="text-lime-600 font-extrabold text-base">✓</span>
                                    <span>{{ $facility->name }}</span>
                                </div>
                            @empty
                                <p class="text-zinc-500 font-bold">Tidak ada fasilitas terdaftar.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t-4 border-black"></div>

                    <!-- Aturan Kost -->
                    <div class="space-y-4">
                        <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2">
                            <svg class="w-5 h-5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Aturan Properti</span>
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @forelse($kost->rules as $rule)
                                <div class="flex items-start gap-3 bg-zinc-100 border-2 border-black p-3.5 rounded-xl text-sm font-bold text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <svg class="w-4 h-4 text-pink-600 shrink-0 stroke-[2.5] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>{{ $rule->name }}</span>
                                </div>
                            @empty
                                <p class="text-zinc-500 font-bold">Tidak ada aturan khusus.</p>
                            @endforelse
                        </div>
                    </div>

                </div>
                <!-- END MAIN CONTENT CARD -->

                <!-- ============================================================
                     LOKASI KOST — Interactive Detail Map with Layer Controls
                     ============================================================ -->
                <div class="border-4 border-black bg-white rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">

                    <!-- Section Header -->
                    <div class="bg-yellow-300 border-b-4 border-black px-6 py-4 flex items-center gap-3">
                        <div class="w-9 h-9 bg-black rounded flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-yellow-300 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-black uppercase tracking-tight">Lokasi Kost</h2>
                            <p class="text-xs font-bold text-black/70">{{ $kost->address }}, Kec. {{ $kost->district }}, Kota Bandung</p>
                        </div>
                        <a
                            href="https://www.google.com/maps/search/?api=1&query={{ $kost->latitude }},{{ $kost->longitude }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="ml-auto inline-flex items-center gap-1.5 bg-black text-yellow-300 border-2 border-black font-black text-xs uppercase px-3 py-2 rounded shadow-[3px_3px_0px_0px_rgba(255,255,255,0.4)] hover:bg-zinc-800 transition-all shrink-0"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Google Maps
                        </a>
                    </div>

                    <!-- Map Container with Alpine.js + Leaflet (TileLayer switcher) -->
                    <div
                        x-data="{
                            map: null,
                            currentLayer: 'street',
                            layers: {},
                            initDetailMap() {
                                const lat = {{ (float)($kost->latitude ?? -6.917464) }};
                                const lng = {{ (float)($kost->longitude ?? 107.619123) }};
                                const kostTitle = {{ Js::from($kost->name) }};
                                const kostAddress = {{ Js::from($kost->address . ', Kec. ' . $kost->district . ', Kota Bandung') }};
                                const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
                                const hasGoogleKey = {{ Js::from($googleMapsApiKey ?? '') }};

                                const initLeaflet = () => {
                                    if (this.map || typeof L === 'undefined') return;

                                    this.map = L.map(this.$refs.detailMap, {
                                        center: [lat, lng],
                                        zoom: 16,
                                        zoomControl: true,
                                        attributionControl: true,
                                    });

                                    // Define tile layers
                                    this.layers.street = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        maxZoom: 19,
                                        attribution: '© <a href=\"https://www.openstreetmap.org\">OpenStreetMap</a>'
                                    });

                                    this.layers.satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                                        maxZoom: 19,
                                        attribution: '© Esri, DigitalGlobe, GeoEye'
                                    });

                                    this.layers.terrain = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                                        maxZoom: 17,
                                        attribution: '© <a href=\"https://opentopomap.org\">OpenTopoMap</a>'
                                    });

                                    // Start with street layer
                                    this.layers.street.addTo(this.map);
                                    this.currentLayer = 'street';

                                    // Custom marker
                                    const pinIcon = L.divIcon({
                                        html: `<div style=\"width:36px;height:36px;background:#FACC15;border:3px solid #000;border-radius:50% 50% 50% 0;transform:rotate(-45deg);box-shadow:3px 3px 0 rgba(0,0,0,0.6);\"><div style=\"transform:rotate(45deg);width:100%;height:100%;display:flex;align-items:center;justify-content:center;\"><svg width=\"16\" height=\"16\" fill=\"none\" stroke=\"black\" stroke-width=\"2.5\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6\"/></svg></div></div>`,
                                        iconSize: [36, 36],
                                        iconAnchor: [18, 36],
                                        popupAnchor: [0, -40],
                                        className: ''
                                    });

                                    const marker = L.marker([lat, lng], { icon: pinIcon }).addTo(this.map);

                                    // InfoCard popup
                                    const popupContent = `
                                        <div style=\"font-family:system-ui,sans-serif;min-width:220px;max-width:280px;\">
                                            <div style=\"background:#FACC15;border-bottom:2px solid #000;padding:8px 12px;border-radius:4px 4px 0 0;\">
                                                <p style=\"margin:0;font-size:11px;font-weight:900;text-transform:uppercase;color:#000;letter-spacing:0.05em;\">Lokasi Kost</p>
                                                <p style=\"margin:4px 0 0;font-size:13px;font-weight:900;color:#000;line-height:1.3;\">${kostTitle}</p>
                                            </div>
                                            <div style=\"padding:8px 12px;\">
                                                <p style=\"margin:0;font-size:11px;font-weight:700;color:#3f3f46;line-height:1.4;\">${kostAddress}</p>
                                                <a href=\"${mapsUrl}\" target=\"_blank\" rel=\"noopener noreferrer\" style=\"display:inline-flex;align-items:center;gap:4px;margin-top:8px;background:#000;color:#FACC15;font-size:10px;font-weight:900;text-transform:uppercase;padding:5px 10px;border-radius:4px;text-decoration:none;\">
                                                    <svg width=\"10\" height=\"10\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.5\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14\"/></svg>
                                                    Buka di Google Maps
                                                </a>
                                            </div>
                                        </div>
                                    `;
                                    marker.bindPopup(popupContent, {
                                        maxWidth: 290,
                                        className: 'kost-detail-popup'
                                    }).openPopup();
                                };

                                const loadLeafletAndInit = () => {
                                    if (typeof L !== 'undefined') { initLeaflet(); return; }
                                    if (!document.getElementById('leaflet-detail-css')) {
                                        const link = document.createElement('link');
                                        link.id = 'leaflet-detail-css';
                                        link.rel = 'stylesheet';
                                        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                                        document.head.appendChild(link);
                                    }
                                    if (!document.getElementById('leaflet-detail-js')) {
                                        const script = document.createElement('script');
                                        script.id = 'leaflet-detail-js';
                                        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                                        script.onload = () => initLeaflet();
                                        document.head.appendChild(script);
                                    } else {
                                        setTimeout(() => loadLeafletAndInit(), 200);
                                    }
                                };

                                // Use Google Maps if API key exists, with Leaflet fallback
                                if (hasGoogleKey && !window.google?.maps) {
                                    if (!document.getElementById('google-detail-map-script')) {
                                        window.initGoogleDetailMap = () => {
                                            const mapEl = this.$refs.detailMap;
                                            const gmap = new google.maps.Map(mapEl, {
                                                center: { lat, lng },
                                                zoom: 16,
                                                mapTypeControl: true,
                                                mapTypeControlOptions: {
                                                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                                                    mapTypeIds: ['roadmap', 'satellite', 'terrain'],
                                                },
                                                streetViewControl: false,
                                                fullscreenControl: false,
                                            });
                                            const marker = new google.maps.Marker({
                                                position: { lat, lng },
                                                map: gmap,
                                                title: kostTitle,
                                            });
                                            const infoWindow = new google.maps.InfoWindow({
                                                content: `<div style=\"font-family:system-ui,sans-serif;min-width:200px;\"><div style=\"background:#FACC15;padding:8px 10px;border-bottom:2px solid #000;\"><p style=\"margin:0;font-size:11px;font-weight:900;text-transform:uppercase;\">Lokasi Kost</p><p style=\"margin:4px 0 0;font-weight:900;font-size:13px;\">${kostTitle}</p></div><div style=\"padding:8px 10px;\"><p style=\"margin:0;font-size:11px;font-weight:700;color:#555;\">${kostAddress}</p><a href=\"${mapsUrl}\" target=\"_blank\" style=\"display:inline-flex;align-items:center;gap:4px;margin-top:8px;background:#000;color:#FACC15;font-size:10px;font-weight:900;text-transform:uppercase;padding:5px 10px;border-radius:4px;text-decoration:none;\">Buka di Google Maps ↗</a></div></div>`,
                                            });
                                            infoWindow.open(gmap, marker);
                                            marker.addListener('click', () => infoWindow.open(gmap, marker));
                                        };
                                        const s = document.createElement('script');
                                        s.id = 'google-detail-map-script';
                                        s.src = `https://maps.googleapis.com/maps/api/js?key=${hasGoogleKey}&callback=initGoogleDetailMap`;
                                        s.async = true;
                                        s.defer = true;
                                        s.onerror = () => loadLeafletAndInit();
                                        document.head.appendChild(s);
                                    }
                                    setTimeout(() => { if (!this.map && typeof google === 'undefined') loadLeafletAndInit(); }, 3000);
                                } else if (hasGoogleKey && window.google?.maps) {
                                    // Google Maps already loaded
                                    window.initGoogleDetailMap && window.initGoogleDetailMap();
                                } else {
                                    loadLeafletAndInit();
                                }
                            },
                            switchLayer(type) {
                                if (!this.map || !this.layers[type] || this.currentLayer === type) return;
                                this.map.removeLayer(this.layers[this.currentLayer]);
                                this.layers[type].addTo(this.map);
                                this.currentLayer = type;
                            }
                        }"
                        x-init="initDetailMap()"
                        class="relative"
                    >
                        <!-- Map Type Switcher Buttons (Leaflet-only; Google has built-in controls) -->
                        <div class="absolute top-3 left-3 z-[400] flex gap-1.5" x-show="map !== null && Object.keys(layers).length > 0">
                            <button
                                @click="switchLayer('street')"
                                :class="currentLayer === 'street' ? 'bg-yellow-400 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-white border-black'"
                                class="px-2.5 py-1 text-[10px] font-black uppercase border-2 rounded text-black transition-all"
                                title="Peta Jalan"
                            >🗺 Jalan</button>
                            <button
                                @click="switchLayer('satellite')"
                                :class="currentLayer === 'satellite' ? 'bg-yellow-400 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-white border-black'"
                                class="px-2.5 py-1 text-[10px] font-black uppercase border-2 rounded text-black transition-all"
                                title="Tampilan Satelit"
                            >🛰 Satelit</button>
                            <button
                                @click="switchLayer('terrain')"
                                :class="currentLayer === 'terrain' ? 'bg-yellow-400 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-white border-black'"
                                class="px-2.5 py-1 text-[10px] font-black uppercase border-2 rounded text-black transition-all"
                                title="Topografi"
                            >⛰ Topografi</button>
                        </div>

                        <!-- Map Canvas -->
                        <div x-ref="detailMap" class="w-full h-72 sm:h-80 z-0 bg-zinc-100"></div>
                    </div>

                    <!-- Map Footer -->
                    <div class="border-t-3 border-black px-6 py-3 bg-zinc-50 flex items-center justify-between">
                        <p class="text-xs font-bold text-zinc-500">
                            📍 {{ number_format((float)$kost->latitude, 6) }}, {{ number_format((float)$kost->longitude, 6) }}
                        </p>
                        <a
                            href="https://www.google.com/maps/search/?api=1&query={{ $kost->latitude }},{{ $kost->longitude }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 text-xs font-black uppercase text-black hover:text-rose-600 transition-colors"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Buka di Google Maps
                        </a>
                    </div>
                </div>
                <!-- END LOKASI KOST -->

            </div>
            <!-- END LEFT COLUMN -->

            <!-- ============================================================
                 RIGHT COLUMN — Sticky Price Card (strictly inside its column)
                 ============================================================ -->
            <div class="lg:col-span-1">
                <div class="sticky top-8 self-start w-full bg-white border-4 border-black rounded-2xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hidden lg:block space-y-6">

                    <!-- Display Harga -->
                    <div class="bg-yellow-300 border-3 border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-1">
                        <p class="text-xs font-black uppercase tracking-wider text-black">Harga Sewa Bulanan</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-black tracking-tight">
                                Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}
                            </span>
                            <span class="text-xs font-black text-black">/ bulan</span>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="space-y-3">
                        @php
                            $waMessage = rawurlencode("Halo, saya tertarik dengan kost \"" . $kost->name . "\" di " . $kost->district . ". Apakah kamar masih tersedia?");
                        @endphp

                        <a
                            href="https://wa.me/6281234567890?text={{ $waMessage }}"
                            target="_blank"
                            class="w-full py-4 bg-emerald-400 hover:bg-emerald-300 text-black border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2 group"
                        >
                            <svg class="w-5 h-5 text-black stroke-[2.5] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Tanya via WhatsApp</span>
                        </a>

                        <button
                            type="button"
                            @click="showModal = true"
                            class="w-full py-4 bg-cyan-300 hover:bg-cyan-200 text-black border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>Kirim Pesan Internal</span>
                        </button>
                    </div>

                    <!-- Owner Info Card -->
                    <div class="pt-4 border-t-3 border-black text-center space-y-1">
                        <p class="text-xs font-black uppercase text-zinc-500">Disewakan Oleh</p>
                        <p class="text-sm font-black text-black bg-zinc-100 border-2 border-black py-1.5 px-3 rounded-lg inline-flex items-center gap-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            <svg class="w-4 h-4 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>{{ $kost->user->name ?? 'Pemilik Kost' }}</span>
                        </p>
                    </div>

                </div>
            </div>
            <!-- END RIGHT COLUMN -->

        </div>
        <!-- END MAIN TWO-COLUMN LAYOUT -->

    </div>

    <!-- Floating Mobile Bar Neo-Brutalist -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t-4 border-black p-4 shadow-[0_-6px_0px_0px_rgba(0,0,0,1)] lg:hidden z-50">
        <div class="flex items-center justify-between gap-4 max-w-7xl mx-auto">
            <div>
                <p class="text-[10px] font-black uppercase text-zinc-500">Harga Sewa</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-lg font-black text-black">Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}</span>
                    <span class="text-[10px] font-bold text-black">/bln</span>
                </div>
            </div>

            @php
                $waMessageMobile = rawurlencode("Halo, saya tertarik dengan kost \"" . $kost->name . "\" di " . $kost->district . ". Apakah kamar masih tersedia?");
            @endphp
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="showModal = true"
                    class="px-4 py-3 bg-cyan-300 hover:bg-cyan-200 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl whitespace-nowrap"
                >
                    Pesan
                </button>
                <a
                    href="https://wa.me/6281234567890?text={{ $waMessageMobile }}"
                    target="_blank"
                    class="px-5 py-3 bg-emerald-400 hover:bg-emerald-300 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl whitespace-nowrap inline-flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span>Tanya WA</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Neo-Brutalist Inquiry Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
        <!-- Backdrop -->
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>

        <!-- Modal Content -->
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-white border-4 border-black rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] w-full max-w-lg relative z-10 flex flex-col max-h-[90vh]">

            <div class="p-5 border-b-4 border-black flex items-center justify-between bg-yellow-300 rounded-t-xl shrink-0">
                <h3 class="text-xl font-black text-black uppercase tracking-tight">Kirim Pesan ke Pemilik</h3>
                <button type="button" @click="showModal = false" class="w-8 h-8 bg-white border-2 border-black rounded flex items-center justify-center text-black hover:bg-rose-400 active:translate-y-0.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none transition-all">
                    <svg class="w-5 h-5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto">
                <form wire:submit.prevent="sendInquiry" class="space-y-4">
                    <div>
                        <label class="block text-sm font-black uppercase text-black mb-1.5">Nama Lengkap</label>
                        <input type="text" wire:model="inquiry_name" class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all" placeholder="Masukkan nama Anda">
                        @error('inquiry_name') <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-black uppercase text-black mb-1.5">Nomor WhatsApp</label>
                        <input type="text" wire:model="inquiry_phone" class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all" placeholder="Contoh: 081234567890">
                        @error('inquiry_phone') <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-black uppercase text-black mb-1.5">Pesan Anda</label>
                        <textarea wire:model="inquiry_message" rows="4" class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all resize-none" placeholder="Tuliskan pertanyaan Anda mengenai ketersediaan kamar, fasilitas, dll..."></textarea>
                        @error('inquiry_message') <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:target="sendInquiry" wire:loading.class="opacity-50 cursor-not-allowed" class="w-full mt-4 py-3.5 bg-cyan-400 hover:bg-cyan-300 text-black border-3 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="sendInquiry">Kirim Sekarang</span>
                        <span wire:loading wire:target="sendInquiry" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Mengirim...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Toast Notification Neo-Brutalist -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="fixed bottom-24 lg:bottom-10 right-4 lg:right-10 z-[110]">
            <div class="bg-lime-400 border-4 border-black p-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex items-center gap-4 max-w-sm">
                <div class="w-10 h-10 bg-white border-2 border-black rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-black stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-black uppercase">Berhasil!</h4>
                    <p class="text-xs font-bold text-black mt-0.5">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-black hover:text-rose-500 transition-colors ml-auto">
                    <svg class="w-5 h-5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif

</div>
