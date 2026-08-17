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

        createPriceBadgeElement(priceText, isBoosted) {
            const bg = isBoosted ? '#FACC15' : '#FFFFFF';
            const textStr = String(priceText || 'Kost');
            // Dynamic width based on text length, min 56px
            const paddingH = 20;
            const charWidth = 8;
            const boxW = Math.max(56, textStr.length * charWidth + paddingH);
            const boxH = 26;
            const tailW = 10;
            const tailH = 8;
            const shadowOffset = 3;
            const totalW = boxW + shadowOffset;
            const totalH = boxH + tailH + shadowOffset;
            const cx = boxW / 2; // center x of badge

            // Shadow rect (offset bottom-right)
            const shadowRect = `<rect x="${shadowOffset}" y="${shadowOffset}" width="${boxW}" height="${boxH}" rx="6" fill="#000000" />`;
            // Main rect
            const mainRect = `<rect x="0" y="0" width="${boxW}" height="${boxH}" rx="6" fill="${bg}" stroke="#000000" stroke-width="2" />`;
            // Text centered in main rect
            const text = `<text x="${boxW / 2}" y="${boxH / 2 + 4}" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-weight="900" font-size="11" fill="#000000" text-anchor="middle">${this.escapeHtml(textStr)}</text>`;
            // Shadow tail triangle (offset)
            const tailShadow = `<polygon points="${cx - tailW / 2 + shadowOffset},${boxH + shadowOffset} ${cx + tailW / 2 + shadowOffset},${boxH + shadowOffset} ${cx + shadowOffset},${boxH + tailH + shadowOffset}" fill="#000000" />`;
            // Main tail triangle
            const tailMain = `<polygon points="${cx - tailW / 2},${boxH} ${cx + tailW / 2},${boxH} ${cx},${boxH + tailH}" fill="${bg}" stroke="#000000" stroke-width="2" stroke-linejoin="round" />`;
            // Cover the top edge of the tail so it blends with the box border
            const tailCover = `<rect x="${cx - tailW / 2 + 2}" y="${boxH - 1}" width="${tailW - 4}" height="3" fill="${bg}" />`;

            const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${totalW}" height="${totalH}" viewBox="0 0 ${totalW} ${totalH}">${tailShadow}${shadowRect}${tailMain}${mainRect}${tailCover}${text}</svg>`;

            const img = document.createElement('img');
            img.src = 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg);
            img.width = totalW;
            img.height = totalH;
            img.style.cursor = 'pointer';

            // Anchor at tip of tail
            return { img, svg, width: totalW, height: totalH, anchorX: Math.round(cx), anchorY: totalH - shadowOffset };
        },

        setupGoogleMap() {
            if (this.mapEngine === 'leaflet') return true; // don't clobber a working Leaflet map
            if (!this.$refs.catalogMapElement) {
                return false;
            }
            try {
                if (!this.map) {
                    this.map = new google.maps.Map(this.$refs.catalogMapElement, {
                        center: { lat: -6.917464, lng: 107.619123 },
                        zoom: 13,
                        mapTypeControl: false,
                        streetViewControl: false,
                        fullscreenControl: true,
                    });
                    this.infoWindow = new google.maps.InfoWindow();
                    this.mapEngine = 'google';
                    this.googleRetries = 0;

                    // Inject custom switcher into Google Maps controls so it stays visible in Fullscreen
                    if (this.$refs.mapTypeSwitcher) {
                        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(this.$refs.mapTypeSwitcher);
                    }

                    // Render markers once tiles & viewport are initialized
                    google.maps.event.addListenerOnce(this.map, 'idle', () => {
                        this.renderGoogleMarkers();
                    });
                }
                this.renderGoogleMarkers();

                return true;
            } catch (e) {
                console.warn('Google Map Catalog init error:', e);
                this.loadLeafletAndInit();
                return false;
            }
        },

        renderGoogleMarkers() {
            if (this.mapEngine !== 'google' || !this.map || !window.google) return;
            this.markers.forEach(m => {
                if (typeof m.setMap === 'function') m.setMap(null);
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

            currentItems.forEach(item => {
                const lat = parseFloat(item.lat);
                const lng = parseFloat(item.lng);
                if (isNaN(lat) || isNaN(lng) || lat === 0 || lng === 0) return;

                validCount++;
                bounds.extend({ lat, lng });

                const badge = this.createPriceBadgeElement(item.price_short, item.is_boosted);

                // Use classic google.maps.Marker (no Map ID required)
                const marker = new google.maps.Marker({
                    position: { lat, lng },
                    map: this.map,
                    title: item.name,
                    icon: {
                        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(badge.svg),
                        size: new google.maps.Size(badge.width, badge.height),
                        scaledSize: new google.maps.Size(badge.width, badge.height),
                        anchor: new google.maps.Point(badge.anchorX, badge.anchorY)
                    }
                });

                marker.addListener('click', () => {
                    this.infoWindow.setContent(this.buildPopupHtml(item));
                    this.infoWindow.open(this.map, marker);
                });

                this.markers.push(marker);
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
            const genderColor = {
                putra: '#BFDBFE',
                putri: '#FBCFE8',
                campur: '#BBF7D0'
            }[String(item.gender || '').toLowerCase()] || '#E5E7EB';
            return `
                <style>
                    .gm-style-iw-c {
                        padding: 0 !important;
                        border-radius: 14px !important;
                        border: 2.5px solid #000 !important;
                        box-shadow: 5px 5px 0px #000 !important;
                        background: #fff !important;
                        overflow: hidden !important;
                        max-width: 260px !important;
                    }
                    .gm-style-iw-d {
                        overflow: hidden !important;
                        padding: 0 !important;
                    }
                    .gm-style-iw-tc::after { background: #000 !important; }
                    .gm-style-iw-c button[title^="Pan"],
                    .gm-style-iw-c button[aria-label^="Pan"],
                    .gm-style-iw-a button:not(.gm-ui-hover-effect) { display: none !important; }

                    .gm-ui-hover-effect {
                        top: 8px !important; right: 8px !important;
                        background: #fff !important;
                        border: 2px solid #000 !important;
                        border-radius: 50% !important;
                        box-shadow: 2px 2px 0px #000 !important;
                        opacity: 1 !important;
                        width: 26px !important; height: 26px !important;
                        transition: all 0.12s ease !important;
                        z-index: 100 !important;
                        display: flex !important;
                        align-items: center !important; justify-content: center !important;
                    }
                    .gm-ui-hover-effect:hover {
                        transform: translate(2px, 2px) !important;
                        box-shadow: 0px 0px 0px #000 !important;
                    }
                    .gm-ui-hover-effect > span, .gm-ui-hover-effect > img { display: none !important; }
                    .gm-ui-hover-effect::before, .gm-ui-hover-effect::after {
                        content: '' !important; position: absolute !important;
                        top: 50% !important; left: 50% !important;
                        width: 11px !important; height: 2px !important;
                        background-color: #000 !important; border-radius: 1px !important;
                    }
                    .gm-ui-hover-effect::before { transform: translate(-50%, -50%) rotate(45deg) !important; }
                    .gm-ui-hover-effect::after  { transform: translate(-50%, -50%) rotate(-45deg) !important; }

                    .kp-btn {
                        position: relative; display: flex; align-items: center;
                        justify-content: center; gap: 5px;
                        background: #FACC15; color: #000; border: 2px solid #000;
                        padding: 9px 12px; border-radius: 8px; font-weight: 900;
                        font-size: 12px; text-decoration: none; text-transform: uppercase;
                        box-shadow: 3px 3px 0px #000; transition: all 0.12s ease;
                        box-sizing: border-box; width: 100%; letter-spacing: 0.3px;
                    }
                    .kp-btn:hover { transform: translate(2px,2px); box-shadow: 1px 1px 0px #000; }
                </style>
                <div style="font-family: 'Inter', system-ui, sans-serif; width: 248px; color: #000; box-sizing: border-box;">
                    <!-- Image -->
                    <div style="position: relative; width: 100%; height: 136px; overflow: hidden; border-bottom: 2.5px solid #000;">
                        <img src="${escape(item.image)}"
                             style="width: 100%; height: 100%; object-fit: cover; display: block;" />
                        <!-- Gender + District badges -->
                        <div style="position: absolute; bottom: 8px; left: 8px; display: flex; gap: 5px;">
                            <span style="background: ${genderColor}; color: #000; font-size: 9px; font-weight: 900; text-transform: uppercase; padding: 3px 8px; border: 2px solid #000; border-radius: 4px; box-shadow: 2px 2px 0 #000; letter-spacing: 0.3px;">
                                ${escape(item.gender)}
                            </span>
                            <span style="background: #FEF3C7; color: #000; font-size: 9px; font-weight: 900; text-transform: uppercase; padding: 3px 8px; border: 2px solid #000; border-radius: 4px; box-shadow: 2px 2px 0 #000; letter-spacing: 0.3px;">
                                ${escape(item.district)}
                            </span>
                        </div>
                    </div>

                    <!-- Info body -->
                    <div style="padding: 12px 12px 10px;">
                        <h4 style="font-size: 14px; font-weight: 900; margin: 0 0 3px 0; line-height: 1.25; text-transform: uppercase; letter-spacing: 0.2px; padding-right: 24px;">
                            ${escape(item.name)}
                        </h4>
                        <p style="font-size: 11px; color: #6B7280; margin: 0 0 10px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500;">
                            ${escape(item.address)}
                        </p>

                        <!-- Price section -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; background: #F9FAFB; border: 2px solid #000; border-radius: 8px; padding: 8px 10px;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                                <span style="font-weight: 900; font-size: 15px; color: #000; letter-spacing: -0.2px;">${escape(item.price_full)}</span>
                            </div>
                            <span style="font-size: 11px; color: #4B5563; font-weight: 700;">${escape(item.price_unit)}</span>
                        </div>

                        <a href="${escape(item.url)}" class="kp-btn">
                            <span>Lihat Detail</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            `;
        },
    };
};
