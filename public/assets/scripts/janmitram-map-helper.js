/**
 * Janmitram Interactive OpenStreetMap (Leaflet) Helper
 * Provides Address Search (Geocoding), GPS Current Location, Draggable Markers, and Tile Fallbacks.
 */

window.initJanmitramMap = function(config) {
    var containerId = config.containerId || 'map';
    var mapEl = document.getElementById(containerId);
    if (!mapEl) return null;

    var defaultLat = parseFloat(config.lat) || 28.6139; // India Default (Delhi)
    var defaultLng = parseFloat(config.lng) || 77.2090;
    var zoom = config.zoom || 13;
    var isDraggable = config.draggable !== false;

    // Check if coordinates were invalid (0, 0 or NaN)
    if (isNaN(defaultLat) || isNaN(defaultLng) || (defaultLat === 0 && defaultLng === 0)) {
        defaultLat = 28.6139;
        defaultLng = 77.2090;
    }

    // Initialize Map
    var map = L.map(containerId).setView([defaultLat, defaultLng], zoom);

    // Primary OpenStreetMap Tile Layer
    var primaryTiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        subdomains: ['a', 'b', 'c'],
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });

    // Fallback CartoDB Voyager Layer if OSM tiles fail
    var fallbackTiles = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        subdomains: 'abcd',
        attribution: '&copy; CartoDB &copy; OpenStreetMap'
    });

    primaryTiles.addTo(map);

    var tileErrors = 0;
    primaryTiles.on('tileerror', function() {
        tileErrors++;
        if (tileErrors >= 3 && !map.hasLayer(fallbackTiles)) {
            map.removeLayer(primaryTiles);
            fallbackTiles.addTo(map);
        }
    });

    // Custom Icon if provided
    var customIcon = null;
    if (config.iconUrl) {
        customIcon = L.icon({
            iconUrl: config.iconUrl,
            iconSize: config.iconSize || [35, 35],
            iconAnchor: config.iconAnchor || [17, 35],
            popupAnchor: config.popupAnchor || [0, -30]
        });
    }

    var markerOptions = { draggable: isDraggable };
    if (customIcon) markerOptions.icon = customIcon;

    var marker = L.marker([defaultLat, defaultLng], markerOptions).addTo(map);

    if (config.popupText) {
        marker.bindPopup(config.popupText);
    }

    // Helper to update Lat/Lng input elements
    function updateInputs(lat, lng) {
        var latInput = document.getElementById(config.latInputId || 'latitude');
        var lngInput = document.getElementById(config.lngInputId || 'longitude');
        if (latInput) latInput.value = parseFloat(lat).toFixed(7);
        if (lngInput) lngInput.value = parseFloat(lng).toFixed(7);
        if (typeof config.onLocationChange === 'function') {
            config.onLocationChange(lat, lng);
        }
    }

    updateInputs(defaultLat, defaultLng);

    if (isDraggable) {
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });

        marker.on('dragend', function(e) {
            var pos = marker.getLatLng();
            updateInputs(pos.lat, pos.lng);
        });
    }

    // Inject Control UI Bar (Search Box + GPS Button) if enabled
    if (config.showControls !== false) {
        var controlDiv = document.createElement('div');
        controlDiv.className = 'janmitram-map-controls p-2 bg-white rounded-3 shadow-sm border mb-2 d-flex flex-wrap gap-2 align-items-center';
        controlDiv.style.cssText = 'position: relative; z-index: 1000; font-family: sans-serif; font-size: 13px;';

        controlDiv.innerHTML =
            '<div class="flex-grow-1 position-relative" style="min-width: 220px;">' +
                '<div class="input-group input-group-sm">' +
                    '<span class="input-group-text bg-light"><i class="fas fa-search"></i></span>' +
                    '<input type="text" id="' + containerId + '-search-input" class="form-control" placeholder="Search location (e.g. Raipur, Delhi)...">' +
                    '<button type="button" id="' + containerId + '-search-btn" class="btn btn-primary px-3 fw-bold">Search</button>' +
                '</div>' +
                '<div id="' + containerId + '-search-results" class="list-group position-absolute w-100 shadow-lg d-none" style="z-index: 2000; max-height: 200px; overflow-y: auto; top: 38px;"></div>' +
            '</div>' +
            '<button type="button" id="' + containerId + '-gps-btn" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1 shadow-sm fw-semibold">' +
                '<i class="fas fa-crosshairs"></i> <span>Use My Location</span>' +
            '</button>';

        mapEl.parentNode.insertBefore(controlDiv, mapEl);

        // Geocoding Search Handler (Nominatim API)
        var searchInput = document.getElementById(containerId + '-search-input');
        var searchBtn = document.getElementById(containerId + '-search-btn');
        var searchResults = document.getElementById(containerId + '-search-results');

        function doSearch() {
            var query = searchInput.value.trim();
            if (!query) return;

            searchBtn.disabled = true;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            var apiUrl = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&limit=5&email=support@janmitram.com';

            fetch(apiUrl)
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    searchBtn.disabled = false;
                    searchBtn.innerHTML = 'Search';
                    searchResults.innerHTML = '';

                    if (!data || data.length === 0) {
                        var noResDiv = document.createElement('div');
                        noResDiv.className = 'list-group-item list-group-item-light small text-muted';
                        noResDiv.textContent = 'No locations found for "' + query + '"';
                        searchResults.appendChild(noResDiv);
                        searchResults.classList.remove('d-none');
                        return;
                    }

                    data.forEach(function(item) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'list-group-item list-group-item-action text-start small py-2';
                        
                        var icon = document.createElement('i');
                        icon.className = 'fas fa-map-marker-alt me-1 text-danger';
                        btn.appendChild(icon);

                        var textSpan = document.createElement('span');
                        textSpan.textContent = ' ' + (item.display_name || '');
                        btn.appendChild(textSpan);

                        btn.onclick = function() {
                            var nLat = parseFloat(item.lat);
                            var nLng = parseFloat(item.lon);
                            map.setView([nLat, nLng], 15);
                            marker.setLatLng([nLat, nLng]);
                            updateInputs(nLat, nLng);
                            searchResults.classList.add('d-none');
                            searchInput.value = item.display_name || '';
                        };
                        searchResults.appendChild(btn);
                    });

                    searchResults.classList.remove('d-none');
                })
                .catch(function(err) {
                    searchBtn.disabled = false;
                    searchBtn.innerHTML = 'Search';
                    console.error('Janmitram Geocoding Error:', err);
                });
        }
                    console.error('Janmitram Geocoding Error:', err);
                });
        }

        if (searchBtn) searchBtn.onclick = doSearch;
        if (searchInput) {
            searchInput.onkeypress = function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    doSearch();
                }
            };
        }

        // Hide search results on outside click
        document.addEventListener('click', function(e) {
            if (searchResults && !controlDiv.contains(e.target)) {
                searchResults.classList.add('d-none');
            }
        });

        // GPS Location Handler
        var gpsBtn = document.getElementById(containerId + '-gps-btn');
        if (gpsBtn) {
            gpsBtn.onclick = function() {
                if (!navigator.geolocation) {
                    alert('Geolocation is not supported by your browser.');
                    return;
                }

                gpsBtn.disabled = true;
                gpsBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Locating...';

                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        gpsBtn.disabled = false;
                        gpsBtn.innerHTML = '<i class="fas fa-crosshairs"></i> Use My Location';
                        var gLat = pos.coords.latitude;
                        var gLng = pos.coords.longitude;
                        map.setView([gLat, gLng], 16);
                        marker.setLatLng([gLat, gLng]);
                        updateInputs(gLat, gLng);
                    },
                    function(err) {
                        gpsBtn.disabled = false;
                        gpsBtn.innerHTML = '<i class="fas fa-crosshairs"></i> Use My Location';
                        alert('Could not get current location: ' + err.message);
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            };
        }
    }

    return {
        map: map,
        marker: marker,
        invalidateSize: function() {
            setTimeout(function() {
                map.invalidateSize();
            }, 200);
        }
    };
};
