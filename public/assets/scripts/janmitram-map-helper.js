/**
 * Janmitram Ola Maps / MapLibre GL Interactive Geolocation Helper
 * Provides Address Search (Ola Places Autocomplete / Nominatim fallback),
 * GPS Current Location Detection, Draggable Pin Markers, and Bi-directional Coordinate Sync.
 */

window.initJanmitramMap = function(config) {
    var containerId = config.containerId || 'map';
    var mapEl = document.getElementById(containerId);
    if (!mapEl) return null;

    var defaultLat = parseFloat(config.lat) || 27.005694931660006;
    var defaultLng = parseFloat(config.lng) || 75.77754972401056;
    var zoom = config.zoom || 13;
    var isDraggable = config.draggable !== false;

    // Sanitize coordinates
    if (isNaN(defaultLat) || isNaN(defaultLng) || (defaultLat === 0 && defaultLng === 0)) {
        defaultLat = 27.005694931660006;
        defaultLng = 75.77754972401056;
    }

    var latInput = document.getElementById(config.latInputId || 'latitude');
    var lngInput = document.getElementById(config.lngInputId || 'longitude');

    function updateInputs(lat, lng) {
        if (latInput) latInput.value = parseFloat(lat).toFixed(7);
        if (lngInput) lngInput.value = parseFloat(lng).toFixed(7);
        if (typeof config.onLocationChange === 'function') {
            config.onLocationChange(lat, lng);
        }
    }

    updateInputs(defaultLat, defaultLng);

    // If MapLibre GL is loaded, initialize vector map
    if (typeof maplibregl !== 'undefined') {
        var mapStyle = config.tilesUrl || 'https://demotiles.maplibre.org/style.json';
        if (config.apiKey) {
            mapStyle = (config.tilesUrl || 'https://api.olamaps.io/tiles/vector/v1/styles/default-light-standard/style.json') + '?api_key=' + config.apiKey;
        }

        var map = new maplibregl.Map({
            container: containerId,
            style: mapStyle,
            center: [defaultLng, defaultLat],
            zoom: zoom,
            attributionControl: false
        });

        map.addControl(new maplibregl.NavigationControl({ showCompass: false }), 'top-right');

        var marker = new maplibregl.Marker({
            draggable: isDraggable,
            color: '#ff6b00' // Ola Orange
        })
        .setLngLat([defaultLng, defaultLat])
        .addTo(map);

        if (config.popupText) {
            marker.setPopup(new maplibregl.Popup({ offset: 25 }).setHTML(config.popupText));
        }

        if (isDraggable) {
            marker.on('dragend', function() {
                var lngLat = marker.getLngLat();
                updateInputs(lngLat.lat, lngLat.lng);
            });

            map.on('click', function(e) {
                marker.setLngLat([e.lngLat.lng, e.lngLat.lat]);
                updateInputs(e.lngLat.lat, e.lngLat.lng);
            });

            function onManualInputChange() {
                if (!latInput || !lngInput) return;
                var nLat = parseFloat(latInput.value);
                var nLng = parseFloat(lngInput.value);
                if (!isNaN(nLat) && !isNaN(nLng) && nLat !== 0 && nLng !== 0) {
                    map.flyTo({ center: [nLng, nLat], zoom: map.getZoom() });
                    marker.setLngLat([nLng, nLat]);
                }
            }

            if (latInput) latInput.addEventListener('change', onManualInputChange);
            if (lngInput) lngInput.addEventListener('change', onManualInputChange);
        }

        // Render Search & GPS controls if enabled
        if (config.showControls !== false) {
            injectMapControls(mapEl, map, marker, updateInputs);
        }

        setTimeout(function() {
            if (map) map.resize();
        }, 300);

        return map;
    }

    // Leaflet fallback if MapLibre GL is unavailable
    if (typeof L !== 'undefined') {
        var lMap = L.map(containerId).setView([defaultLat, defaultLng], zoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(lMap);

        var lMarker = L.marker([defaultLat, defaultLng], { draggable: isDraggable }).addTo(lMap);

        if (isDraggable) {
            lMarker.on('dragend', function() {
                var pos = lMarker.getLatLng();
                updateInputs(pos.lat, pos.lng);
            });
            lMap.on('click', function(e) {
                lMarker.setLatLng(e.latlng);
                updateInputs(e.latlng.lat, e.latlng.lng);
            });
        }
        return lMap;
    }

    return null;
};

function injectMapControls(mapEl, map, marker, updateInputs) {
    var parent = mapEl.parentNode;
    if (!parent) return;

    var controlDiv = document.createElement('div');
    controlDiv.className = 'janmitram-map-controls mb-2 d-flex flex-wrap gap-2 align-items-center';

    var searchWrapper = document.createElement('div');
    searchWrapper.className = 'position-relative flex-grow-1';
    searchWrapper.style.minWidth = '240px';

    var searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control form-control-sm';
    searchInput.placeholder = 'Search area, landmark, or street in India...';

    var dropdown = document.createElement('div');
    dropdown.className = 'position-absolute w-100 bg-white border rounded shadow-sm overflow-auto';
    dropdown.style.maxHeight = '200px';
    dropdown.style.zIndex = '1050';
    dropdown.style.display = 'none';

    searchWrapper.appendChild(searchInput);
    searchWrapper.appendChild(dropdown);

    var gpsBtn = document.createElement('button');
    gpsBtn.type = 'button';
    gpsBtn.className = 'btn btn-sm btn-outline-primary d-flex align-items-center gap-1';
    gpsBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Use My GPS';

    controlDiv.appendChild(searchWrapper);
    controlDiv.appendChild(gpsBtn);
    parent.insertBefore(controlDiv, mapEl);

    // Search Autocomplete Handler
    var searchTimer = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        var val = searchInput.value.trim();
        if (val.length < 2) {
            dropdown.style.display = 'none';
            return;
        }

        searchTimer = setTimeout(function() {
            fetch('/api/maps/autocomplete?input=' + encodeURIComponent(val) + '&limit=5')
                .then(function(res) { return res.json(); })
                .then(function(resData) {
                    dropdown.innerHTML = '';
                    var results = resData.data || [];
                    if (results.length === 0) {
                        dropdown.style.display = 'none';
                        return;
                    }

                    results.forEach(function(item) {
                        var opt = document.createElement('a');
                        opt.href = 'javascript:void(0)';
                        opt.className = 'd-block px-2 py-1.5 small text-dark text-decoration-none border-bottom hover-bg-light';
                        opt.textContent = item.display_name;
                        opt.addEventListener('click', function() {
                            searchInput.value = item.display_name;
                            dropdown.style.display = 'none';
                            if (item.lat && item.lng) {
                                map.flyTo({ center: [item.lng, item.lat], zoom: 14 });
                                marker.setLngLat([item.lng, item.lat]);
                                updateInputs(item.lat, item.lng);
                            }
                        });
                        dropdown.appendChild(opt);
                    });
                    dropdown.style.display = 'block';
                })
                .catch(function(e) { console.warn('Search error:', e); });
        }, 350);
    });

    // GPS Button Click
    gpsBtn.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Geolocation not supported by your browser.');
            return;
        }
        gpsBtn.disabled = true;
        gpsBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Locating...';

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                gpsBtn.disabled = false;
                gpsBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Use My GPS';
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                map.flyTo({ center: [lng, lat], zoom: 14 });
                marker.setLngLat([lng, lat]);
                updateInputs(lat, lng);

                fetch('/api/maps/reverse-geocode?lat=' + lat + '&lng=' + lng)
                    .then(function(r) { return r.json(); })
                    .then(function(rd) {
                        if (rd.data && rd.data.display_name) {
                            searchInput.value = rd.data.display_name;
                        }
                    });
            },
            function(err) {
                gpsBtn.disabled = false;
                gpsBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Use My GPS';
                alert('Could not detect GPS location. Please check browser permissions.');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });
}
