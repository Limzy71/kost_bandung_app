<!-- ============================================================
    SHARED FORM FIELDS — digunakan oleh CreateKost & EditKost
    Variabel: $isEdit (bool), $facilities, $rules,
              $extraPeriodLabels, $districts, $googleMapsApiKey
    ============================================================ -->

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
                        <label class="cursor-pointer h-full">
                            <input type="radio" wire:model="gender_type" value="campur" class="peer sr-only">
                            <div
                                class="h-full min-h-[68px] px-2 py-3 rounded-lg border-2 border-black text-center font-black text-xs md:text-sm text-black bg-zinc-50 hover:bg-yellow-100 peer-checked:bg-yellow-400 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all flex flex-col items-center justify-center gap-1.5">
                                <x-icon name="lucide-users"
                                    class="w-5 h-5 text-black stroke-[2.5] shrink-0" />
                                <span class="leading-tight">Campur</span>
                            </div>
                        </label>

                        <label class="cursor-pointer h-full">
                            <input type="radio" wire:model="gender_type" value="putri" class="peer sr-only">
                            <div
                                class="h-full min-h-[68px] px-2 py-3 rounded-lg border-2 border-black text-center font-black text-xs md:text-sm text-black bg-zinc-50 hover:bg-pink-100 peer-checked:bg-pink-300 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all flex flex-col items-center justify-center gap-1.5">
                                <x-icon name="lucide-user" class="w-5 h-5 text-black stroke-[2.5] shrink-0" />
                                <span class="leading-tight">Khusus Putri</span>
                            </div>
                        </label>

                        <label class="cursor-pointer h-full">
                            <input type="radio" wire:model="gender_type" value="putra" class="peer sr-only">
                            <div
                                class="h-full min-h-[68px] px-2 py-3 rounded-lg border-2 border-black text-center font-black text-xs md:text-sm text-black bg-zinc-50 hover:bg-cyan-100 peer-checked:bg-cyan-300 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all flex flex-col items-center justify-center gap-1.5">
                                <x-icon name="lucide-user" class="w-5 h-5 text-black stroke-[2.5] shrink-0" />
                                <span class="leading-tight">Khusus Putra</span>
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
                var DEFAULT_LAT = -6.917464, DEFAULT_LNG = 107.619123;
                if (!window.kostMapInit) {
                    window.kostMapInit = function() {
                        return {
                            map: null, marker: null, isOutOfBounds: false, isReverseGeocoding: false, reverseGeocodeTimeout: null, ignoreNextAddressWatch: false, markerManuallyMoved: false, isGeocoding: false, _updatingPosition: false, geocodeMessage: '',
                            hasGoogleKey: '{{ $googleMapsApiKey }}',
                            get districtsData() { return window.bandungDistricts || {}; },

                            pickBestNominatim: function(results, query) {
                                if (!results || results.length === 0) return null;
                                var qNorm = query.replace(/^jalan\s+/i, '').trim().toLowerCase();
                                var isStreetLevel = function(cls, type) {
                                    cls = (cls || '').toLowerCase();
                                    type = (type || '').toLowerCase();
                                    return cls === 'highway' || cls === 'building' || cls === 'amenity' || cls === 'shop' ||
                                        (cls === 'place' && (type === 'house' || type === 'plot' || type === 'road'));
                                };
                                var nameMatch = function(item) {
                                    var n = (item.name || '').replace(/^jalan\s+/i, '').trim().toLowerCase();
                                    var d = (item.display_name || '').replace(/^jalan\s+/i, '').trim().toLowerCase();
                                    return n === qNorm || d.startsWith(qNorm + ',') || d === qNorm;
                                };
                                var nameContains = function(item) {
                                    var d = (item.display_name || '').replace(/^jalan\s+/i, '').toLowerCase();
                                    var dWords = d.replace(/[^a-z0-9\s]/g, '').split(/\s+/);
                                    var qWords = qNorm.replace(/[^a-z0-9\s]/g, '').split(/\s+/).filter(function(w) {
                                        return w.length > 2 && !/^\d+$/.test(w);
                                    });
                                    if (qWords.length === 0) return true;
                                    var matches = 0;
                                    for (var i = 0; i < qWords.length; i++) {
                                        if (dWords.indexOf(qWords[i]) !== -1) matches++;
                                    }
                                    return matches >= Math.min(2, qWords.length);
                                };
                                for (var i = 0; i < results.length; i++) {
                                    if (isStreetLevel(results[i].class, results[i].type) && nameMatch(results[i])) return results[i];
                                }
                                for (var i = 0; i < results.length; i++) {
                                    if (isStreetLevel(results[i].class, results[i].type) && nameContains(results[i])) return results[i];
                                }
                                for (var i = 0; i < results.length; i++) {
                                    if (isStreetLevel(results[i].class, results[i].type)) return results[i];
                                }
                                for (var i = 0; i < results.length; i++) {
                                    if ((results[i].class || '').toLowerCase() === 'place') return results[i];
                                }
                                return null;
                            },

                            resetToDefaultLocation: function() {
                                this.address = '';
                                this.geocodeMessage = '';
                                this.district = '';
                                this.districtAutoMessage = null;
                                this.isOutOfBounds = false;
                                this.ignoreNextAddressWatch = false;
                                this.markerManuallyMoved = false;
                                this.isGeocoding = false;
                                this._updatingPosition = true;
                                this.lat = String(DEFAULT_LAT);
                                this.lng = String(DEFAULT_LNG);
                                this._updatingPosition = false;
                                this.updateMapPosition();
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

                            setCoords: function(newLat, newLng, isUserMapInteraction) {
                                this._updatingPosition = true;
                                this.lat = newLat.toFixed(6);
                                this.lng = newLng.toFixed(6);
                                this._updatingPosition = false;
                                this.checkBounds(newLat, newLng);
                                this.updateMapPosition();
                                // Only reverse-geocode (and lock isReverseGeocoding) when user
                                // dragged/clicked the map directly. When called from geocodeAddress
                                // we must NOT set the lock so subsequent keystrokes are not blocked.
                                if (!isUserMapInteraction) return;
                                clearTimeout(this.reverseGeocodeTimeout);
                                var self = this;
                                this.reverseGeocodeTimeout = setTimeout(function() {
                                    self.reverseGeocode(newLat, newLng, true);
                                }, 800);
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

                            reverseGeocode: function(lat, lng, isUserMapInteraction) {
                                var self = this;
                                self.isReverseGeocoding = true;

                                var useNominatimReverse = function() {
                                    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&addressdetails=1')
                                        .then(function(r) { return r.json(); })
                                        .then(function(d) {
                                            if (d) {
                                                if (d.address) self.updateDistrictFromMatch(self.matchDistrict(d.address, 'nominatim'));
                                                if (isUserMapInteraction && d.display_name) {
                                                    self.ignoreNextAddressWatch = true;
                                                    self.address = self.cleanAddress(d.display_name);
                                                }
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
                                                if (isUserMapInteraction && results[0].formatted_address) {
                                                    self.ignoreNextAddressWatch = true;
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
                                var clean = (addr || '').trim();
                                this.geocodeMessage = '';
                                if (!clean) { this.resetToDefaultLocation(); return; }

                                var lower = clean.toLowerCase();
                                if (clean.length < 4 || /^(jl\.?|jalan|gg\.?|gang|no\.?)$/i.test(lower)) return;

                                var self = this;
                                self.isGeocoding = true;

                                var q = clean.replace(/^jl\.?\s+/i, 'Jalan ').replace(/^jalan\s+/i, 'Jalan ');
                                if (!/bandung/i.test(q)) q += ', Kota Bandung, Jawa Barat';
                                else if (self.district && !new RegExp(self.district.replace(/(\s)/g,'\\s'), 'i').test(q)) q += ', ' + self.district;

                                var districtBounds = null;
                                if (self.district && self.districtsData[self.district] && self.districtsData[self.district].bounds) {
                                    districtBounds = self.districtsData[self.district].bounds;
                                }

                                var useNominatim = function() {
                                    var url = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=10&addressdetails=1';
                                    if (districtBounds) {
                                        url += '&viewbox=' + districtBounds.lng_min + ',' + districtBounds.lat_max + ',' + districtBounds.lng_max + ',' + districtBounds.lat_min;
                                    }
                                    fetch(url)
                                        .then(function(r) { return r.json(); })
                                        .then(function(d) {
                                            self.isGeocoding = false;
                                            var picked = self.pickBestNominatim(d, q);
                                            if (picked) {
                                                var rl = parseFloat(picked.lat), rn = parseFloat(picked.lon);
                                                self.geocodeMessage = '';
                                                self.markerManuallyMoved = true;
                                                self.updateDistrictFromMatch(self.matchDistrict(picked.address, 'nominatim'));
                                                self.setCoords(rl, rn);
                                            } else {
                                                self.geocodeMessage = 'Lokasi tidak ditemukan. Coba masukkan alamat atau nama lokasi yang lebih spesifik.';
                                            }
                                        }).catch(function(e) { self.isGeocoding = false; console.warn('Nominatim error', e); });
                                };

                                var tryPlaces = function() {
                                    if (window.google && window.google.maps && window.google.maps.places) {
                                        var dummyDiv = document.createElement('div');
                                        new google.maps.places.PlacesService(dummyDiv).textSearch({ query: q, region: 'id' }, function(results, status) {
                                            if (status === 'OK' && results && results.length > 0) {
                                                var loc = results[0].geometry.location;
                                                var addr = (results[0].formatted_address || '').toLowerCase();
                                                var matched = null, keys = Object.keys(self.districtsData);
                                                for (var i = 0; i < keys.length; i++) {
                                                    if (addr.indexOf(keys[i].toLowerCase()) !== -1) { matched = keys[i]; break; }
                                                }
                                                self.geocodeMessage = '';
                                                self.markerManuallyMoved = true;
                                                self.updateDistrictFromMatch(matched);
                                                self.setCoords(loc.lat(), loc.lng());
                                            } else {
                                                useNominatim();
                                            }
                                        });
                                    } else {
                                        useNominatim();
                                    }
                                };

                                if (window.google && window.google.maps && window.google.maps.Geocoder) {
                                    var geocodeParams = { address: q, componentRestrictions: { country: 'ID' } };
                                    if (districtBounds) {
                                        geocodeParams.bounds = new window.google.maps.LatLngBounds(
                                            new window.google.maps.LatLng(districtBounds.lat_min, districtBounds.lng_min),
                                            new window.google.maps.LatLng(districtBounds.lat_max, districtBounds.lng_max)
                                        );
                                    }
                                    var qClean = q.replace(/^jalan\s+/i, '').replace(/[^a-z0-9\s]/g, '').toLowerCase().split(/\s+/).filter(function(w) { return w.length > 2 && !/^\d+$/.test(w); });
                                    try {
                                        new google.maps.Geocoder().geocode(geocodeParams, function(results, status) {
                                            self.isGeocoding = false;
                                            if (status === 'OK' && results && results.length > 0) {
                                                var best = results[0], bestScore = -1;
                                                for (var i = 0; i < results.length; i++) {
                                                    var addr = (results[i].formatted_address || '').replace(/[^a-z0-9\s]/g, '').toLowerCase();
                                                    var wordMatch = qClean.length === 0 ? 0 : qClean.filter(function(w) { return addr.indexOf(w) !== -1; }).length;
                                                    var s = (results[i].partial_match ? 0 : 10) + wordMatch * 3;
                                                    s += results[i].geometry.location_type === 'ROOFTOP' ? 5 : results[i].geometry.location_type === 'RANGE_INTERPOLATED' ? 3 : 1;
                                                    if (s > bestScore) { bestScore = s; best = results[i]; }
                                                }
                                                var loc = best.geometry.location;
                                                self.geocodeMessage = '';
                                                self.markerManuallyMoved = true;
                                                self.updateDistrictFromMatch(self.matchDistrict(best.address_components, 'google'));
                                                self.setCoords(loc.lat(), loc.lng());
                                            } else {
                                                tryPlaces();
                                            }
                                        });
                                    } catch (e) { console.warn('Google Geocoder threw, fallback to Places/Nominatim', e); self.isGeocoding = false; tryPlaces(); }
                                } else {
                                    useNominatim();
                                }
                            },

                            initMap: function() {
                                var self = this;
                                var lat0 = parseFloat(this.lat) || DEFAULT_LAT;
                                var lng0 = parseFloat(this.lng) || DEFAULT_LNG;

                                var setupGoogle = function() {
                                    if (self.map || !window.google || !window.google.maps) return false;
                                    try {
                                        self.map = new google.maps.Map(self.$refs.mapElement, { center: { lat: lat0, lng: lng0 }, zoom: 13, mapTypeControl: false, streetViewControl: false, fullscreenControl: false });
                                        self.marker = new google.maps.Marker({ position: { lat: lat0, lng: lng0 }, map: self.map, draggable: true, title: 'Lokasi Kost Anda' });
                                        self.marker.addListener('dragend', function(e) { self.markerManuallyMoved = true; self.setCoords(e.latLng.lat(), e.latLng.lng(), true); });
                                        self.map.addListener('click', function(e) { self.markerManuallyMoved = true; self.marker.setPosition(e.latLng); self.setCoords(e.latLng.lat(), e.latLng.lng(), true); });
                                        return true;
                                    } catch(e) { console.warn('Google Maps init error:', e); return false; }
                                };

                                var setupLeaflet = function() {
                                    if (self.map || typeof L === 'undefined') return;
                                    self.map = L.map(self.$refs.mapElement).setView([lat0, lng0], 13);
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(self.map);
                                    self.marker = L.marker([lat0, lng0], { draggable: true }).addTo(self.map);
                                    self.marker.on('dragend', function(e) { self.markerManuallyMoved = true; var p = e.target.getLatLng(); self.setCoords(p.lat, p.lng, true); });
                                    self.map.on('click', function(e) { self.markerManuallyMoved = true; self.marker.setLatLng(e.latlng); self.setCoords(e.latlng.lat, e.latlng.lng, true); });
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
                                        gs.src = 'https://maps.googleapis.com/maps/api/js?key=' + self.hasGoogleKey + '&libraries=places';
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
                                this.$watch('lat', function() { if (!self._updatingPosition) self.updateMapPosition(); });
                                this.$watch('lng', function() { if (!self._updatingPosition) self.updateMapPosition(); });
                                this.$watch('district', function(newVal, oldVal) { 
                                    var lt = parseFloat(self.lat), ln = parseFloat(self.lng);
                                    if (!isNaN(lt) && !isNaN(ln)) self.checkBounds(lt, ln);
                                    
                                    if (newVal && newVal !== oldVal && self.districtsData[newVal] && self.districtsData[newVal].center) {
                                        var center = self.districtsData[newVal].center;
                                        if (!self.markerManuallyMoved) {
                                            self.setCoords(center.lat, center.lng, false);
                                        } else if (self.map) {
                                            if (typeof L !== 'undefined' && self.map.setView) self.map.setView([center.lat, center.lng], 14);
                                            else if (window.google && self.map.panTo) { self.map.panTo({lat: center.lat, lng: center.lng}); self.map.setZoom(14); }
                                        }
                                    }
                                });
                                // Trigger geocoding directly from Alpine — bypasses Livewire round-trip
                                // so every keystroke fires a fresh geocode after 600ms quiet time.
                                var geocodeTimer = null;
                                this.$watch('address', function(val) {
                                    if (self.ignoreNextAddressWatch) {
                                        self.ignoreNextAddressWatch = false;
                                        return;
                                    }
                                    clearTimeout(geocodeTimer);
                                    geocodeTimer = setTimeout(function() {
                                        self.geocodeAddress(val);
                                    }, 600);
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

                <div class="flex flex-col sm:flex-row gap-6 items-start">
                    <!-- Dropdown Kecamatan -->
                    <div class="space-y-2 w-full sm:w-52 shrink-0">
                        <label for="district" class="block text-xs font-black uppercase tracking-wider text-black">
                            Kecamatan <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative w-full">
                            <select id="district" x-model="district" @change="districtAutoMessage = null"
                                style="padding-left: 1.25rem !important;"
                                class="w-full bg-white border-2 border-black rounded-lg !pl-5 pr-10 py-3.5 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] cursor-pointer transition-all appearance-none">
                                <option value="" disabled>-- Pilih Kecamatan --</option>
                                @foreach ($districts as $dist)
                                    <option value="{{ $dist }}" class="font-bold text-sm text-black">Kec.
                                        {{ $dist }}</option>
                                @endforeach
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-black">
                                <x-icon name="lucide-chevron-down" class="w-4 h-4 stroke-[3]" />
                            </div>
                        </div>
                        @error('district')
                            <p
                                class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                                {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat Lengkap dengan Tombol Clear (X) Neo-Brutalist -->
                    <div class="space-y-2 flex-1 w-full min-w-0">
                        <label for="address" class="block text-xs font-black uppercase tracking-wider text-black">
                            Alamat Lengkap / Nama Kost <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative w-full">
                            <input type="text" id="address" x-model="address"
                                x-on:keydown.enter.prevent="$el.blur()"
                                placeholder="Contoh: Jl. Dipatiukur No. 80 atau nama kost"
                                style="padding-left: 1.25rem !important;"
                                class="w-full bg-white border-2 border-black rounded-lg !pl-5 pr-12 py-3.5 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                            <button type="button" x-show="address && !isGeocoding" x-cloak @click="resetToDefaultLocation()"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 w-7 h-7 rounded bg-rose-400 hover:bg-rose-500 text-black border-2 border-black flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-[1px] active:translate-y-[1px] transition-all cursor-pointer"
                                title="Hapus alamat dan reset lokasi">
                                <x-icon name="lucide-x" class="w-4 h-4 stroke-[3]" />
                            </button>
                            <div x-show="isGeocoding" x-cloak class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center justify-center text-zinc-400">
                                <x-icon name="lucide-loader-circle" class="animate-spin w-5 h-5" />
                            </div>
                        </div>
                        @error('address')
                            <p
                                class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                                {{ $message }}</p>
                        @enderror
                        <div x-show="geocodeMessage" x-cloak
                            class="text-xs font-bold text-amber-700 bg-amber-100 border-2 border-amber-500 px-3 py-1.5 rounded-md mt-1 inline-block shadow-[2px_2px_0px_0px_rgba(0,0,0,0.2)]">
                            <span x-text="geocodeMessage"></span>
                        </div>
                    </div>
                </div>
                <!-- District Auto Message -->
                <div x-show="districtAutoMessage" x-cloak
                    class="text-xs font-bold text-zinc-600 bg-zinc-100 border-2 border-zinc-400 px-3 py-1.5 rounded-md shadow-[2px_2px_0px_0px_rgba(0,0,0,0.2)] inline-block whitespace-nowrap">
                    <span x-text="districtAutoMessage"></span>
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
                            <x-icon name="lucide-triangle-alert" class="w-5 h-5 text-rose-600 shrink-0 stroke-[2.5]" />
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
                            <x-icon name="lucide-triangle-alert" class="w-5 h-5 text-rose-600 shrink-0 stroke-[2.5]" />
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                    @error('longitude')
                        <div
                            class="p-3 bg-rose-100 border-3 border-black rounded-xl text-rose-700 font-black text-xs shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] flex items-center gap-2">
                            <x-icon name="lucide-triangle-alert" class="w-5 h-5 text-rose-600 shrink-0 stroke-[2.5]" />
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Seksi 3: Harga, Periode & Fasilitas -->
            <div
                class="bg-white rounded-xl p-6 md:p-8 border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <div class="flex items-center gap-3 border-b-3 border-black pb-4">
                    <div
                        class="w-10 h-10 rounded bg-black text-yellow-300 border-2 border-black font-black text-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        3
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Harga, Periode Sewa &
                            Fasilitas</h2>
                        <p class="text-xs font-bold text-zinc-600">Tarif sewa, periode pembayaran, dan fasilitas
                            pendukung yang disediakan</p>
                    </div>
                </div>

                <!-- Periode Sewa Utama -->
                @php
                    $rentPeriodUnitMap = [
                        'daily' => '/ hari',
                        'weekly' => '/ minggu',
                        'monthly' => '/ bln',
                        'three_monthly' => '/ 3 bln',
                        'six_monthly' => '/ 6 bln',
                        'yearly' => '/ tahun',
                    ];
                    $rentPeriodOptions = [
                        'daily' => 'Per Hari',
                        'weekly' => 'Per Minggu',
                        'monthly' => 'Per Bulan',
                        'three_monthly' => 'Per 3 Bulan',
                        'six_monthly' => 'Per 6 Bulan',
                        'yearly' => 'Per Tahun',
                    ];
                    $rentUnit = $rentPeriodUnitMap[$rent_period] ?? '/ bln';
                @endphp
                <div class="space-y-2">
                    <label for="rent_period" class="block text-xs font-black uppercase tracking-wider text-black">
                        Periode Sewa Utama <span class="text-rose-600">*</span>
                    </label>
                    <select id="rent_period" wire:model.live="rent_period"
                        class="w-full bg-white border-2 border-black rounded-lg px-4 py-3 text-sm font-black text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer">
                        @foreach ($rentPeriodOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] font-bold italic text-zinc-500">
                        Periode penyewaan utama kost. Harga utama di bawah mengikuti satuan periode ini.
                    </p>
                    @error('rent_period')
                        <p
                            class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                            {{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga & Jumlah Kamar Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Harga Sewa Utama -->
                    <div class="space-y-2"
                        x-data="{
                            raw: '{{ $price_monthly }}',
                            formatted: '',
                            init() {
                                if (this.raw) this.formatted = Number(this.raw).toLocaleString('id-ID');
                                this.$watch('raw', v => {
                                    const n = parseInt(v.replace(/\./g, ''), 10);
                                    this.formatted = isNaN(n) ? '' : n.toLocaleString('id-ID');
                                });
                            },
                            onInput(e) {
                                const digits = e.target.value.replace(/[^0-9]/g, '');
                                const n = parseInt(digits, 10);
                                this.formatted = isNaN(n) ? '' : n.toLocaleString('id-ID');
                                this.raw = isNaN(n) ? '' : String(n);
                                this.$wire.set('price_monthly', this.raw);
                            }
                        }">
                        <label for="price_monthly_display"
                            class="block text-xs font-black uppercase tracking-wider text-black">
                            Harga Sewa Utama <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative rounded-lg overflow-hidden flex border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                            <div class="bg-yellow-300 border-r-2 border-black px-4 flex items-center font-black text-sm text-black shrink-0">
                                Rp
                            </div>
                            <input type="text" id="price_monthly_display" inputmode="numeric"
                                x-model="formatted"
                                @input="onInput($event)"
                                placeholder="1.500.000"
                                class="w-full bg-white px-4 py-3 text-sm font-black text-black focus:outline-none focus:bg-yellow-50">
                            <div class="bg-zinc-100 border-l-2 border-black px-4 flex items-center justify-center text-xs font-black text-black uppercase whitespace-nowrap shrink-0">
                                {{ $rentUnit }}
                            </div>
                        </div>
                        <p class="text-[11px] font-bold italic text-zinc-500">Harga utama sesuai periode yang dipilih; untuk filter pencarian dikonversi ke per bulan.</p>
                        @error('price_monthly')
                            <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Uang Deposit -->
                    <div class="space-y-2"
                        x-data="{
                            raw: '{{ $price_deposit }}',
                            formatted: '',
                            init() {
                                if (this.raw) this.formatted = Number(this.raw).toLocaleString('id-ID');
                                this.$watch('raw', v => {
                                    const n = parseInt(v.replace(/\./g, ''), 10);
                                    this.formatted = isNaN(n) ? '' : n.toLocaleString('id-ID');
                                });
                            },
                            onInput(e) {
                                const digits = e.target.value.replace(/[^0-9]/g, '');
                                const n = parseInt(digits, 10);
                                this.formatted = isNaN(n) ? '' : n.toLocaleString('id-ID');
                                this.raw = isNaN(n) ? '' : String(n);
                                this.$wire.set('price_deposit', this.raw);
                            }
                        }">
                        <label for="price_deposit_display"
                            class="block text-xs font-black uppercase tracking-wider text-black">
                            Uang Deposit (Opsional)
                        </label>
                        <div class="relative rounded-lg overflow-hidden flex border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                            <div class="bg-yellow-300 border-r-2 border-black px-4 flex items-center font-black text-sm text-black shrink-0">
                                Rp
                            </div>
                            <input type="text" id="price_deposit_display" inputmode="numeric"
                                x-model="formatted"
                                @input="onInput($event)"
                                placeholder="500.000"
                                class="w-full bg-white px-4 py-3 text-sm font-black text-black focus:outline-none focus:bg-yellow-50">
                        </div>
                        <p class="text-[11px] font-bold italic text-zinc-500">Uang jaminan yang dibayarkan saat masuk. <span class="text-black font-black">Dikembalikan saat keluar.</span></p>
                        @error('price_deposit')
                            <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Capacity Grid (2 Columns Full Width) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Total Kamar -->
                    <div class="space-y-2">
                        <label for="total_rooms" class="block text-xs font-black uppercase tracking-wider text-black">
                            Total Jumlah Kamar <span class="text-rose-600">*</span>
                        </label>
                        <input type="number" id="total_rooms" wire:model="total_rooms" placeholder="10"
                            min="0" oninput="var n = parseInt(this.value, 10); this.value = isNaN(n) ? '' : Math.max(0, n)"
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
                            min="0" oninput="var n = parseInt(this.value, 10); this.value = isNaN(n) ? '' : Math.max(0, n)"
                            class="w-full bg-white border-2 border-black rounded-lg px-4 py-3 text-sm font-black text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        @error('available_rooms')
                            <p
                                class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                                {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Harga Sewa Periode Lain (Opsional) -->
                <div class="space-y-3 pt-2 border-t-2 border-black">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <label class="block text-xs font-black uppercase tracking-wider text-black">
                            Harga Sewa Periode Lain <span class="text-[10px] font-bold normal-case text-zinc-500">(Opsional)</span>
                        </label>
                        <span class="text-[11px] font-bold italic text-zinc-600">
                            Centang lalu isi total bayar untuk periode tersebut. Wajib diisi jika dicentang.
                        </span>
                    </div>

                    @php
                        $periodsArray = collect($extraPeriodLabels)->except($rent_period)->toArray();
                        $periodsKeys = array_keys($periodsArray);
                        $periodsValues = array_values($periodsArray);
                        $leftKeys = array_filter($periodsKeys, fn($k) => array_search($k, $periodsKeys) % 2 === 0);
                        $rightKeys = array_filter($periodsKeys, fn($k) => array_search($k, $periodsKeys) % 2 !== 0);
                    @endphp
                    <div class="grid grid-cols-2 gap-3" wire:key="extra-periods-grid-{{ $rent_period }}" x-data="{ periods: @entangle('extraPeriods').live }">
                        {{-- Kolom Kiri --}}
                        <div class="flex flex-col gap-3">
                            @foreach ($leftKeys as $period)
                                @php $label = $extraPeriodLabels[$period]; @endphp
                                <div
                                    @click="periods.includes('{{ $period }}') ? periods = periods.filter(p => p !== '{{ $period }}') : periods.push('{{ $period }}')"
                                    :class="periods.includes('{{ $period }}') ? 'bg-lime-300' : 'bg-zinc-50 hover:bg-yellow-100'"
                                    class="rounded-lg border-2 border-black p-3 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-colors duration-300 cursor-pointer select-none">
                                    <input type="checkbox" id="extra-period-{{ $period }}" value="{{ $period }}"
                                        :checked="periods.includes('{{ $period }}')" tabindex="-1" class="sr-only">
                                    <span class="flex items-center justify-between gap-2 text-black font-black text-xs md:text-sm">
                                        <span>{{ $label }}</span>
                                        <span
                                            :class="periods.includes('{{ $period }}') ? 'bg-black text-lime-300' : ''"
                                            class="w-5 h-5 rounded border-2 border-black bg-white flex items-center justify-center text-black font-black text-xs shrink-0 transition-colors duration-300">✓</span>
                                    </span>
                                    <div x-cloak @click.stop
                                        class="grid grid-rows-[0fr] opacity-0 transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"
                                        :class="periods.includes('{{ $period }}') ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'"
                                        :inert="!periods.includes('{{ $period }}')">
                                        <div class="overflow-hidden min-h-0">
                                            <div class="pt-2">
                                                <div class="relative rounded-lg overflow-hidden flex border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                                    <span class="bg-yellow-300 border-r-2 border-black px-3 flex items-center font-black text-xs text-black shrink-0">Rp</span>
                                                    <input type="number" wire:model="extraPeriodPrices.{{ $period }}"
                                                        placeholder="Total bayar {{ strtolower($label) }}"
                                                        min="0" oninput="var n = parseInt(this.value, 10); this.value = isNaN(n) ? '' : Math.max(0, n)"
                                                        class="w-full bg-white px-3 py-2 text-sm font-black text-black focus:outline-none focus:bg-yellow-50 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                </div>
                                                @error('extraPeriodPrices.' . $period)
                                                    <span class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{-- Kolom Kanan --}}
                        <div class="flex flex-col gap-3">
                            @foreach ($rightKeys as $period)
                                @php $label = $extraPeriodLabels[$period]; @endphp
                                <div
                                    @click="periods.includes('{{ $period }}') ? periods = periods.filter(p => p !== '{{ $period }}') : periods.push('{{ $period }}')"
                                    :class="periods.includes('{{ $period }}') ? 'bg-lime-300' : 'bg-zinc-50 hover:bg-yellow-100'"
                                    class="rounded-lg border-2 border-black p-3 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-colors duration-300 cursor-pointer select-none">
                                    <input type="checkbox" id="extra-period-{{ $period }}" value="{{ $period }}"
                                        :checked="periods.includes('{{ $period }}')" tabindex="-1" class="sr-only">
                                    <span class="flex items-center justify-between gap-2 text-black font-black text-xs md:text-sm">
                                        <span>{{ $label }}</span>
                                        <span
                                            :class="periods.includes('{{ $period }}') ? 'bg-black text-lime-300' : ''"
                                            class="w-5 h-5 rounded border-2 border-black bg-white flex items-center justify-center text-black font-black text-xs shrink-0 transition-colors duration-300">✓</span>
                                    </span>
                                    <div x-cloak @click.stop
                                        class="grid grid-rows-[0fr] opacity-0 transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"
                                        :class="periods.includes('{{ $period }}') ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'"
                                        :inert="!periods.includes('{{ $period }}')">
                                        <div class="overflow-hidden min-h-0">
                                            <div class="pt-2">
                                                <div class="relative rounded-lg overflow-hidden flex border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                                    <span class="bg-yellow-300 border-r-2 border-black px-3 flex items-center font-black text-xs text-black shrink-0">Rp</span>
                                                    <input type="number" wire:model="extraPeriodPrices.{{ $period }}"
                                                        placeholder="Total bayar {{ strtolower($label) }}"
                                                        min="0" oninput="var n = parseInt(this.value, 10); this.value = isNaN(n) ? '' : Math.max(0, n)"
                                                        class="w-full bg-white px-3 py-2 text-sm font-black text-black focus:outline-none focus:bg-yellow-50 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                </div>
                                                @error('extraPeriodPrices.' . $period)
                                                    <span class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Checkbox Fasilitas Kost -->
                <div class="space-y-5 pt-2">
                    @php
                        $utilityFacilityObj = (object)[
                            'id' => 'include_utilities',
                            'name' => 'Listrik & Air',
                            'is_utility' => true,
                        ];
                        $roomFacilities = $facilities->where('type', 'room');
                        $combinedRoomFacilities = $roomFacilities->toBase()->push($utilityFacilityObj)->sortBy('name');
                        $buildingFacilities = $facilities->where('type', 'building');
                        $parkingFacilities = $facilities->where('type', 'parking');
                    @endphp

                    <!-- Fasilitas Kamar -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <label class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-black">
                                <x-icon name="lucide-user" class="w-4 h-4 stroke-[2.5]" />
                                Fasilitas Kamar
                            </label>
                            <span
                                class="text-[10px] font-black uppercase bg-lime-200 border-2 border-black px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                {{ $combinedRoomFacilities->count() }} pilihan
                            </span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @forelse ($combinedRoomFacilities as $facility)
                                @if (isset($facility->is_utility) && $facility->is_utility)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" wire:model="include_utilities" value="1"
                                            class="peer sr-only">
                                        <div
                                            class="px-4 py-3 rounded-lg border-2 border-black bg-zinc-50 text-black text-xs font-black flex items-center justify-between peer-checked:bg-lime-300 peer-checked:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-yellow-100 transition-all">
                                            <span>Listrik & Air</span>
                                            <span
                                                class="w-5 h-5 rounded border-2 border-black bg-white flex items-center justify-center text-black opacity-0 peer-checked:opacity-100 font-black text-xs shrink-0">✓</span>
                                        </div>
                                    </label>
                                @else
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
                                @endif
                            @empty
                                <p class="text-zinc-500 font-bold text-sm col-span-full">Belum ada fasilitas kamar.</p>
                            @endforelse
                        </div>

                        <div class="mt-2 flex gap-2">
                            <input type="text" wire:model="newRoomFacility"
                                wire:keydown.enter.prevent="addFacility('room')"
                                placeholder="Fasilitas kamar lainnya (kustom)..."
                                maxlength="50"
                                class="w-full bg-white border-2 border-black rounded-lg px-3 py-2 text-xs font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all">
                            <button type="button" wire:click="addFacility('room')"
                                class="px-4 py-2 bg-yellow-300 hover:bg-yellow-400 text-black border-2 border-black font-black text-[10px] uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer shrink-0">
                                + Tambah
                            </button>
                        </div>
                        @error('newRoomFacility')
                            <p class="text-[10px] font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2 py-0.5 rounded-md inline-block">{{ $message }}</p>
                        @enderror

                        @if(collect($customFacilities)->where('type', 'room')->count() > 0)
                            <div class="flex flex-wrap gap-2.5 mt-2.5">
                                @foreach ($customFacilities as $index => $customItem)
                                    @if($customItem['type'] === 'room')
                                        <span class="inline-flex items-center gap-2 px-3.5 py-2 border-2 border-black rounded-lg text-xs font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] bg-lime-200">
                                            {{ $customItem['name'] }}
                                            <button type="button" wire:click="removeCustomFacility({{ $index }})"
                                                class="w-5 h-5 rounded-md bg-rose-400 hover:bg-rose-500 text-black border-2 border-black flex items-center justify-center cursor-pointer shrink-0 transition-colors shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] active:shadow-none"
                                                title="Hapus fasilitas">
                                                <svg class="w-3 h-3 text-black stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Fasilitas Umum -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <label class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-black">
                                <x-icon name="lucide-building-2" class="w-4 h-4 stroke-[2.5]" />
                                Fasilitas Umum
                            </label>
                            <span
                                class="text-[10px] font-black uppercase bg-cyan-200 border-2 border-black px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                {{ $buildingFacilities->count() }} pilihan
                            </span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @forelse ($buildingFacilities as $facility)
                                <label class="cursor-pointer">
                                    <input type="checkbox" wire:model="selectedFacilities" value="{{ $facility->id }}"
                                        class="peer sr-only">
                                    <div
                                        class="px-4 py-3 rounded-lg border-2 border-black bg-zinc-50 text-black text-xs font-black flex items-center justify-between peer-checked:bg-cyan-300 peer-checked:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-yellow-100 transition-all">
                                        <span>{{ $facility->name }}</span>
                                        <span
                                            class="w-5 h-5 rounded border-2 border-black bg-white flex items-center justify-center text-black opacity-0 peer-checked:opacity-100 font-black text-xs">✓</span>
                                    </div>
                                </label>
                            @empty
                                <p class="text-zinc-500 font-bold text-sm col-span-full">Belum ada fasilitas umum.</p>
                            @endforelse
                        </div>

                        <div class="mt-2 flex gap-2">
                            <input type="text" wire:model="newBuildingFacility"
                                wire:keydown.enter.prevent="addFacility('building')"
                                placeholder="Fasilitas umum lainnya (kustom)..."
                                maxlength="50"
                                class="w-full bg-white border-2 border-black rounded-lg px-3 py-2 text-xs font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all">
                            <button type="button" wire:click="addFacility('building')"
                                class="px-4 py-2 bg-cyan-300 hover:bg-cyan-400 text-black border-2 border-black font-black text-[10px] uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer shrink-0">
                                + Tambah
                            </button>
                        </div>
                        @error('newBuildingFacility')
                            <p class="text-[10px] font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2 py-0.5 rounded-md inline-block">{{ $message }}</p>
                        @enderror

                        @if(collect($customFacilities)->where('type', 'building')->count() > 0)
                            <div class="flex flex-wrap gap-2.5 mt-2.5">
                                @foreach ($customFacilities as $index => $customItem)
                                    @if($customItem['type'] === 'building')
                                        <span class="inline-flex items-center gap-2 px-3.5 py-2 border-2 border-black rounded-lg text-xs font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] bg-cyan-200">
                                            {{ $customItem['name'] }}
                                            <button type="button" wire:click="removeCustomFacility({{ $index }})"
                                                class="w-5 h-5 rounded-md bg-rose-400 hover:bg-rose-500 text-black border-2 border-black flex items-center justify-center cursor-pointer shrink-0 transition-colors shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] active:shadow-none"
                                                title="Hapus fasilitas">
                                                <svg class="w-3 h-3 text-black stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Fasilitas Parkir -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <label class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-black">
                                <x-icon name="lucide-square-parking" class="w-4 h-4 stroke-[2.5]" />
                                Fasilitas Parkir
                            </label>
                            <span
                                class="text-[10px] font-black uppercase bg-yellow-200 border-2 border-black px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                {{ $parkingFacilities->count() }} pilihan
                            </span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @forelse ($parkingFacilities as $facility)
                                <label class="cursor-pointer">
                                    <input type="checkbox" wire:model="selectedFacilities" value="{{ $facility->id }}"
                                        class="peer sr-only">
                                    <div
                                        class="px-4 py-3 rounded-lg border-2 border-black bg-zinc-50 text-black text-xs font-black flex items-center justify-between peer-checked:bg-yellow-300 peer-checked:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-yellow-100 transition-all">
                                        <span>{{ $facility->name }}</span>
                                        <span
                                            class="w-5 h-5 rounded border-2 border-black bg-white flex items-center justify-center text-black opacity-0 peer-checked:opacity-100 font-black text-xs">✓</span>
                                    </div>
                                </label>
                            @empty
                                <p class="text-zinc-500 font-bold text-sm col-span-full">Belum ada fasilitas parkir.</p>
                            @endforelse
                        </div>
                    </div>

                    @error('selectedFacilities')
                        <p
                            class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                            {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Seksi 4: Aturan Kost -->
            <div
                class="bg-white rounded-xl p-6 md:p-8 border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <div class="flex items-center gap-3 border-b-3 border-black pb-4">
                    <div
                        class="w-10 h-10 rounded bg-black text-yellow-300 border-2 border-black font-black text-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        4
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Aturan Kost</h2>
                        <p class="text-xs font-bold text-zinc-600">Aturan yang berlaku agar calon penghuni tahu
                            sejak awal</p>
                    </div>
                </div>

                <!-- Checkbox Master Aturan -->
                <div class="space-y-3">
                    <label class="block text-xs font-black uppercase tracking-wider text-black">
                        Aturan yang Berlaku (Pilih yang sesuai)
                    </label>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach ($rules as $rule)
                            <label class="cursor-pointer">
                                <input type="checkbox" wire:model="selectedRules" value="{{ $rule->id }}"
                                    class="peer sr-only">
                                <div
                                    class="px-4 py-3 rounded-lg border-2 border-black bg-zinc-50 text-black text-xs font-black flex items-center justify-between peer-checked:bg-cyan-200 peer-checked:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-yellow-100 transition-all">
                                    <span>{{ $rule->name }}</span>
                                    <span
                                        class="w-5 h-5 rounded border-2 border-black bg-white flex items-center justify-center text-black opacity-0 peer-checked:opacity-100 font-black text-xs">✓</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedRules')
                        <p
                            class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                            {{ $message }}</p>
                    @enderror
                </div>

                <!-- Aturan Lainnya (Custom) -->
                <div class="space-y-3 pt-2 border-t-2 border-black">
                    <label class="block text-xs font-black uppercase tracking-wider text-black">
                        Aturan Lainnya (Kustom)
                    </label>

                    <div class="flex gap-2">
                        <input type="text" wire:model="newRule"
                            wire:keydown.enter.prevent="addRule"
                            placeholder="Ketik aturan baru jika tidak ada di atas..."
                            maxlength="50"
                            class="w-full bg-white border-2 border-black rounded-lg px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                        <button type="button" wire:click="addRule"
                            class="px-5 py-3 bg-cyan-300 hover:bg-cyan-200 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer shrink-0">
                            + Tambah
                        </button>
                    </div>

                    @error('newRule')
                        <p
                            class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">
                            {{ $message }}</p>
                    @enderror

                    @if (count($customRules) > 0)
                        <div class="flex flex-wrap gap-2.5 mt-2.5">
                            @foreach ($customRules as $index => $customName)
                                <span
                                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-cyan-200 border-2 border-black rounded-lg text-xs font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    {{ $customName }}
                                    <button type="button" wire:click="removeCustomRule({{ $index }})"
                                        class="w-5 h-5 rounded-md bg-rose-400 hover:bg-rose-500 text-black border-2 border-black flex items-center justify-center cursor-pointer shrink-0 transition-colors shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] active:shadow-none"
                                        title="Hapus aturan">
                                        <svg class="w-3 h-3 text-black stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Seksi 5: Sekitar Kost -->
            <div
                class="bg-white rounded-xl p-6 md:p-8 border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <div class="flex items-center gap-3 border-b-3 border-black pb-4">
                    <div
                        class="w-10 h-10 rounded bg-black text-yellow-300 border-2 border-black font-black text-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        5
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Sekitar Kost & Landmark</h2>
                        <p class="text-xs font-bold text-zinc-600">Informasi titik terdekat dan lokasi strategis lingkungan sekitar</p>
                    </div>
                </div>

                <!-- Landmark Terdekat (Multi-Item) -->
                <div class="space-y-2"
                    x-data="{
                        detecting: false,
                        detectMsg: '',
                        detectType: 'ok',
                        msgTimer: null,
                        lat: @entangle('latitude'),
                        lng: @entangle('longitude'),
                        showDetectMsg(msg, type = 'ok') {
                            this.detectMsg = msg;
                            this.detectType = type;
                            clearTimeout(this.msgTimer);
                            this.msgTimer = setTimeout(() => { this.detectMsg = ''; }, 6000);
                        },
                        haversine(lat1, lng1, lat2, lng2) {
                            const toRad = (d) => (d * Math.PI) / 180;
                            const R = 6371000;
                            const dLat = toRad(lat2 - lat1);
                            const dLng = toRad(lng2 - lng1);
                            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
                            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                        },
                        detect() {
                            this.detectMsg = '';
                            const lat = parseFloat(this.lat), lng = parseFloat(this.lng);
                            if (isNaN(lat) || isNaN(lng)) {
                                this.showDetectMsg('Letakkan pin lokasi di peta (Seksi 2) terlebih dahulu.', 'warn');
                                return;
                            }
                            if (!window.google || !window.google.maps || !window.google.maps.places) {
                                this.showDetectMsg('Deteksi otomatis membutuhkan Google Maps. Tambahkan landmark secara manual.', 'warn');
                                return;
                            }
                            this.detecting = true;
                            this.showDetectMsg('Sedang mendeteksi titik terdekat...', 'ok');
                            const allowed = new Set([
                                'grocery_or_supermarket', 'supermarket', 'convenience_store', 'department_store', 'shopping_mall', 'store',
                                'restaurant', 'meal_takeaway', 'meal_delivery', 'cafe', 'bakery', 'food', 'bar', 'night_club',
                                'mosque', 'church', 'place_of_worship', 'hindu_temple', 'synagogue',
                                'university', 'school', 'primary_school', 'secondary_school',
                                'hospital', 'pharmacy', 'doctor', 'dentist', 'health',
                                'gym', 'fitness_center', 'spa', 'beauty_salon',
                                'atm', 'bank',
                                'transit_station', 'bus_station', 'bus_stop', 'train_station', 'subway_station', 'light_rail_station', 'taxi_stand',
                                'toll_station', 'toll_booth',
                                'laundry', 'clothing_store', 'hardware_store'
                            ]);
                            const brandRe = /alfamidi|alfamart|indomaret|circle.?k|lawson|family.?mart/i;
                            const tollRe = /\btol\b|toll|gerbang.*tol/i;
                            const foodRe = /warung|warteg|nasi|makan|masakan|kedai|mie|soto|bakso|lalapan|seafood|ayam|padang|angkringan|depot|kantin|kafe|kopi|minuman|jus|buah/i;
                            const service = new google.maps.places.PlacesService(document.createElement('div'));
                            service.nearbySearch({
                                location: { lat: lat, lng: lng },
                                radius: 500,
                                type: 'establishment'
                            }, (results, status) => {
                                this.detecting = false;
                                if (status !== 'OK' || !results || results.length === 0) {
                                    this.showDetectMsg('Tidak ditemukan tempat apapun di sekitar pin lokasi. Pastikan pin telah diletakkan dengan benar.', 'warn');
                                    return;
                                }
                                const items = results
                                    .map((p) => ({ d: this.haversine(lat, lng, p.geometry.location.lat(), p.geometry.location.lng()), name: (p.name || '').trim(), types: p.types || [] }))
                                    .filter((r) => r.d <= 500)
                                    .filter((r) => r.types.some((t) => allowed.has(t)) || brandRe.test(r.name) || tollRe.test(r.name) || foodRe.test(r.name))
                                    .sort((a, b) => a.d - b.d)
                                    .slice(0, 8)
                                    .map((r) => Math.round(r.d) + 'm ' + r.name);
                                if (items.length === 0) {
                                    this.showDetectMsg('Tidak ada tempat strategis dalam 500m. Coba geser pin lokasi atau tambahkan landmark secara manual.', 'warn');
                                    return;
                                }
                                this.$wire.addLandmarks(items);
                            });
                        }
                    }"
                    @landmarks-added.window="showDetectMsg($event.detail.added > 0 ? $event.detail.added + ' titik terdekat berhasil ditambahkan!' : 'Semua titik terdekat sudah ditambahkan atau batas 12 tercapai.', $event.detail.added > 0 ? 'ok' : 'warn')">
                    <label class="block text-xs font-black uppercase tracking-wider text-black">
                        Titik Terdekat / Landmark (Opsional - Bisa Banyak)
                    </label>
                    <div class="flex gap-2">
                        <input type="text" wire:model="newLandmark"
                            wire:keydown.enter.prevent="addLandmark"
                            placeholder="Contoh: 300m dari UNPAD, 500m RS Borromeus, Dekat Tol..."
                            maxlength="80"
                            class="w-full bg-white border-2 border-black rounded-lg px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all">
                        <button type="button" wire:click="addLandmark"
                            class="px-5 py-3 bg-yellow-300 hover:bg-yellow-400 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer shrink-0">
                            + Tambah
                        </button>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <button type="button" @click="detect()" :disabled="detecting"
                            class="px-5 py-3 bg-emerald-300 hover:bg-emerald-400 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer shrink-0 disabled:opacity-60 disabled:cursor-wait inline-flex items-center gap-2">
                            <svg x-show="detecting" class="animate-spin w-3.5 h-3.5 text-black shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="detecting ? 'Mendeteksi...' : '📍 Deteksi Otomatis (500m)'"></span>
                        </button>
                        <p x-show="detectMsg !== ''" x-cloak x-text="detectMsg"
                            class="text-xs font-black px-2.5 py-1 rounded-md border-2"
                            :class="detectType === 'ok' ? 'bg-emerald-100 text-emerald-700 border-emerald-500' : 'bg-amber-100 text-amber-700 border-amber-500'"></p>
                    </div>
                    <p class="text-[11px] font-bold italic text-zinc-500">
                        Masukkan lokasi strategis terdekat satu per satu, atau klik "Deteksi Otomatis" untuk mengisi
                        titik terdekat dalam 500m (minimarket, rumah makan, tempat ibadah, kampus, rumah sakit, gym, ATM, transportasi, tol).
                    </p>
                    @error('newLandmark')
                        <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</p>
                    @enderror
                    @error('nearby_landmarks')
                        <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</p>
                    @enderror

                    @if (count($landmarkList) > 0)
                        <div class="flex flex-wrap gap-2.5 mt-2.5">
                            @foreach ($landmarkList as $index => $landmarkItem)
                                <span
                                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-yellow-200 border-2 border-black rounded-lg text-xs font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    📍 {{ $landmarkItem }}
                                    <button type="button" wire:click="removeLandmark({{ $index }})"
                                        class="w-5 h-5 rounded-md bg-rose-400 hover:bg-rose-500 text-black border-2 border-black flex items-center justify-center cursor-pointer shrink-0 transition-colors shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] active:shadow-none"
                                        title="Hapus landmark">
                                        <svg class="w-3 h-3 text-black stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Seksi 6: Foto Utama Properti -->
            <div
                class="bg-white rounded-xl p-6 md:p-8 border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <div class="flex items-center gap-3 border-b-3 border-black pb-4">
                    <div
                        class="w-10 h-10 rounded bg-black text-yellow-300 border-2 border-black font-black text-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        6
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Foto Utama Properti</h2>
                        <p class="text-xs font-bold text-zinc-600">Unggah foto fasad atau kamar terbaik properti kost
                            Anda. Klik "Jadikan Foto Utama" untuk memilih sampul.</p>
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
                                <x-icon name="lucide-image" class="w-7 h-7 stroke-[2]" />
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
                    @php
                        $photoTotal = $isEdit ? count($existingPhotos) + count($photos) : count($photos);
                    @endphp
                    <div x-show="isUploading || {{ $photoTotal > 0 ? 'true' : 'false' }}" x-cloak
                        class="bg-lime-100 border-3 border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all duration-200">
                        <!-- State 1: Upload Progress in Track -->
                        <div x-show="isUploading" class="space-y-2.5 font-black text-black">
                            <div class="flex items-center justify-between text-xs uppercase">
                                <span class="flex items-center gap-2">
                                    <x-icon name="lucide-loader-circle" class="animate-spin h-4 w-4 text-black" />
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
                        @if ($photoTotal > 0)
                            <div class="space-y-4">
                                <div class="flex items-center justify-between border-b-2 border-black pb-2">
                                    <div class="text-xs font-black text-black uppercase flex items-center gap-2">
                                        Preview Foto
                                        <span
                                            class="px-2 py-0.5 rounded border-2 border-black text-xs font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] {{ $photoTotal < 4 ? 'bg-rose-300 text-black' : ($photoTotal > 10 ? 'bg-rose-400 text-black' : 'bg-lime-300 text-black') }}">
                                            {{ $photoTotal }}/10
                                        </span>
                                        @if ($photoTotal < 4)
                                            <span
                                                class="text-[10px] font-black text-rose-600 bg-rose-100 border border-rose-400 px-1.5 py-0.5 rounded uppercase">Kurang
                                                {{ 4 - $photoTotal }} foto lagi</span>
                                        @elseif($photoTotal > 10)
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

                                @if ($isEdit && count($existingPhotos) > 0)
                                    <div class="space-y-2">
                                        <p class="text-[11px] font-black uppercase tracking-wider text-zinc-600">
                                            Foto Tersimpan</p>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                            @foreach ($existingPhotos as $img)
                                                <div
                                                    class="relative group aspect-[4/3] rounded-lg border-3 border-black overflow-hidden shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] bg-zinc-200 {{ $primaryPhotoId === $img['id'] ? 'ring-4 ring-yellow-400' : '' }}">
                                                    <img src="{{ $img['url'] }}"
                                                        alt="Foto Kost"
                                                        class="w-full h-full object-cover">

                                                    <!-- Remove Button -->
                                                    <button type="button"
                                                        wire:click="removeExistingPhoto({{ $img['id'] }})"
                                                        class="absolute top-2 right-2 w-7 h-7 bg-rose-400 hover:bg-rose-300 border-2 border-black rounded text-black font-black text-[10px] shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 cursor-pointer active:translate-x-0.5 active:translate-y-0.5 active:shadow-none z-20"
                                                        title="Hapus Foto">
                                                        &#x2715;
                                                    </button>

                                                    <!-- Primary / Set Primary -->
                                                    @if ($primaryPhotoId === $img['id'])
                                                        <div
                                                            class="absolute bottom-2 left-2 bg-yellow-400 text-black text-[9px] font-black uppercase px-2 py-0.5 border-2 border-black rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] pointer-events-none">
                                                            Foto Utama
                                                        </div>
                                                    @else
                                                        <button type="button"
                                                            wire:click="setExistingPrimary({{ $img['id'] }})"
                                                            class="absolute bottom-2 left-2 bg-white text-black text-[9px] font-black uppercase px-2 py-0.5 border-2 border-black rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] opacity-0 group-hover:opacity-100 transition-all cursor-pointer z-20">
                                                            Jadikan Utama
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if (count($photos) > 0)
                                    <div class="space-y-2">
                                        @if ($isEdit)
                                            <p class="text-[11px] font-black uppercase tracking-wider text-zinc-600">
                                                Foto Baru</p>
                                        @endif
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

                                                    <!-- Primary Badge / Set Primary -->
                                                    @if (!$isEdit && $index === 0)
                                                        <div
                                                            class="absolute bottom-2 left-2 bg-yellow-400 text-black text-[9px] font-black uppercase px-2 py-0.5 border-2 border-black rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] pointer-events-none">
                                                            Foto Utama
                                                        </div>
                                                    @elseif($isEdit && $primaryPhotoId === null && $index === 0)
                                                        <div
                                                            class="absolute bottom-2 left-2 bg-yellow-400 text-black text-[9px] font-black uppercase px-2 py-0.5 border-2 border-black rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] pointer-events-none">
                                                            Foto Utama
                                                        </div>
                                                    @else
                                                        <button type="button"
                                                            wire:click="setPrimaryPhoto({{ $index }})"
                                                            class="absolute bottom-2 left-2 bg-white text-black text-[9px] font-black uppercase px-2 py-0.5 border-2 border-black rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] opacity-0 group-hover:opacity-100 transition-all cursor-pointer z-20"
                                                            title="Jadikan Foto Utama">
                                                            Jadikan Utama
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

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
