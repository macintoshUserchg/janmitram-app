/**
 * Janmitram Google Maps Interactive Geolocation Helper
 * Provides Address Search (Google Places Autocomplete), GPS Current Location Detection,
 * Draggable Pin Markers, and Bi-directional Coordinate Sync.
 */

window.initJanmitramMap = function(config) {
    var containerId = config.containerId || 'map';
    var mapEl = document.getElementById(containerId);
    if (!mapEl) return null;

    var defaultLat = parseFloat(config.lat) || 27.005694931660006;
    var defaultLng = parseFloat(config.lng) || 75.77754972401056;
    var zoom = config.zoom || 15;
    var isDraggable = config.draggable !== false;

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

    // If Google Maps is loaded, initialize native Google Map
    if (typeof google !== 'undefined' && google.maps) {
        var centerLatLng = new google.maps.LatLng(defaultLat, defaultLng);

        var map = new google.maps.Map(mapEl, {
            center: centerLatLng,
            zoom: zoom,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoomControl: true,
            mapTypeControl: false,
            streetViewControl: false,
        });

        var marker = new google.maps.Marker({
            position: centerLatLng,
            map: map,
            draggable: isDraggable,
            animation: google.maps.Animation.DROP,
        });

        if (config.popupText) {
            var infoWindow = new google.maps.InfoWindow({
                content: config.popupText,
            });
            infoWindow.open(map, marker);
        }

        if (isDraggable) {
            marker.addListener('dragend', function() {
                var pos = marker.getPosition();
                updateInputs(pos.lat(), pos.lng());
            });

            map.addListener('click', function(e) {
                marker.setPosition(e.latLng);
                updateInputs(e.latLng.lat(), e.latLng.lng());
            });

            function onManualInputChange() {
                if (!latInput || !lngInput) return;
                var nLat = parseFloat(latInput.value);
                var nLng = parseFloat(lngInput.value);
                if (!isNaN(nLat) && !isNaN(nLng) && nLat !== 0 && nLng !== 0) {
                    var newPos = new google.maps.LatLng(nLat, nLng);
                    map.panTo(newPos);
                    marker.setPosition(newPos);
                }
            }

            if (latInput) latInput.addEventListener('change', onManualInputChange);
            if (lngInput) lngInput.addEventListener('change', onManualInputChange);
        }

        // Render Search & GPS controls if enabled
        if (config.showControls !== false) {
            injectGoogleMapControls(mapEl, map, marker, updateInputs);
        }

        return map;
    }

    return null;
};

function injectGoogleMapControls(mapEl, map, marker, updateInputs) {
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
    searchInput.placeholder = 'Search address or place in India...';
    searchInput.autocomplete = 'off';

    var dropdown = document.createElement('div');
    dropdown.className = 'list-group position-absolute w-100 shadow-lg';
    dropdown.style.cssText = 'z-index: 1050; max-height: 220px; overflow-y: auto; display: none; top: 100%; left: 0;';

    searchWrapper.appendChild(searchInput);
    searchWrapper.appendChild(dropdown);

    var gpsBtn = document.createElement('button');
    gpsBtn.type = 'button';
    gpsBtn.className = 'btn btn-outline-secondary btn-sm d-flex align-items-center gap-1';
    gpsBtn.innerHTML = '<i class="fas fa-crosshairs text-success"></i> My Location';
    gpsBtn.title = 'Detect My GPS Location';

    controlDiv.appendChild(searchWrapper);
    controlDiv.appendChild(gpsBtn);
    parent.insertBefore(controlDiv, mapEl);

    // Google Places Search
    var searchTimeout = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        var q = searchInput.value.trim();
        if (q.length < 2) {
            dropdown.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(function() {
            var currentPos = marker ? marker.getPosition() : null;
            var lat = currentPos ? currentPos.lat() : 27.0056949;
            var lng = currentPos ? currentPos.lng() : 75.7775497;

            fetch('/api/maps/autocomplete?input=' + encodeURIComponent(q) + '&lat=' + lat + '&lng=' + lng + '&limit=5')
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    dropdown.innerHTML = '';
                    var results = res.data || [];
                    if (results.length === 0) {
                        dropdown.style.display = 'none';
                        return;
                    }

                    results.forEach(function(item) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'list-group-item list-group-item-action text-start py-2 px-3 small';
                        btn.innerHTML = '<i class="fas fa-map-marker-alt text-danger me-2"></i>' + (item.display_name || '');
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            searchInput.value = item.display_name;
                            dropdown.style.display = 'none';

                            if (item.lat && item.lng) {
                                var newPos = new google.maps.LatLng(item.lat, item.lng);
                                map.panTo(newPos);
                                map.setZoom(16);
                                marker.setPosition(newPos);
                                updateInputs(item.lat, item.lng);
                            } else if (item.place_id) {
                                fetch('/api/maps/place-details?place_id=' + encodeURIComponent(item.place_id))
                                    .then(function(pr) { return pr.json(); })
                                    .then(function(pres) {
                                        if (pres.data && pres.data.lat && pres.data.lng) {
                                            var nPos = new google.maps.LatLng(pres.data.lat, pres.data.lng);
                                            map.panTo(nPos);
                                            map.setZoom(16);
                                            marker.setPosition(nPos);
                                            updateInputs(pres.data.lat, pres.data.lng);
                                        }
                                    });
                            }
                        });
                        dropdown.appendChild(btn);
                    });
                    dropdown.style.display = 'block';
                })
                .catch(function() {
                    dropdown.style.display = 'none';
                });
        }, 200);
    });

    document.addEventListener('click', function(e) {
        if (!searchWrapper.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    gpsBtn.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }

        gpsBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Locating...';
        gpsBtn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                gpsBtn.innerHTML = '<i class="fas fa-crosshairs text-success"></i> My Location';
                gpsBtn.disabled = false;
                var gPos = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
                map.panTo(gPos);
                map.setZoom(16);
                marker.setPosition(gPos);
                updateInputs(pos.coords.latitude, pos.coords.longitude);
            },
            function(err) {
                gpsBtn.innerHTML = '<i class="fas fa-crosshairs text-success"></i> My Location';
                gpsBtn.disabled = false;
                alert('Could not detect GPS location. Please check browser permissions.');
            },
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
        );
    });
}
