<div
    class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

        <!-- Top Header & Back Button -->
        <div>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 text-xs font-black uppercase text-black bg-white border-2 border-black px-3.5 py-2 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-yellow-300 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg mb-6 group">
                <svg class="w-4 h-4 text-black group-hover:-translate-x-1 transition-transform stroke-[3]" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Dashboard</span>
            </a>

            <div
                class="bg-yellow-300 border-4 border-black p-6 md:p-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] rounded-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span
                        class="px-3 py-1 bg-black text-yellow-300 font-extrabold text-xs uppercase tracking-wider border border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        Form Pendaftaran
                    </span>
                    <h1 class="text-3xl md:text-4xl font-black text-black tracking-tight uppercase mt-2">
                        Tambah Properti Kost Baru
                    </h1>
                    <p class="text-sm font-bold text-black/80 mt-1">
                        Isi detail properti kost Anda dengan lengkap untuk menarik minat pencari kost di Kota Bandung.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Start -->
        <form wire:submit.prevent="save" x-data="{ formIsOutOfBounds: false }" @bounds-update.window="formIsOutOfBounds = $event.detail" class="space-y-8">

            <!-- Seksi 1: Informasi Dasar -->
            <div
                class="bg-white rounded-xl p-6 md:p-8 border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <div class="flex items-center gap-3 border-b-3 border-black pb-4">
                    <div
                        class="w-10 h-10 rounded bg-black text-yellow-300 border-2 border-black font-black text-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        1
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Informasi Dasar</h2>
                        <p class="text-xs font-bold text-zinc-600">Nama kost, jenis penghuni, dan deskripsi singkat</p>
                    </div>
                </div>

                <!-- Nama Kost -->
                <div class="space-y-2">
                    <label for="name" class="block text-xs font-black uppercase tracking-wider text-black">
                        Nama Properti Kost <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" id="name" wire:model="name"
                        placeholder="Contoh: Kost Eksklusif Dago Asri"
                        class="w-full bg-white border-2 border-black rounded-lg px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                    @error('name')
                        <p
                            class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                            {{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Penghuni -->
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-black">
                        Tipe Penghuni Kost <span class="text-rose-600">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="gender_type" value="campur" class="peer sr-only">
                            <div
                                class="px-4 py-3.5 rounded-lg border-2 border-black text-center font-black text-xs md:text-sm text-black bg-zinc-50 hover:bg-yellow-100 peer-checked:bg-yellow-400 peer-checked:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-black stroke-[2.5]" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>Campur</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" wire:model="gender_type" value="putri" class="peer sr-only">
                            <div
                                class="px-4 py-3.5 rounded-lg border-2 border-black text-center font-black text-xs md:text-sm text-black bg-zinc-50 hover:bg-pink-100 peer-checked:bg-pink-400 peer-checked:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-black stroke-[2.5]" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 14c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z" />
                                    <circle cx="12" cy="7" r="4" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.5 9c.5.8 1.5 1.2 2.5 1.2s2-.4 2.5-1.2" />
                                </svg>
                                <span>Khusus Putri</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" wire:model="gender_type" value="putra" class="peer sr-only">
                            <div
                                class="px-4 py-3.5 rounded-lg border-2 border-black text-center font-black text-xs md:text-sm text-black bg-zinc-50 hover:bg-cyan-100 peer-checked:bg-cyan-300 peer-checked:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-black stroke-[2.5]" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z" />
                                </svg>
                                <span>Khusus Putra</span>
                            </div>
                        </label>
                    </div>
                    @error('gender_type')
                        <p
                            class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                            {{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Kost -->
                <div x-data="{
                    desc: @entangle('description').live,
                    get count() { return (this.desc || '').length }
                }" class="space-y-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <label for="description" class="block text-xs font-black uppercase tracking-wider text-black">
                            Deskripsi Lengkap <span class="text-rose-600">*</span>
                        </label>
                        <span class="text-[11px] font-bold italic text-zinc-600">
                            Tips: sebutkan lokasi, fasilitas unggulan, dan lingkungan sekitar dalam 3–5 kalimat singkat.
                        </span>
                    </div>

                    <div class="relative">
                        <textarea id="description" x-model="desc" rows="4" maxlength="500"
                            placeholder="Contoh: Kost khusus putra di Coblong, 5 menit dari kampus. Kamar full furnished, AC, WiFi cepat. Lingkungan aman, dekat minimarket & warung makan. Cocok untuk mahasiswa/karyawan."
                            class="w-full bg-white border-2 border-black rounded-lg p-4 pb-10 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all"></textarea>

                        <!-- Neo-Brutalist Live Character Counter Badge -->
                        <div class="absolute bottom-3 right-3 pointer-events-none">
                            <span
                                class="px-2.5 py-1 text-[10px] font-black uppercase rounded border-2 transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-block"
                                :class="{
                                    'bg-zinc-100 text-zinc-700 border-black': count <= 299,
                                    'bg-yellow-300 text-black border-black': count >= 300 && count < 500,
                                    'bg-rose-100 text-rose-700 border-rose-500': count >= 500
                                }">
                                <span x-text="count"></span>/500 karakter
                            </span>
                        </div>
                    </div>

                    @error('description')
                        <p
                            class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                            {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Seksi 2: Lokasi & Geofencing -->
            <script>
                window.bandungDistricts = @json(config('bandung.districts', []));
                if (!window.kostMapInit) {
                    window.kostMapInit = function() {
                        return {
                            map: null, marker: null, isOutOfBounds: false, reverseGeocodeTimeout: null,
                            hasGoogleKey: '{{ $googleMapsApiKey }}',
                            get districtsData() { return window.bandungDistricts || {}; },

                            resetToDefaultLocation: function() {
                                this.address = '';
                                this.district = '';
                                this.districtAutoMessage = null;
                                this.lat = '-6.917464';
                                this.lng = '107.619123';
                                this.isOutOfBounds = false;
                                var lat0 = -6.917464, lng0 = 107.619123;
                                if (this.map) {
                                    if (typeof L !== 'undefined' && this.marker && this.marker.setLatLng) {
                                        this.marker.setLatLng([lat0, lng0]);
                                        this.map.setView([lat0, lng0], 13);
                                    } else if (window.google && this.marker && this.marker.setPosition) {
                                        this.marker.setPosition({ lat: lat0, lng: lng0 });
                                        this.map.panTo({ lat: lat0, lng: lng0 });
                                        this.map.setZoom(13);
                                    }
                                }
                            },

                            checkBounds: function(lat, lng) {
                                if (!this.district || !this.districtsData[this.district] || !this.districtsData[this.district].bounds) {
                                    this.isOutOfBounds = false;
                                    window.dispatchEvent(new CustomEvent('bounds-update', { detail: false }));
                                    return;
                                }
                                var b = this.districtsData[this.district].bounds;
                                this.isOutOfBounds = !(lat >= b.lat_min && lat <= b.lat_max && lng >= b.lng_min && lng <= b.lng_max);
                                window.dispatchEvent(new CustomEvent('bounds-update', { detail: this.isOutOfBounds }));
                            },

                            setCoords: function(newLat, newLng) {
                                this.lat = newLat.toFixed(6);
                                this.lng = newLng.toFixed(6);
                                this.checkBounds(newLat, newLng);
                                clearTimeout(this.reverseGeocodeTimeout);
                                var self = this;
                                this.reverseGeocodeTimeout = setTimeout(function() { self.reverseGeocode(newLat, newLng); }, 800);
                            },

                            matchDistrict: function(components, source) {
                                var normalize = function(str) {
                                    if (!str) return '';
                                    return str.replace(/^kecamatan\s+/i,'').replace(/^kec\.?\s+/i,'').trim().toLowerCase();
                                };
                                
                                var keys = Object.keys(this.districtsData);
                                var checkAgainstKeys = function(val) {
                                    if (!val) return null;
                                    var norm = normalize(val);
                                    for (var j = 0; j < keys.length; j++) {
                                        if (keys[j].toLowerCase() === norm) return keys[j];
                                    }
                                    for (var k = 0; k < keys.length; k++) {
                                        var kn = keys[k].toLowerCase().replace(/\s+/g,' ');
                                        var vn = norm.replace(/\s+/g,' ');
                                        if (kn.includes(vn) || vn.includes(kn)) return keys[k];
                                    }
                                    return null;
                                };

                                if (source === 'google') {
                                    var levels = ['administrative_area_level_3','administrative_area_level_4','sublocality_level_1','sublocality'];
                                    for (var i = 0; i < levels.length; i++) {
                                        var comp = components.find(function(c) { return c.types.includes(levels[i]); });
                                        if (comp) {
                                            var match = checkAgainstKeys(comp.long_name) || checkAgainstKeys(comp.short_name);
                                            if (match) return match;
                                        }
                                    }
                                } else if (source === 'nominatim') {
                                    var fields = ['district', 'city_district', 'suburb', 'county', 'village', 'town', 'municipality'];
                                    for (var i = 0; i < fields.length; i++) {
                                        if (components[fields[i]]) {
                                            var match = checkAgainstKeys(components[fields[i]]);
                                            if (match) return match;
                                        }
                                    }
                                }
                                return null;
                            },

                            updateDistrictFromMatch: function(matched) {
                                if (matched) {
                                    if (!this.district) {
                                        this.districtAutoMessage = 'Kecamatan terdeteksi otomatis dari lokasi.';
                                    } else if (this.district !== matched) {
                                        this.districtAutoMessage = 'Kecamatan disesuaikan: ' + this.district + ' ke ' + matched;
                                    }
                                    this.district = matched;
                                } else {
                                    this.districtAutoMessage = 'Kecamatan tidak dapat dideteksi otomatis, silakan pilih manual.';
                                }
                                var lt = parseFloat(this.lat), ln = parseFloat(this.lng);
                                if (!isNaN(lt) && !isNaN(ln)) this.checkBounds(lt, ln);
                            },

                            cleanAddress: function(str) {
                                if (!str) return '';
                                return str.replace(/^[A-Z0-9]{4,8}\+[A-Z0-9]{2,4}\s*,?\s*/i, '').trim();
                            },

                            reverseGeocode: function(lat, lng) {
                                var self = this;
                                self.isReverseGeocoding = true;

                                var useNominatimReverse = function() {
                                    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&addressdetails=1')
                                        .then(function(r) { return r.json(); })
                                        .then(function(d) {
                                            if (d) {
                                                if (d.address) self.updateDistrictFromMatch(self.matchDistrict(d.address, 'nominatim'));
                                                if (d.display_name) self.address = self.cleanAddress(d.display_name);
                                            }
                                            setTimeout(function() { self.isReverseGeocoding = false; }, 400);
                                        })
                                        .catch(function(e) {
                                            console.warn('Nominatim reverse error', e);
                                            self.isReverseGeocoding = false;
                                        });
                                };

                                if (window.google && window.google.maps && window.google.maps.Geocoder) {
                                    try {
                                        new google.maps.Geocoder().geocode({ location: { lat: lat, lng: lng } }, function(results, status) {
                                            if (status === 'OK' && results && results[0]) {
                                                self.updateDistrictFromMatch(self.matchDistrict(results[0].address_components, 'google'));
                                                if (results[0].formatted_address) {
                                                    self.address = self.cleanAddress(results[0].formatted_address);
                                                }
                                                setTimeout(function() { self.isReverseGeocoding = false; }, 400);
                                            } else {
                                                useNominatimReverse();
                                            }
                                        });
                                    } catch(err) {
                                        useNominatimReverse();
                                    }
                                } else {
                                    useNominatimReverse();
                                }
                            },

                            geocodeAddress: function(addr) {
                                if (this.isReverseGeocoding) return;

                                if (!addr || !addr.trim()) {
                                    this.resetToDefaultLocation();
                                    return;
                                }

                                var self = this;
                                var q = addr.trim();

                                if (!/bandung/i.test(q)) {
                                    if (this.district && this.district.trim() && !/kecamatan|kec\./i.test(q)) {
                                        q += ', Kecamatan ' + this.district;
                                    }
                                    q += ', Kota Bandung, Jawa Barat';
                                }

                                var useNominatim = function() {
                                    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=1&addressdetails=1')
                                        .then(function(r) { return r.json(); })
                                        .then(function(d) {
                                            if (d && d.length > 0) {
                                                var rl = parseFloat(d[0].lat), rn = parseFloat(d[0].lon);
                                                self.updateDistrictFromMatch(self.matchDistrict(d[0].address, 'nominatim'));
                                                self.setCoords(rl, rn);
                                                if (self.map && typeof L !== 'undefined' && self.map.setView) self.map.setView([rl, rn], 16);
                                            }
                                        }).catch(function(e) { console.warn('Nominatim error', e); });
                                };

                                if (window.google && window.google.maps && window.google.maps.Geocoder) {
                                    new google.maps.Geocoder().geocode({ address: q, componentRestrictions: { country: 'ID' } }, function(results, status) {
                                        if (status === 'OK' && results && results[0]) {
                                            var loc = results[0].geometry.location;
                                            var locType = results[0].geometry.location_type;
                                            self.updateDistrictFromMatch(self.matchDistrict(results[0].address_components, 'google'));
                                            self.setCoords(loc.lat(), loc.lng());
                                            if (self.map && self.map.setCenter) {
                                                self.map.setCenter(loc);
                                                self.map.setZoom(locType === 'ROOFTOP' || locType === 'RANGE_INTERPOLATED' ? 17 : locType === 'APPROXIMATE' ? 14 : 15);
                                            }
                                        } else {
                                            useNominatim();
                                        }
                                    });
                                } else {
                                    useNominatim();
                                }
                            },

                            initMap: function() {
                                var self = this;
                                var lat0 = parseFloat(this.lat) || -6.917464;
                                var lng0 = parseFloat(this.lng) || 107.619123;

                                var setupGoogle = function() {
                                    if (self.map || !window.google || !window.google.maps) return false;
                                    try {
                                        self.map = new google.maps.Map(self.$refs.mapElement, { center: { lat: lat0, lng: lng0 }, zoom: 13, mapTypeControl: false, streetViewControl: false, fullscreenControl: false });
                                        self.marker = new google.maps.Marker({ position: { lat: lat0, lng: lng0 }, map: self.map, draggable: true, title: 'Lokasi Kost Anda' });
                                        self.marker.addListener('dragend', function(e) { self.setCoords(e.latLng.lat(), e.latLng.lng()); });
                                        self.map.addListener('click', function(e) { self.marker.setPosition(e.latLng); self.setCoords(e.latLng.lat(), e.latLng.lng()); });
                                        return true;
                                    } catch(e) { console.warn('Google Maps init error:', e); return false; }
                                };

                                var setupLeaflet = function() {
                                    if (self.map || typeof L === 'undefined') return;
                                    self.map = L.map(self.$refs.mapElement).setView([lat0, lng0], 13);
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(self.map);
                                    self.marker = L.marker([lat0, lng0], { draggable: true }).addTo(self.map);
                                    self.marker.on('dragend', function(e) { var p = e.target.getLatLng(); self.setCoords(p.lat, p.lng); });
                                    self.map.on('click', function(e) { self.marker.setLatLng(e.latlng); self.setCoords(e.latlng.lat, e.latlng.lng); });
                                };

                                var loadLeaflet = function() {
                                    if (typeof L !== 'undefined') { setupLeaflet(); return; }
                                    if (!document.getElementById('leaflet-css')) {
                                        var lk = document.createElement('link'); lk.id = 'leaflet-css'; lk.rel = 'stylesheet'; lk.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'; document.head.appendChild(lk);
                                    }
                                    if (!document.getElementById('leaflet-js')) {
                                        var ls = document.createElement('script'); ls.id = 'leaflet-js'; ls.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'; ls.onload = setupLeaflet; document.body.appendChild(ls);
                                    } else { setupLeaflet(); }
                                };

                                var tryGoogle = function() {
                                    if (window.google && window.google.maps) {
                                        if (!setupGoogle()) loadLeaflet();
                                    } else if (!document.getElementById('google-maps-script')) {
                                        var gs = document.createElement('script');
                                        gs.id = 'google-maps-script';
                                        gs.src = 'https://maps.googleapis.com/maps/api/js?key=' + self.hasGoogleKey;
                                        gs.async = true; gs.defer = true;
                                        gs.onload = function() { if (!setupGoogle()) loadLeaflet(); };
                                        gs.onerror = loadLeaflet;
                                        document.body.appendChild(gs);
                                    } else {
                                        var w = 0;
                                        var poll = setInterval(function() {
                                            w += 100;
                                            if (window.google && window.google.maps) { clearInterval(poll); if (!setupGoogle()) loadLeaflet(); }
                                            else if (w >= 5000) { clearInterval(poll); loadLeaflet(); }
                                        }, 100);
                                    }
                                };

                                if (this.hasGoogleKey) { tryGoogle(); } else { loadLeaflet(); }
                                this.$watch('lat', function() { self.updateMapPosition(); });
                                this.$watch('lng', function() { self.updateMapPosition(); });
                                this.$watch('district', function() { 
                                    var lt = parseFloat(self.lat), ln = parseFloat(self.lng);
                                    if (!isNaN(lt) && !isNaN(ln)) self.checkBounds(lt, ln);
                                });
                            },

                            updateMapPosition: function() {
                                var lt = parseFloat(this.lat), ln = parseFloat(this.lng);
                                if (isNaN(lt) || isNaN(ln) || !this.map || !this.marker) return;
                                if (typeof L !== 'undefined' && this.marker.setLatLng) {
                                    this.marker.setLatLng([lt, ln]); this.map.setView([lt, ln]);
                                } else if (window.google && this.marker.setPosition) {
                                    this.marker.setPosition({ lat: lt, lng: ln }); this.map.panTo({ lat: lt, lng: ln });
                                }
                            }
                        };
                    };
                }
            </script>
            <div x-data="Object.assign(window.kostMapInit(), {
                    lat: @entangle('latitude'),
                    lng: @entangle('longitude'),
                    district: @entangle('district'),
                    districtAutoMessage: @entangle('district_auto_message'),
                    address: @entangle('address')
                })"
                x-init="initMap()"
                @geocode-address.window="geocodeAddress($event.detail.address)"
                class="bg-white rounded-xl p-6 md:p-8 border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <div class="flex items-center gap-3 border-b-3 border-black pb-4">
                    <div
                        class="w-10 h-10 rounded bg-black text-yellow-300 border-2 border-black font-black text-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        2
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Lokasi Kost & Geofencing
                            Bandung</h2>
                        <p class="text-xs font-bold text-zinc-600">Area kecamatan, alamat fisik, dan penentuan titik
                            presisi lokasi pada peta</p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Dropdown Kecamatan -->
                    <div class="space-y-2 md:w-50 flex-shrink-0">
                        <label for="district" class="block text-xs font-black uppercase tracking-wider text-black">
                            Kecamatan <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <select id="district" wire:model.live="district" x-model="district" @change="districtAutoMessage = null"
                                class="w-full bg-white border-2 border-black rounded-lg pl-3.5 pr-9 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] cursor-pointer transition-all appearance-none">
                                <option value="" disabled>-- Pilih Kecamatan --</option>
                                @foreach ($districts as $dist)
                                    <option value="{{ $dist }}" class="font-bold text-sm text-black">Kec.
                                        {{ $dist }}</option>
                                @endforeach
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-black">
                                <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @error('district')
                            <p
                                class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                                {{ $message }}</p>
                        @enderror
                        <!-- District Auto Message -->
                        <div x-show="districtAutoMessage" x-cloak class="mt-2 text-xs font-bold text-sky-700 bg-sky-100 border-2 border-sky-400 px-2.5 py-1.5 rounded-md inline-block shadow-[2px_2px_0px_0px_rgba(14,165,233,1)]">
                            <span x-text="districtAutoMessage"></span>
                        </div>
                    </div>

                    <!-- Alamat Lengkap dengan Tombol Clear (X) -->
                    <div class="space-y-2 flex-1">
                        <label for="address" class="block text-xs font-black uppercase tracking-wider text-black">
                            Alamat Lengkap <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="address" wire:model.live.debounce.300ms="address" x-model="address"
                                x-on:keydown.enter.prevent="$el.blur()"
                                placeholder="Contoh: Jl. Dipatiukur No. 80, RT 02/RW 05"
                                class="w-full bg-white border-2 border-black rounded-lg pl-4 pr-10 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                            <button type="button" x-show="address" x-cloak @click="resetToDefaultLocation()"
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-zinc-400 hover:text-black transition-colors"
                                title="Hapus alamat dan reset lokasi">
                                <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        @error('address')
                            <p
                                class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                                {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Peta Interaktif & Pin Picker (Geofencing Bandung) -->
                <div class="space-y-3 pt-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <label class="block text-xs font-black uppercase tracking-wider text-black">
                            Tentukan Titik Presisi Lokasi (Peta Interaktif Bandung) <span
                                class="text-rose-600">*</span>
                        </label>
                        <div
                            class="inline-flex items-center gap-1.5 bg-yellow-300 border-2 border-black px-3 py-1 rounded text-xs font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            <span>📍 Lat: <span x-text="lat"></span> | Lng: <span x-text="lng"></span></span>
                        </div>
                    </div>

                    <p class="text-xs font-bold text-zinc-600">
                        Geser marker/pin merah atau klik di mana saja pada peta untuk menandai titik fisik kost Anda.
                        Titik harus berada di dalam batas administratif Kota Bandung.
                    </p>

                    <!-- Google Maps / Leaflet Canvas -->
                    <div class="relative" wire:ignore>
                        <div x-ref="mapElement"
                            class="w-full h-80 rounded-xl border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] z-0 bg-zinc-100">
                        </div>
                    </div>

                    <!-- Client-Side Geofencing Warning -->
                    <div x-show="isOutOfBounds" x-cloak
                        class="p-4 bg-rose-100 border-3 border-black rounded-xl text-rose-700 font-black text-xs shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-1">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-rose-600 shrink-0 stroke-[2.5]" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-sm font-black text-rose-800 uppercase">📍 Lokasi Di Luar Batas
                                Kecamatan!</span>
                        </div>
                        <p class="text-xs font-bold text-rose-900 pl-7">
                            Titik lokasi yang Anda pilih berada di luar wilayah administratif kecamatan <span
                                class="underline font-black text-rose-950" x-text="district"></span> Kota Bandung.
                            Silakan geser pin kembali ke dalam area kecamatan ini atau ubah pilihan kecamatan.
                        </p>
                    </div>

                    @error('latitude')
                        <div
                            class="p-3 bg-rose-100 border-3 border-black rounded-xl text-rose-700 font-black text-xs shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] flex items-center gap-2">
                            <svg class="w-5 h-5 text-rose-600 shrink-0 stroke-[2.5]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                    @error('longitude')
                        <div
                            class="p-3 bg-rose-100 border-3 border-black rounded-xl text-rose-700 font-black text-xs shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] flex items-center gap-2">
                            <svg class="w-5 h-5 text-rose-600 shrink-0 stroke-[2.5]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Seksi 3: Harga & Fasilitas -->
            <div
                class="bg-white rounded-xl p-6 md:p-8 border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <div class="flex items-center gap-3 border-b-3 border-black pb-4">
                    <div
                        class="w-10 h-10 rounded bg-black text-yellow-300 border-2 border-black font-black text-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        3
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Harga & Fasilitas</h2>
                        <p class="text-xs font-bold text-zinc-600">Tarif sewa bulanan dan fasilitas pendukung yang
                            disediakan</p>
                    </div>
                </div>

                <!-- Harga & Jumlah Kamar Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Harga Sewa per Bulan -->
                    <div class="space-y-2">
                        <label for="price_monthly"
                            class="block text-xs font-black uppercase tracking-wider text-black">
                            Harga Sewa Per Bulan (IDR) <span class="text-rose-600">*</span>
                        </label>
                        <div
                            class="relative rounded-lg overflow-hidden flex border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                            <div
                                class="bg-yellow-300 border-r-2 border-black px-4 flex items-center font-black text-sm text-black">
                                Rp
                            </div>
                            <input type="number" id="price_monthly" wire:model="price_monthly"
                                placeholder="1500000" min="0" oninput="if(this.value < 0) this.value = 0"
                                class="w-full bg-white px-4 py-3 text-sm font-black text-black focus:outline-none focus:bg-yellow-50 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <div
                                class="bg-zinc-100 border-l-2 border-black px-4 flex items-center text-xs font-black text-black uppercase">
                                / Bln
                            </div>
                        </div>
                        @error('price_monthly')
                            <p
                                class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                                {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Kamar -->
                    <div class="space-y-2">
                        <label for="total_rooms" class="block text-xs font-black uppercase tracking-wider text-black">
                            Total Jumlah Kamar <span class="text-rose-600">*</span>
                        </label>
                        <input type="number" id="total_rooms" wire:model="total_rooms" placeholder="10"
                            min="0" oninput="if(this.value < 0) this.value = 0"
                            class="w-full bg-white border-2 border-black rounded-lg px-4 py-3 text-sm font-black text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        @error('total_rooms')
                            <p
                                class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                                {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kamar Tersedia -->
                    <div class="space-y-2">
                        <label for="available_rooms"
                            class="block text-xs font-black uppercase tracking-wider text-black">
                            Sisa Kamar Kosong <span class="text-rose-600">*</span>
                        </label>
                        <input type="number" id="available_rooms" wire:model="available_rooms" placeholder="2"
                            min="0" oninput="if(this.value < 0) this.value = 0"
                            class="w-full bg-white border-2 border-black rounded-lg px-4 py-3 text-sm font-black text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        @error('available_rooms')
                            <p
                                class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                                {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Checkbox Fasilitas Kost -->
                <div class="space-y-3 pt-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-black">
                        Fasilitas Kost (Pilih yang tersedia)
                    </label>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach ($facilities as $facility)
                            <label class="cursor-pointer">
                                <input type="checkbox" wire:model="selectedFacilities" value="{{ $facility->id }}"
                                    class="peer sr-only">
                                <div
                                    class="px-4 py-3 rounded-lg border-2 border-black bg-zinc-50 text-black text-xs font-black flex items-center justify-between peer-checked:bg-lime-300 peer-checked:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-yellow-100 transition-all">
                                    <span>{{ $facility->name }}</span>
                                    <span
                                        class="w-5 h-5 rounded border-2 border-black bg-white flex items-center justify-center text-black opacity-0 peer-checked:opacity-100 font-black text-xs">✓</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedFacilities')
                        <p
                            class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                            {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Seksi 4: Foto Utama Kost -->
            <div
                class="bg-white rounded-xl p-6 md:p-8 border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <div class="flex items-center gap-3 border-b-3 border-black pb-4">
                    <div
                        class="w-10 h-10 rounded bg-black text-yellow-300 border-2 border-black font-black text-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        4
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Foto Utama Properti</h2>
                        <p class="text-xs font-bold text-zinc-600">Unggah foto fasad atau kamar terbaik properti kost
                            Anda</p>
                    </div>
                </div>

                <!-- Drag & Drop Upload Dropzone -->
                <div x-data="{
                    isUploading: false,
                    progress: 0,
                    startUpload() {
                        this.isUploading = true;
                        this.progress = 0;
                    },
                    updateProgress(val) {
                        this.progress = val;
                    },
                    finishUpload() {
                        this.progress = 100;
                        setTimeout(() => {
                            this.isUploading = false;
                            this.progress = 0;
                        }, 400);
                    },
                    errorUpload() {
                        this.isUploading = false;
                        this.progress = 0;
                    }
                }" x-on:livewire-upload-start="startUpload()"
                    x-on:livewire-upload-finish="finishUpload()" x-on:livewire-upload-error="errorUpload()"
                    x-on:livewire-upload-progress="updateProgress($event.detail.progress)" class="space-y-4">
                    <div
                        class="relative border-3 border-dashed border-black rounded-xl p-8 text-center bg-yellow-100/70 hover:bg-yellow-200/80 transition-all cursor-pointer group shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <input type="file" wire:model="photos" multiple accept="image/*"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="space-y-3 pointer-events-none">
                            <div
                                class="w-14 h-14 rounded-lg bg-white border-2 border-black flex items-center justify-center mx-auto text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] group-hover:scale-110 transition-transform">
                                <svg class="w-7 h-7 stroke-[2]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-black text-black uppercase">
                                    Klik atau seret file foto ke area ini
                                </p>
                                <p
                                    class="text-xs font-black text-black mt-2 bg-black text-yellow-300 inline-block px-3 py-1 rounded border border-black">
                                    WAJIB UNGGAH MINIMAL 4 FOTO, MAKSIMAL 10 FOTO (MAKS 2MB/FOTO)
                                </p>
                                <p class="text-xs font-bold text-zinc-600 mt-1.5">Format: JPG, PNG, WEBP</p>
                                <span
                                    class="inline-block mt-3 bg-yellow-400 text-black font-black text-xs uppercase px-4 py-2 border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] group-hover:bg-yellow-300 transition-all rounded">
                                    Pilih File Foto
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Neo-Brutalist Upload Status & Preview Container (Unified No-Shift Card) -->
                    <div x-show="isUploading || {{ count($photos) > 0 ? 'true' : 'false' }}" x-cloak
                        class="bg-lime-100 border-3 border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-200">
                        <!-- State 1: Upload Progress in Track -->
                        <div x-show="isUploading" class="space-y-2.5 font-black text-black">
                            <div class="flex items-center justify-between text-xs uppercase">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Mengunggah Foto...</span>
                                </span>
                                <span x-text="progress + '%'"
                                    class="bg-yellow-300 border-2 border-black px-2.5 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] text-xs font-black">0%</span>
                            </div>

                            <!-- Progress Track -->
                            <div
                                class="w-full bg-white border-2 border-black rounded-lg h-6 p-0.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden">
                                <!-- Progress Fill -->
                                <div class="bg-lime-400 border-r-2 border-black h-full transition-all duration-300 ease-out rounded-sm"
                                    :style="'width: ' + progress + '%'"></div>
                            </div>
                        </div>

                        <!-- State 2: Upload Complete & Photo Preview -->
                        @if (count($photos) > 0)
                            <div class="space-y-4">
                                <div class="flex items-center justify-between border-b-2 border-black pb-2">
                                    <div class="text-xs font-black text-black uppercase flex items-center gap-2">
                                        Preview Foto
                                        <span
                                            class="px-2 py-0.5 rounded border-2 border-black text-xs font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] {{ count($photos) < 4 ? 'bg-rose-300 text-black' : (count($photos) > 10 ? 'bg-rose-400 text-black' : 'bg-lime-300 text-black') }}">
                                            {{ count($photos) }}/10
                                        </span>
                                        @if (count($photos) < 4)
                                            <span
                                                class="text-[10px] font-black text-rose-600 bg-rose-100 border border-rose-400 px-1.5 py-0.5 rounded uppercase">Kurang
                                                {{ 4 - count($photos) }} foto lagi</span>
                                        @elseif(count($photos) > 10)
                                            <span
                                                class="text-[10px] font-black text-rose-600 bg-rose-100 border border-rose-400 px-1.5 py-0.5 rounded uppercase">Melebihi
                                                batas maksimum!</span>
                                        @else
                                            <span
                                                class="text-[10px] font-black text-lime-700 bg-lime-100 border border-lime-500 px-1.5 py-0.5 rounded uppercase">✓
                                                Jumlah valid</span>
                                        @endif
                                    </div>
                                    @error('photos')
                                        <span
                                            class="text-xs font-bold text-rose-500 bg-rose-100 border-2 border-black px-2 py-0.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    @foreach ($photos as $index => $photo)
                                        <div
                                            class="relative group aspect-[4/3] rounded-lg border-3 border-black overflow-hidden shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] bg-zinc-200">
                                            <img src="{{ $photo->temporaryUrl() }}"
                                                alt="Preview Foto {{ $index + 1 }}"
                                                class="w-full h-full object-cover">

                                            <!-- Remove Button -->
                                            <button type="button" wire:click="removePhoto({{ $index }})"
                                                class="absolute top-2 right-2 w-7 h-7 bg-rose-400 hover:bg-rose-300 border-2 border-black rounded text-black font-black text-[10px] shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 cursor-pointer active:translate-x-0.5 active:translate-y-0.5 active:shadow-none z-20"
                                                title="Hapus Foto">
                                                &#x2715;
                                            </button>

                                            <!-- Primary Badge -->
                                            @if ($index === 0)
                                                <div
                                                    class="absolute bottom-2 left-2 bg-yellow-400 text-black text-[9px] font-black uppercase px-2 py-0.5 border-2 border-black rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] pointer-events-none">
                                                    Foto Utama
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                @foreach ($photos as $index => $photo)
                                    @error("photos.{$index}")
                                        <span
                                            class="block text-[10px] font-bold text-rose-500 bg-rose-100 border-2 border-black px-2 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mt-1">Foto
                                            ke-{{ $index + 1 }}: {{ $message }}</span>
                                    @enderror
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Submit & Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t-3 border-black">
                <a href="{{ route('dashboard') }}"
                    class="px-6 py-3 bg-white hover:bg-zinc-100 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded">
                    Batal
                </a>

                <button type="submit" wire:loading.attr="disabled" wire:target="save" x-bind:disabled="formIsOutOfBounds"
                    :class="formIsOutOfBounds ? 'opacity-50 cursor-not-allowed bg-zinc-300' : 'bg-yellow-400 hover:bg-yellow-300 active:translate-x-1 active:translate-y-1 active:shadow-none'"
                    class="px-8 py-3.5 text-black border-3 border-black font-black text-sm uppercase shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] transition-all rounded inline-flex items-center gap-2">
                    <span wire:loading.remove wire:target="save">Simpan Properti Kost</span>
                    <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>

        </form>

    </div>
</div>
