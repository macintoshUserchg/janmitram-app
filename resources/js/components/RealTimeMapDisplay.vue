<script setup>
import L from "leaflet";
import { ref, onMounted, onUnmounted, watch, nextTick } from "vue";

const props = defineProps({
    width: { type: String, default: "100%" },
    height: { type: String, default: "500px" },
    riderLocation: {
        type: Object,
        required: true,
        default: () => ({ lat: 0, lng: 0 }),
    },
    customerLocation: {
        type: Object,
        required: true,
        default: () => ({ lat: 0, lng: 0 }),
    },
});

const mapContainer = ref(null);
let map = null;
let riderMarker = null;
let customerMarker = null;
let routePolyline = null;

const customerIcon = L.icon({
    iconUrl: "/assets/icons/home.png",
    iconSize: [35, 35],
    iconAnchor: [17, 35],
    popupAnchor: [0, -30],
});

const riderIcon = L.icon({
    iconUrl: "/assets/icons/pin-map.png",
    iconSize: [35, 35],
    iconAnchor: [17, 35],
    popupAnchor: [0, -30],
});

function isValidCoord(loc) {
    if (!loc) return false;
    const lat = parseFloat(loc.lat);
    const lng = parseFloat(loc.lng);
    return !isNaN(lat) && !isNaN(lng) && (lat !== 0 || lng !== 0);
}

function initMap() {
    if (!mapContainer.value) return;

    const rValid = isValidCoord(props.riderLocation);
    const cValid = isValidCoord(props.customerLocation);

    const defaultLat = rValid ? parseFloat(props.riderLocation.lat) : (cValid ? parseFloat(props.customerLocation.lat) : 27.005694931660006);
    const defaultLng = rValid ? parseFloat(props.riderLocation.lng) : (cValid ? parseFloat(props.customerLocation.lng) : 75.77754972401056);

    map = L.map(mapContainer.value).setView([defaultLat, defaultLng], 13);

    const primaryTiles = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        subdomains: ["a", "b", "c"],
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    });

    const fallbackTiles = L.tileLayer("https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png", {
        maxZoom: 19,
        subdomains: "abcd",
        attribution: "&copy; CartoDB &copy; OpenStreetMap",
    });

    primaryTiles.addTo(map);

    let tileErrors = 0;
    primaryTiles.on("tileerror", () => {
        tileErrors++;
        if (tileErrors >= 3 && !map.hasLayer(fallbackTiles)) {
            map.removeLayer(primaryTiles);
            fallbackTiles.addTo(map);
        }
    });

    updateMapLayers();

    nextTick(() => {
        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 200);
    });
}

function updateMapLayers() {
    if (!map) return;

    const rValid = isValidCoord(props.riderLocation);
    const cValid = isValidCoord(props.customerLocation);

    const bounds = [];

    // Customer Marker
    if (cValid) {
        const cLat = parseFloat(props.customerLocation.lat);
        const cLng = parseFloat(props.customerLocation.lng);
        bounds.push([cLat, cLng]);

        if (!customerMarker) {
            customerMarker = L.marker([cLat, cLng], { icon: customerIcon }).addTo(map).bindPopup("Customer Location");
        } else {
            customerMarker.setLatLng([cLat, cLng]);
        }
    }

    // Rider Marker
    if (rValid) {
        const rLat = parseFloat(props.riderLocation.lat);
        const rLng = parseFloat(props.riderLocation.lng);
        bounds.push([rLat, rLng]);

        if (!riderMarker) {
            riderMarker = L.marker([rLat, rLng], { icon: riderIcon }).addTo(map).bindPopup("Rider Location");
        } else {
            riderMarker.setLatLng([rLat, rLng]);
        }
    }

    // Route Polyline (OSRM Real Road Routing)
    if (rValid && cValid) {
        const rLat = parseFloat(props.riderLocation.lat);
        const rLng = parseFloat(props.riderLocation.lng);
        const cLat = parseFloat(props.customerLocation.lat);
        const cLng = parseFloat(props.customerLocation.lng);

        const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${rLng},${rLat};${cLng},${cLat}?overview=full&geometries=geojson`;

        fetch(osrmUrl)
            .then((res) => res.json())
            .then((data) => {
                let routePoints = [];
                if (data && data.routes && data.routes.length > 0 && data.routes[0].geometry) {
                    // OSRM returns [lng, lat] array, map to [lat, lng] for Leaflet
                    routePoints = data.routes[0].geometry.coordinates.map((pt) => [pt[1], pt[0]]);
                } else {
                    routePoints = [[rLat, rLng], [cLat, cLng]];
                }

                if (!routePolyline) {
                    routePolyline = L.polyline(routePoints, { color: "#2563eb", weight: 5, opacity: 0.85 }).addTo(map);
                } else {
                    routePolyline.setLatLngs(routePoints);
                }

                if (map) {
                    map.fitBounds(routePolyline.getBounds(), { padding: [50, 50] });
                }
            })
            .catch(() => {
                const fallbackPoints = [[rLat, rLng], [cLat, cLng]];
                if (!routePolyline) {
                    routePolyline = L.polyline(fallbackPoints, { color: "#2563eb", weight: 5, opacity: 0.8, dashArray: "8, 8" }).addTo(map);
                } else {
                    routePolyline.setLatLngs(fallbackPoints);
                }
                if (map) {
                    map.fitBounds(L.latLngBounds(bounds), { padding: [50, 50] });
                }
            });
    } else if (bounds.length > 0) {
        if (bounds.length === 1) {
            map.setView(bounds[0], 14);
        } else {
            map.fitBounds(L.latLngBounds(bounds), { padding: [50, 50] });
        }
    }
}

onMounted(() => {
    initMap();
});

onUnmounted(() => {
    if (map) {
        map.remove();
        map = null;
    }
});

watch(
    [() => props.riderLocation, () => props.customerLocation],
    () => {
        updateMapLayers();
    },
    { deep: true }
);
</script>

<template>
    <div
        ref="mapContainer"
        :style="{ width: width, height: height }"
        class="w-full rounded-xl border shadow-inner overflow-hidden"
    ></div>
</template>
