/**
 * Catalog Map Alpine.js Component
 * Handles Google Maps / Leaflet rendering for the public kost search page.
 * Registered as window.catalogMap() so Alpine can call x-data="catalogMap()".
 *
 * KEY DESIGN: Map is LAZY-INITIALIZED — only when user first switches to map
 * mode and the container div is actually visible in the DOM. This prevents
 * "blank map" issues caused by initializing Google Maps inside a hidden div.
 */
let catalogLeafletRetries = 0;
const CATALOG_LEAFLET_MAX_RETRIES = 5;

window.catalogMap = function (config) {
    return {
        viewMode: 'list',
        hasGoogleKey: '',
        map: null,
        mapEngine: '',     // 'google' | 'leaflet' | '' — engine actually in use
        googleRetries: 0,  // attempts made to load the Google Maps script
        mapReady: false,   // true once the map library has been loaded
        markers: [],
        infoWindow: null,
        mapFailed: false,
        districtBounds: config.districtBounds || {},

        /** Called from x-init — sets up watchers and eagerly loads map in background */
        init() {
            this.hasGoogleKey = this.$el.dataset.mapsKey || '';

            // EAGER MAP INIT: Load scripts and initialize map in background immediately.
            // This prevents network delay when the user clicks 'Lihat Peta'.
            // Use setTimeout to yield to the main thread so page rendering isn't blocked.
            setTimeout(() => {
                this.initCatalogMap();
            }, 100);

            // Watch for viewMode switch to trigger a resize, because the map was
            // initialized inside a hidden (display: none) container.
            this.$watch('viewMode', (newMode) => {
                if (newMode === 'map') {
                    // Wait one tick for Alpine to un-hide the container via x-show
                    this.$nextTick(() => {
                        this.resizeMap();
                    });
                }
            });

            // Re-render markers whenever Livewire pushes updated mapItems
            window.addEventListener('map-items-updated', () => {
                if (this.viewMode === 'map' && this.map) {
                    if (this.mapEngine === 'google' && window.google && window.google.maps) {
                        this.renderGoogleMarkers();
                    } else if (this.mapEngine === 'leaflet' && typeof L !== 'undefined') {
                        this.renderLeafletMarkers();
                    }
                }
            });

            // Resilience: if the network drops/reconnects (ERR_NETWORK_CHANGED),
            // retry loading the Google Maps script — it may succeed on a fresh connection.
            window.addEventListener('online', () => {
                if (!this.map && this.hasGoogleKey && !window.google) {
                    this.initCatalogMap();
                }
            });

            // When Google Maps finishes loading, set up the map — but never clobber
            // a Leaflet map that may have already been initialized as a fallback.
            window.addEventListener('google-catalog-map-loaded', () => {
                if (this.mapEngine === 'leaflet' || this.map) return;
                if (!this.setupGoogleMap()) this.loadLeafletAndInit();
            });
        },

        get items() {
            return (this.$wire && this.$wire.mapItems) ? this.$wire.mapItems : [];
        },

        /** Force resize on already-created map instance */
        resizeMap() {
            if (!this.map) return;
            if (this.mapEngine === 'google' && window.google) {
                google.maps.event.trigger(this.map, 'resize');
                this.renderGoogleMarkers(); // re-fit bounds after resize
            } else if (this.mapEngine === 'leaflet' && typeof L !== 'undefined' && this.map.invalidateSize) {
                this.map.invalidateSize();
                this.renderLeafletMarkers();
            }
        },

        /** Load the appropriate map library then set up the map */
        initCatalogMap() {
            if (this.map) return; // already initialized, nothing to do
            if (this.hasGoogleKey) {
                if (window.google && window.google.maps) {
                    this.setupGoogleMap();
                } else if (this.mapEngine !== 'leaflet') {
                    this.loadGoogleMaps();
                }
            } else {
                this.loadLeafletAndInit();
            }
        },

        /** Load the Google Maps script with retry-on-failure, then fall back to Leaflet */
        loadGoogleMaps() {
            if (window.google && window.google.maps) {
                this.setupGoogleMap();
                return;
            }
            if (this.mapEngine === 'leaflet' || this.map) return;

            let script = document.getElementById('google-catalog-map-script');
            if (!script) {
                window.initGoogleCatalogMap = () =>
                    window.dispatchEvent(new CustomEvent('google-catalog-map-loaded'));
                script = document.createElement('script');
                script.id = 'google-catalog-map-script';
                script.src = 'https://maps.googleapis.com/maps/api/js?key=' +
                    this.hasGoogleKey + '&callback=initGoogleCatalogMap' +
                    '&loading=async&libraries=marker';
                script.async = true;
                script.onerror = () => {
                    // Remove the failed script so a later retry can re-add it
                    if (script.parentNode) script.parentNode.removeChild(script);
                    if (this.map || this.mapEngine === 'leaflet') return;
                    if (this.googleRetries < 3) {
                        this.googleRetries++;
                        // Exponential backoff: 2s, 4s, 6s
                        setTimeout(() => this.loadGoogleMaps(), 2000 * this.googleRetries);
                    } else {
                        this.loadLeafletAndInit();
                    }
                };
                document.head.appendChild(script);
            }
            // Fallback to Leaflet after 5s if Google Maps script hasn't loaded
            setTimeout(() => { if (!this.map) this.loadLeafletAndInit(); }, 5000);
        },

        loadLeafletAndInit() {
            if (typeof L !== 'undefined') {
                catalogLeafletRetries = 0;
                this.setupLeafletMap();
                return;
            }
            if (catalogLeafletRetries >= CATALOG_LEAFLET_MAX_RETRIES) {
                if (!this.map) window.dispatchEvent(new Event('map-load-error'));
                return;
            }
            if (!document.getElementById('leaflet-css')) {
                const link = document.createElement('link');
                link.id = 'leaflet-css';
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(link);
            }
            if (!document.getElementById('leaflet-js')) {
                const script = document.createElement('script');
                script.id = 'leaflet-js';
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.onload = () => {
                    catalogLeafletRetries = 0;
                    this.setupLeafletMap();
                };
                script.onerror = () => {
                    const el = document.getElementById('leaflet-js');
                    if (el) el.remove();
                    window.dispatchEvent(new Event('map-load-error'));
                };
                document.head.appendChild(script);
            } else {
                catalogLeafletRetries++;
                setTimeout(() => this.loadLeafletAndInit(), 200);
            }
        },

        setupGoogleMap() {
            if (this.mapEngine === 'leaflet') return true; // don't clobber a working Leaflet map
            if (!this.$refs.catalogMapElement) return false;
            if (!window.google || !window.google.maps) {
                window.dispatchEvent(new Event('map-load-error'));
                return false;
            }
            try {
                if (!this.map) {
                    this.map = new google.maps.Map(this.$refs.catalogMapElement, {
                        center: { lat: -6.917464, lng: 107.619123 },
                        zoom: 13,
                        mapId: 'DEMO_MAP_ID',
                        mapTypeControl: false,
                        streetViewControl: false,
                        fullscreenControl: false,
                    });
                    this.infoWindow = new google.maps.InfoWindow();
                    this.mapEngine = 'google';
                    this.googleRetries = 0;
                }
                this.renderGoogleMarkers();
                
                // Critical Resilience: Force Google Maps to recalculate size
                setTimeout(() => {
                    if (this.map && window.google) {
                        google.maps.event.trigger(this.map, 'resize');
                        this.renderGoogleMarkers(); // re-fit bounds after resize
                    }
                }, 500);
                
                return true;
            } catch (e) {
                console.warn('Google Map Catalog init error:', e);
                window.dispatchEvent(new Event('map-load-error'));
                return false;
            }
        },

        renderGoogleMarkers() {
            if (this.mapEngine !== 'google' || !this.map || !window.google) return;
            this.markers.forEach(m => m.setMap(null));
            this.markers = [];

            const currentItems = this.items;
            const activeDistrict = this.$wire ? this.$wire.district : '';
            const hasActiveDistrict = activeDistrict && this.districtBounds[activeDistrict];

            // Case B: 0 Listings but has active district
            if ((!currentItems || currentItems.length === 0) && hasActiveDistrict) {
                const dist = this.districtBounds[activeDistrict];
                this.map.setCenter({ lat: dist.center.lat, lng: dist.center.lng });
                this.map.setZoom(dist.zoom || 14);
                return;
            }

            if (!currentItems || currentItems.length === 0) {
                this.map.setCenter({ lat: -6.917464, lng: 107.619123 });
                this.map.setZoom(13);
                return;
            }

            const bounds = new google.maps.LatLngBounds();
            let validCount = 0;

            const AdvancedMarker =
                window.google.maps.marker && window.google.maps.marker.AdvancedMarkerElement;
            if (!AdvancedMarker) {
                console.warn('Google Maps marker library unavailable; skipping markers.');
                return;
            }

            currentItems.forEach(item => {
                if (!item.lat || !item.lng) return;
                const pos = { lat: item.lat, lng: item.lng };
                bounds.extend(pos);
                validCount++;

                // Custom DOM marker: neo-brutalist price tag with a downward tail
                // pointing at the exact location (AdvancedMarkerElement does not
                // support the legacy `label`/`icon` options).
                const bg = item.is_boosted ? '#FACC15' : '#FFFFFF';
                const labelEl = document.createElement('div');
                labelEl.textContent = item.price_short;
                labelEl.style.cssText =
                    'background:' + bg + ';border:2px solid #000;border-radius:4px;' +
                    'padding:2px 8px;font-weight:900;font-size:11px;color:#000;' +
                    'white-space:nowrap;box-shadow:2px 2px 0 #000;' +
                    'font-family:system-ui,sans-serif;';

                const tailEl = document.createElement('div');
                tailEl.style.cssText =
                    'width:0;height:0;border-left:6px solid transparent;' +
                    'border-right:6px solid transparent;border-top:8px solid #000;';

                const contentEl = document.createElement('div');
                contentEl.style.cssText =
                    'display:flex;flex-direction:column;align-items:center;';
                contentEl.appendChild(labelEl);
                contentEl.appendChild(tailEl);

                const marker = new AdvancedMarker({
                    position: pos,
                    map: this.map,
                    title: item.name,
                    content: contentEl,
                    collisionBehavior:
                        google.maps.CollisionBehavior?.REQUIRED ?? 'REQUIRED',
                });

                marker.addListener('click', () => {
                    this.infoWindow.setContent(this.buildPopupHtml(item));
                    this.infoWindow.open(this.map, marker);
                });

                this.markers.push(marker);
            });

            // Case A: Listings exist in district, use fitBounds of markers
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
            if (this.mapEngine === 'google') return; // don't clobber a working Google map
            if (!this.$refs.catalogMapElement || typeof L === 'undefined') return;
            if (!this.map) {
                this.map = L.map(this.$refs.catalogMapElement)
                    .setView([-6.917464, 107.619123], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap',
                }).addTo(this.map);
                this.mapEngine = 'leaflet';
            }
            this.renderLeafletMarkers();
            
            // Critical Resilience: Force Leaflet to recalculate size after DOM is fully painted
            setTimeout(() => {
                if (this.map && this.map.invalidateSize) {
                    this.map.invalidateSize();
                    if (this.markers && this.markers.length > 0) {
                        const group = new L.featureGroup(this.markers);
                        this.map.fitBounds(group.getBounds());
                    }
                }
            }, 500);
        },

        renderLeafletMarkers() {
            if (this.mapEngine !== 'leaflet' || !this.map || typeof L === 'undefined') return;
            this.markers.forEach(m => this.map.removeLayer(m));
            this.markers = [];

            const currentItems = this.items;
            const activeDistrict = this.$wire ? this.$wire.district : '';
            const hasActiveDistrict = activeDistrict && this.districtBounds[activeDistrict];

            // Case B: 0 Listings but has active district
            if ((!currentItems || currentItems.length === 0) && hasActiveDistrict) {
                const dist = this.districtBounds[activeDistrict];
                this.map.setView([dist.center.lat, dist.center.lng], dist.zoom || 14);
                return;
            }

            if (!currentItems || currentItems.length === 0) return;

            const boundsArr = [];
            let validCount = 0;

            currentItems.forEach(item => {
                if (!item.lat || !item.lng) return;
                validCount++;

                const bg = item.is_boosted ? '#FDE047' : '#fff';
                const iconHtml =
                    '<div style="padding:2px 6px;border:2px solid #000;font-weight:900;' +
                    'font-size:11px;border-radius:4px;box-shadow:2px 2px 0 #000;' +
                    'background:' + bg + ';color:#000;white-space:nowrap">' +
                    item.price_short + '</div>';

                const customIcon = L.divIcon({
                    html: iconHtml,
                    className: '',
                    iconSize: [54, 26],
                    iconAnchor: [27, 13],
                });

                const marker = L.marker([item.lat, item.lng], { icon: customIcon })
                    .addTo(this.map)
                    .bindPopup(this.buildPopupHtml(item));

                boundsArr.push([item.lat, item.lng]);
                this.markers.push(marker);
            });

            // Case A: Listings exist, fitBounds to them
            if (validCount > 0 && boundsArr.length > 0) {
                this.map.fitBounds(boundsArr);
            }
        },

        /** Escape HTML entities to prevent stored-XSS in map popups */
        escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        },

        /** Build the popup/infoWindow HTML string for a single kost item */
        buildPopupHtml(item) {
            const escape = (v) => this.escapeHtml(v);
            return (
                '<div style="font-family:sans-serif;width:210px;padding:4px">' +
                '<img src="' + escape(item.image) + '" ' +
                    'style="width:100%;height:96px;object-fit:cover;border-radius:6px;' +
                    'border:2px solid #000;margin-bottom:6px" />' +
                '<div style="display:flex;gap:4px;margin-bottom:4px">' +
                    '<span style="background:#F472B6;color:#000;font-size:9px;font-weight:900;' +
                        'text-transform:uppercase;padding:2px 6px;border:1.5px solid #000;border-radius:4px">' +
                        escape(item.gender) + '</span>' +
                    '<span style="background:#67E8F9;color:#000;font-size:9px;font-weight:900;' +
                        'text-transform:uppercase;padding:2px 6px;border:1.5px solid #000;border-radius:4px">' +
                        escape(item.district) + '</span>' +
                '</div>' +
                '<h4 style="font-size:13px;font-weight:900;color:#000;text-transform:uppercase;' +
                    'margin:4px 0 2px;line-height:1.2">' + escape(item.name) + '</h4>' +
                '<p style="font-size:10px;font-weight:700;color:#555;margin-bottom:6px;' +
                    'white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + escape(item.address) + '</p>' +
                '<div style="background:#FACC15;border:1.5px solid #000;padding:4px 8px;border-radius:6px;' +
                    'font-weight:900;font-size:12px;margin-bottom:8px;box-shadow:2px 2px 0 #000">' +
                    escape(item.price_full) +
                    '<span style="font-size:9px">' + escape(item.price_unit) + '</span>' +
                '</div>' +
                '<a href="' + escape(item.url) + '" ' +
                    'style="display:flex;align-items:center;justify-content:center;gap:4px;background:#FB923C;color:#000;' +
                    'border:1.5px solid #000;padding:6px;border-radius:6px;font-weight:900;' +
                    'font-size:11px;text-decoration:none;text-transform:uppercase;box-shadow:2px 2px 0 #000">' +
                    '<span>Lihat Detail</span>' +
                    '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>' +
                '</a>' +
                '</div>'
            );
        },
    };
};
