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

/* Dark-styled Google Maps tile palette (applied to roadmap/street only;
   satellite stays natural). Exposed globally so the inline detail map
   component can reuse the same palette. */
const KOST_DARK_MAP_STYLES = [
    { elementType: 'geometry', stylers: [{ color: '#18181b' }] },
    { elementType: 'labels.icon', stylers: [{ visibility: 'off' }] },
    { elementType: 'labels.text.fill', stylers: [{ color: '#a1a1aa' }] },
    { elementType: 'labels.text.stroke', stylers: [{ color: '#09090b' }] },
    { featureType: 'administrative', elementType: 'geometry', stylers: [{ color: '#27272a' }] },
    { featureType: 'administrative.country', elementType: 'geometry.stroke', stylers: [{ color: '#3f3f46' }] },
    { featureType: 'landscape', elementType: 'geometry', stylers: [{ color: '#18181b' }] },
    { featureType: 'poi', elementType: 'geometry', stylers: [{ color: '#27272a' }] },
    { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#27272a' }] },
    { featureType: 'road', elementType: 'geometry.stroke', stylers: [{ color: '#09090b' }] },
    { featureType: 'road.highway', elementType: 'geometry', stylers: [{ color: '#3f3f46' }] },
    { featureType: 'road.highway', elementType: 'geometry.stroke', stylers: [{ color: '#09090b' }] },
    { featureType: 'transit', elementType: 'geometry', stylers: [{ color: '#27272a' }] },
    { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#0a0a0f' }] },
    { featureType: 'water', elementType: 'labels.text.fill', stylers: [{ color: '#3f3f46' }] },
];
window.KOST_DARK_MAP_STYLES = KOST_DARK_MAP_STYLES;

const CATALOG_DARK_TILE_URL = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
const CATALOG_LIGHT_TILE_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

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
        currentLayer: 'street', // 'street' or 'satellite'
        themeObserver: null,
        /** Local reactive copy of $wire.mapItems — kept in sync via $watch */
        mapItems: config.mapItems || [],

        isDarkMode() {
            return document.documentElement.classList.contains('dark');
        },

        /** Called from x-init — sets up watchers and eagerly loads map in background */
        init() {
            this.hasGoogleKey = this.$el.dataset.mapsKey || '';

            // EAGER SCRIPT LOAD: Load scripts in background immediately,
            // but DO NOT initialize the map yet if the container is hidden.
            setTimeout(() => {
                if (this.hasGoogleKey) {
                    this.loadGoogleMapsScriptOnly();
                } else {
                    this.loadLeafletScriptOnly();
                }
            }, 100);

            // Watch for viewMode switch to mount or resize the map.
            // We must mount the map ONLY when it's visible to avoid
            // AdvancedMarkerElement invisible bugs in display:none containers.
            this.$watch('viewMode', (newMode) => {
                if (newMode === 'map') {
                    this.$nextTick(() => {
                        if (!this.map) {
                            this.initCatalogMap();
                        } else {
                            this.resizeMap();
                            if (this.mapEngine === 'google') this.renderGoogleMarkers();
                            else if (this.mapEngine === 'leaflet') this.renderLeafletMarkers();
                        }
                    });
                    // Fallback to guarantee resize and marker positioning after the 500ms slide-up transition completes
                    setTimeout(() => {
                        if (this.viewMode === 'map') {
                            this.resizeMap();
                            if (this.mapEngine === 'google') this.renderGoogleMarkers();
                            else if (this.mapEngine === 'leaflet') this.renderLeafletMarkers();
                        }
                    }, 600);
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

            // Sync $wire.mapItems into local reactive mapItems and re-render markers
            this.$watch('$wire.mapItems', (val) => {
                this.mapItems = val || [];
                if (this.viewMode === 'map' && this.map) {
                    if (this.mapEngine === 'google' && window.google && window.google.maps) {
                        this.renderGoogleMarkers();
                    } else if (this.mapEngine === 'leaflet' && typeof L !== 'undefined') {
                        this.renderLeafletMarkers();
                    }
                }
            });

            // Resilience: if the network drops/reconnects, retry loading scripts
            window.addEventListener('online', () => {
                if (!this.map && this.hasGoogleKey && !window.google) {
                    this.loadGoogleMapsScriptOnly();
                }
            });

            // When Google Maps finishes loading, if we are already in map view, mount it!
            window.addEventListener('google-catalog-map-loaded', () => {
                if (this.viewMode === 'map') {
                    if (this.mapEngine === 'leaflet' || this.map) return;
                    if (!this.setupGoogleMap()) this.loadLeafletAndInit();
                }
            });

            // Handle window resize events (especially from the "Lihat Peta" button dispatching resize)
            window.addEventListener('resize', () => {
                if (this.viewMode === 'map') {
                    this.resizeMap();
                }
            });

            // React to theme changes (dark mode toggle) and re-skin the map in place
            this.themeObserver = new MutationObserver(() => {
                if (!this.map) return;
                if (this.mapEngine === 'google') {
                    this.applyGoogleDarkMode();
                } else if (this.mapEngine === 'leaflet') {
                    this.applyLeafletDarkMode();
                }
            });
            this.themeObserver.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class'],
            });
        },

        get items() {
            return this.mapItems || [];
        },

        switchLayer(layer) {
            this.currentLayer = layer;
            if (!this.map) return;

            if (this.mapEngine === 'google') {
                const mapTypeId = layer === 'satellite' ? 'hybrid' : 'roadmap';
                this.map.setMapTypeId(mapTypeId);
                this.applyGoogleDarkMode();
            } else if (this.mapEngine === 'leaflet') {
                if (this.currentTileLayer) this.map.removeLayer(this.currentTileLayer);
                let url;
                const opts = { maxZoom: 19 };
                if (layer === 'satellite') {
                    url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
                } else {
                    url = this.isDarkMode() ? CATALOG_DARK_TILE_URL : CATALOG_LIGHT_TILE_URL;
                    opts.subdomains = 'abcd';
                    opts.attribution = '© OpenStreetMap';
                }
                this.currentTileLayer = L.tileLayer(url, opts).addTo(this.map);
            }
        },

        /** Force resize on already-created map instance */
        resizeMap() {
            if (!this.map) return;
            if (this.mapEngine === 'google' && window.google) {
                google.maps.event.trigger(this.map, 'resize');
            } else if (this.mapEngine === 'leaflet' && typeof L !== 'undefined' && this.map.invalidateSize) {
                this.map.invalidateSize();
            }
        },

        /** Apply/remove the dark tile palette on an existing Google map */
        applyGoogleDarkMode() {
            if (this.mapEngine !== 'google' || !this.map) return;
            this.map.setOptions({
                styles: null,
            });
        },

        /** Swap the Leaflet street tiles between light and dark basemaps */
        applyLeafletDarkMode() {
            if (this.mapEngine !== 'leaflet' || !this.map) return;
            if (this.currentLayer !== 'street') return;
            if (this.currentTileLayer) this.map.removeLayer(this.currentTileLayer);
            const url = CATALOG_LIGHT_TILE_URL;
            this.currentTileLayer = L.tileLayer(url, {
                maxZoom: 19,
                subdomains: 'abcd',
                attribution: '© OpenStreetMap'
            }).addTo(this.map);
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

        /** Load the Google Maps script without mounting the map */
        loadGoogleMapsScriptOnly() {
            if (window.google && window.google.maps) return;
            
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
                    if (script.parentNode) script.parentNode.removeChild(script);
                    if (this.map || this.mapEngine === 'leaflet') return;
                    if (this.googleRetries < 3) {
                        this.googleRetries++;
                        setTimeout(() => this.loadGoogleMapsScriptOnly(), 2000 * this.googleRetries);
                    } else {
                        this.loadLeafletScriptOnly();
                    }
                };
                document.head.appendChild(script);
            }
        },

        /** Load the Google Maps script with retry-on-failure, then fall back to Leaflet */
        loadGoogleMaps() {
            if (window.google && window.google.maps) {
                this.setupGoogleMap();
                return;
            }
            if (this.mapEngine === 'leaflet' || this.map) return;

            this.loadGoogleMapsScriptOnly();
            // Fallback to Leaflet after 5s if Google Maps script hasn't loaded
            setTimeout(() => { if (!this.map) this.loadLeafletAndInit(); }, 5000);
        },

        loadLeafletScriptOnly() {
            if (typeof L !== 'undefined') return;
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
                script.onload = () => window.dispatchEvent(new CustomEvent('leaflet-catalog-map-loaded'));
                script.onerror = () => {
                    if (script.parentNode) script.parentNode.removeChild(script);
                    if (catalogLeafletRetries < CATALOG_LEAFLET_MAX_RETRIES) {
                        catalogLeafletRetries++;
                        setTimeout(() => this.loadLeafletScriptOnly(), 1000 * catalogLeafletRetries);
                    } else {
                        if (!this.map) window.dispatchEvent(new Event('map-load-error'));
                    }
                };
                document.head.appendChild(script);
            }
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
            
            this.loadLeafletScriptOnly();
            window.addEventListener('leaflet-catalog-map-loaded', () => {
                if (!this.map) this.setupLeafletMap();
            });
        },

        setupGoogleMap() {
            if (this.mapEngine === 'leaflet') return true; // don't clobber a working Leaflet map
            if (!this.$refs.catalogMapElement) {
                return false;
            }
            try {
                if (!this.map) {
                    const hasAdvancedMarker = Boolean(
                        window.google.maps.marker && window.google.maps.marker.AdvancedMarkerElement
                    );
                    const mapOptions = {
                        center: { lat: -6.917464, lng: 107.619123 },
                        zoom: 13,
                        mapTypeControl: false, // We use custom buttons for this
                        streetViewControl: false,
                        fullscreenControl: true,
                        styles: null,
                    };
                    if (hasAdvancedMarker) {
                        mapOptions.mapId = 'KOST_CATALOG_MAP';
                    }

                    this.map = new google.maps.Map(this.$refs.catalogMapElement, mapOptions);
                    this.infoWindow = new google.maps.InfoWindow();
                    this.mapEngine = 'google';
                    this.googleRetries = 0;

                    // Inject custom switcher into Google Maps controls so it stays visible in Fullscreen
                    if (this.$refs.mapTypeSwitcher) {
                        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(this.$refs.mapTypeSwitcher);
                    }
                }
                this.renderGoogleMarkers();

                return true;
            } catch (e) {
                console.warn('Google Map Catalog init error:', e);
                window.dispatchEvent(new Event('map-load-error'));
                return false;
            }
        },

        renderGoogleMarkers() {
            if (this.mapEngine !== 'google' || !this.map || !window.google) return;
            this.markers.forEach(m => {
                if (typeof m.setMap === 'function') m.setMap(null);
                else m.map = null;
            });
            this.markers = [];

            const currentItems = this.items;
            const activeDistrict = this.$wire ? this.$wire.district : '';
            const hasActiveDistrict = activeDistrict && this.districtBounds[activeDistrict];

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

            const hasAdvancedMarker = Boolean(
                window.google.maps.marker && window.google.maps.marker.AdvancedMarkerElement
            );

            currentItems.forEach(item => {
                const lat = parseFloat(item.lat);
                const lng = parseFloat(item.lng);
                if (isNaN(lat) || isNaN(lng) || lat === 0 || lng === 0) return;

                const pos = { lat, lng };
                bounds.extend(pos);
                validCount++;

                const bg = item.is_boosted ? '#FACC15' : '#FFFFFF';
                const priceText = item.price_short || 'Kost';

                if (hasAdvancedMarker) {
                    // Build Neo-Brutalist HTML DOM Badge for AdvancedMarkerElement
                    const priceTag = document.createElement('div');
                    priceTag.className = 'neo-map-pin-container';
                    priceTag.style.cursor = 'pointer';
                    priceTag.style.userSelect = 'none';
                    priceTag.innerHTML = `
                        <div style="
                            background: ${bg};
                            color: #000000;
                            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                            font-weight: 900;
                            font-size: 11px;
                            line-height: 1.2;
                            padding: 5px 9px;
                            border: 2px solid #000000;
                            border-radius: 7px;
                            box-shadow: 2px 2px 0px 0px rgba(0,0,0,1);
                            white-space: nowrap;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            position: relative;
                            transform: translateY(-8px);
                        ">
                            ${this.escapeHtml(priceText)}
                            <div style="
                                position: absolute;
                                bottom: -6px;
                                left: 50%;
                                transform: translateX(-50%);
                                width: 0;
                                height: 0;
                                border-left: 5px solid transparent;
                                border-right: 5px solid transparent;
                                border-top: 6px solid #000000;
                            "></div>
                        </div>
                    `;

                    const marker = new google.maps.marker.AdvancedMarkerElement({
                        position: pos,
                        map: this.map,
                        title: item.name,
                        content: priceTag
                    });

                    marker.addListener('click', () => {
                        this.infoWindow.setContent(this.buildPopupHtml(item));
                        this.infoWindow.open(this.map, marker);
                    });

                    this.markers.push(marker);
                } else {
                    // Classic google.maps.Marker with custom SVG badge fallback
                    const svg = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="76" height="34" viewBox="0 0 76 34">
                            <rect x="2" y="2" width="72" height="24" rx="6" fill="#000" />
                            <rect x="0" y="0" width="72" height="24" rx="6" fill="${bg}" stroke="#000" stroke-width="2" />
                            <text x="36" y="16" font-family="system-ui, sans-serif" font-weight="900" font-size="11" fill="#000" text-anchor="middle">${this.escapeHtml(priceText)}</text>
                            <polygon points="30,24 42,24 36,32" fill="${bg}" stroke="#000" stroke-width="2" />
                            <line x1="31" y1="24" x2="41" y2="24" stroke="${bg}" stroke-width="2.5" />
                        </svg>
                    `.trim().replace(/\s+/g, ' ');

                    const marker = new google.maps.Marker({
                        position: pos,
                        map: this.map,
                        title: item.name,
                        icon: {
                            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
                            size: new google.maps.Size(76, 34),
                            scaledSize: new google.maps.Size(76, 34),
                            anchor: new google.maps.Point(36, 34)
                        }
                    });

                    marker.addListener('click', () => {
                        this.infoWindow.setContent(this.buildPopupHtml(item));
                        this.infoWindow.open(this.map, marker);
                    });

                    this.markers.push(marker);
                }
            });

            if (validCount > 0) {
                this.map.fitBounds(bounds);
                if (validCount === 1) {
                    setTimeout(() => { this.map.setZoom(14); }, 100);
                }
            }
        },

        setupLeafletMap() {
            if (this.mapEngine === 'google') return; // don't clobber a working Google map
            if (!this.$refs.catalogMapElement || typeof L === 'undefined') return;
            if (!this.map) {
                this.map = L.map(this.$refs.catalogMapElement)
                    .setView([-6.917464, 107.619123], 13);
                // Use custom layer if needed
                if (this.currentLayer === 'satellite') {
                    this.switchLayer('satellite');
                } else {
                    const url = CATALOG_LIGHT_TILE_URL;
                    this.currentTileLayer = L.tileLayer(url, {
                        maxZoom: 19,
                        subdomains: 'abcd',
                        attribution: '© OpenStreetMap'
                    }).addTo(this.map);
                }
                this.mapEngine = 'leaflet';

                // Inject custom switcher into Leaflet container so it overlays correctly
                if (this.$refs.mapTypeSwitcher) {
                    this.$refs.mapTypeSwitcher.style.position = 'absolute';
                    this.$refs.mapTypeSwitcher.style.zIndex = '1000';
                    this.$refs.catalogMapElement.appendChild(this.$refs.mapTypeSwitcher);
                }
            }
            this.renderLeafletMarkers();

            // Force Leaflet to recalculate size once after DOM is fully painted
            setTimeout(() => {
                if (this.map && this.map.invalidateSize) {
                    this.map.invalidateSize();
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
            return `
                <style>
                    /* Reset Google Maps InfoWindow wrappers to remove default padding */
                    .gm-style-iw-c {
                        padding: 0 !important;
                        border-radius: 12px !important;
                        border: 3px solid #000 !important;
                        box-shadow: 6px 6px 0px #000 !important;
                        background: #fff !important;
                    }
                    .gm-style-iw-d {
                        overflow: hidden !important;
                        padding: 0 !important;
                    }
                    /* Hide default tail shadow from Google Maps */
                    .gm-style-iw-tc::after {
                        background: #000 !important;
                    }
                    
                    /* Hide yellow pan arrows (accessibility focus indicators) */
                    .gm-style-iw-c button[title^="Pan"],
                    .gm-style-iw-c button[aria-label^="Pan"],
                    .gm-style-iw-a button:not(.gm-ui-hover-effect) {
                        display: none !important;
                    }

                    /* Override Google Maps InfoWindow default close button */
                    .gm-ui-hover-effect {
                        top: 10px !important;
                        right: 10px !important;
                        background: #FEE2E2 !important;
                        border: 2px solid #000 !important;
                        border-radius: 50% !important;
                        box-shadow: 2px 2px 0px #000 !important;
                        opacity: 1 !important;
                        width: 28px !important;
                        height: 28px !important;
                        transition: all 0.15s ease !important;
                        z-index: 100 !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                    }
                    .gm-ui-hover-effect:hover {
                        top: 11px !important;
                        right: 9px !important;
                        box-shadow: 1px 1px 0px #000 !important;
                        background: #FECACA !important;
                    }
                    /* Hide Google's native sprite icon to prevent hover glitching */
                    .gm-ui-hover-effect > span,
                    .gm-ui-hover-effect > img {
                        display: none !important;
                    }
                    /* Draw our own perfect Neo-Brutalist 'X' */
                    .gm-ui-hover-effect::before,
                    .gm-ui-hover-effect::after {
                        content: '' !important;
                        position: absolute !important;
                        top: 50% !important;
                        left: 50% !important;
                        width: 12px !important;
                        height: 2.5px !important;
                        background-color: #000 !important;
                        border-radius: 2px !important;
                    }
                    .gm-ui-hover-effect::before {
                        transform: translate(-50%, -50%) rotate(45deg) !important;
                    }
                    .gm-ui-hover-effect::after {
                        transform: translate(-50%, -50%) rotate(-45deg) !important;
                    }

                    /* Custom Button */
                    .neo-popup-btn {
                        position: relative;
                        display: flex; align-items: center; justify-content: center; gap: 6px;
                        background: #FB923C; color: #000; border: 2.5px solid #000;
                        padding: 10px 12px; border-radius: 8px; font-weight: 900;
                        font-size: 13px; text-decoration: none; text-transform: uppercase;
                        box-shadow: 3px 3px 0px #000; transition: all 0.15s ease;
                        margin-top: 4px; box-sizing: border-box; width: 100%;
                        top: 0;
                        left: 0;
                    }
                    .neo-popup-btn:hover {
                        top: 2px;
                        left: 2px;
                        box-shadow: 1px 1px 0px #000;
                    }
                </style>
                <div style="font-family: 'Inter', system-ui, sans-serif; width: 250px; color: #000; box-sizing: border-box; display: flex; flex-direction: column;">
                    <div style="position: relative; width: 100%; height: 140px; border-bottom: 3px solid #000;">
                        <img src="${escape(item.image)}" 
                             style="width: 100%; height: 100%; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px;" />
                        <div style="position: absolute; bottom: 8px; left: 8px; display: flex; gap: 6px;">
                            <span style="background: #F472B6; color: #000; font-size: 10px; font-weight: 900; text-transform: uppercase; padding: 4px 8px; border: 2px solid #000; border-radius: 6px; box-shadow: 2px 2px 0px #000;">
                                ${escape(item.gender)}
                            </span>
                            <span style="background: #67E8F9; color: #000; font-size: 10px; font-weight: 900; text-transform: uppercase; padding: 4px 8px; border: 2px solid #000; border-radius: 6px; box-shadow: 2px 2px 0px #000;">
                                ${escape(item.district)}
                            </span>
                        </div>
                    </div>
                    
                    <div style="padding: 14px;">
                        <h4 style="font-size: 16px; font-weight: 900; margin: 0 0 2px 0; line-height: 1.2; text-transform: uppercase;">
                            ${escape(item.name)}
                        </h4>
                        <p style="font-size: 12px; font-weight: 700; color: #555; margin: 0 0 12px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${escape(item.address)}
                        </p>
                        
                        <div style="display: flex; align-items: baseline; gap: 4px; margin-bottom: 14px;">
                            <span style="font-weight: 900; font-size: 17px; color: #000;">${escape(item.price_full)}</span>
                            <span style="font-weight: 800; font-size: 11px; color: #666;">${escape(item.price_unit)}</span>
                        </div>

                        <a href="${escape(item.url)}" class="neo-popup-btn">
                            <span>Lihat Detail</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            `;
        },
    };
};
