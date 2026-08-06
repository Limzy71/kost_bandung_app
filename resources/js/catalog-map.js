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
        currentLayer: 'street', // 'street' or 'satellite'

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
                        }
                    });
                    // Fallback to guarantee resize fires after the 500ms slide-up transition completes
                    setTimeout(() => {
                        if (this.viewMode === 'map') this.resizeMap();
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
        },

        get items() {
            return (this.$wire && this.$wire.mapItems) ? this.$wire.mapItems : [];
        },

        switchLayer(layer) {
            this.currentLayer = layer;
            if (!this.map) return;

            if (this.mapEngine === 'google') {
                const mapTypeId = layer === 'satellite' ? 'hybrid' : 'roadmap';
                this.map.setMapTypeId(mapTypeId);
            } else if (this.mapEngine === 'leaflet') {
                if (this.currentTileLayer) this.map.removeLayer(this.currentTileLayer);
                const url = layer === 'satellite'
                    ? 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
                    : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
                this.currentTileLayer = L.tileLayer(url, { maxZoom: 19 }).addTo(this.map);
            }
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
            if (!this.$refs.catalogMapElement) return false;
            if (!window.google || !window.google.maps) {
                window.dispatchEvent(new Event('map-load-error'));
                return false;
            }
            if (!window.google.maps.Marker) {
                console.warn('Google Maps Marker API unavailable; falling back to Leaflet.');
                this.loadLeafletAndInit();
                return false;
            }
            try {
                if (!this.map) {
                    this.map = new google.maps.Map(this.$refs.catalogMapElement, {
                        center: { lat: -6.917464, lng: 107.619123 },
                        zoom: 13,
                        mapTypeControl: false, // We use custom buttons for this
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
                }
                this.renderGoogleMarkers();
                
                // Critical Resilience: Force Google Maps to recalculate size after panel transition
                setTimeout(() => {
                    if (this.map && window.google) {
                        google.maps.event.trigger(this.map, 'resize');
                        this.renderGoogleMarkers(); // re-fit bounds after resize
                    }
                }, 700);
                
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

            const Marker = window.google.maps.Marker;
            if (!Marker) return;

            currentItems.forEach(item => {
                if (!item.lat || !item.lng) return;
                const pos = { lat: item.lat, lng: item.lng };
                bounds.extend(pos);
                validCount++;

                const bg = item.is_boosted ? '#FACC15' : '#FFFFFF';
                // Create SVG for Neo-Brutalist Price Tag
                const svgWidth = 80;
                const svgHeight = 36;
                const svg = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="${svgWidth}" height="${svgHeight}" viewBox="0 0 ${svgWidth} ${svgHeight}">
                        <!-- Shadow -->
                        <rect x="2" y="2" width="76" height="24" rx="4" fill="#000" />
                        <!-- Box -->
                        <rect x="0" y="0" width="76" height="24" rx="4" fill="${bg}" stroke="#000" stroke-width="2" />
                        <!-- Text -->
                        <text x="38" y="16" font-family="system-ui, sans-serif" font-weight="900" font-size="11" fill="#000" text-anchor="middle">${item.price_short}</text>
                        <!-- Tail -->
                        <polygon points="32,24 44,24 38,32" fill="${bg}" stroke="#000" stroke-width="2" />
                        <!-- Hide top border of tail -->
                        <line x1="33.5" y1="24" x2="42.5" y2="24" stroke="${bg}" stroke-width="2.5" />
                    </svg>
                `.trim().replace(/\s+/g, ' ');

                const iconUrl = 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg);

                const marker = new Marker({
                    position: pos,
                    map: this.map,
                    title: item.name,
                    icon: {
                        url: iconUrl,
                        anchor: new google.maps.Point(38, 32),
                    },
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
                    const url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
                    this.currentTileLayer = L.tileLayer(url, {
                        maxZoom: 19,
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

            // Critical Resilience: Force Leaflet to recalculate size after panel transition
            setTimeout(() => {
                if (this.map && typeof L !== 'undefined' && this.map.invalidateSize) {
                    this.map.invalidateSize();
                    this.renderLeafletMarkers(); // re-fit bounds after resize
                }
            }, 700);
            
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
                        width: 26px !important;
                        height: 26px !important;
                        transition: all 0.15s ease !important;
                        z-index: 100 !important;
                    }
                    .gm-ui-hover-effect:hover {
                        transform: translate(1px, 1px) !important;
                        box-shadow: 1px 1px 0px #000 !important;
                        background: #FECACA !important;
                    }
                    .gm-ui-hover-effect span {
                        margin: 2px !important;
                        width: 18px !important;
                        height: 18px !important;
                    }

                    /* Custom Button */
                    .neo-popup-btn {
                        display: flex; align-items: center; justify-content: center; gap: 6px;
                        background: #FB923C; color: #000; border: 2.5px solid #000;
                        padding: 10px 12px; border-radius: 8px; font-weight: 900;
                        font-size: 13px; text-decoration: none; text-transform: uppercase;
                        box-shadow: 3px 3px 0px #000; transition: all 0.15s ease;
                        margin-top: 4px; box-sizing: border-box; width: 100%;
                    }
                    .neo-popup-btn:hover {
                        transform: translate(2px, 2px);
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
