<template>
    <div class="janmitram-realtime-map-wrapper w-full relative">
        <div
            ref="mapContainer"
            :style="{ width: width, height: height, minHeight: '400px' }"
            class="rounded-xl overflow-hidden shadow-sm border border-gray-300 bg-slate-100 relative z-0"
        ></div>

        <!-- Floating Status Badge -->
        <div class="absolute top-3 left-3 z-[1000] bg-white/95 backdrop-blur px-3 py-1.5 rounded-lg shadow-md border border-gray-200 flex items-center gap-2 text-xs">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span class="font-semibold text-gray-700">Live Delivery Tracking</span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import axios from "axios";

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
    iconSize: [38, 38],
    iconAnchor: [19, 38],
    popupAnchor: [0, -35],
});

const riderIcon = L.icon({
    iconUrl: "/assets/icons/pin-map.png",
    iconSize: [42, 42],
    iconAnchor: [21, 42],
    popupAnchor: [0, -38],
});

function isValidCoord(loc) {
    if (!loc) return false;
    const lat = parseFloat(loc.lat);
    const lng = parseFloat(loc.lng);
    return !isNaN(lat) && !isNaN(lng) && (lat !== 0 || lng !== 0);
}

function updateMapLayers() {
    if (!map) return;

    const rValid = isValidCoord(props.riderLocation);
    const cValid = isValidCoord(props.customerLocation);

    const latLngs = [];

    // Customer Marker
    if (cValid) {
        const cLat = parseFloat(props.customerLocation.lat);
        const cLng = parseFloat(props.customerLocation.lng);
        const cLatLng = L.latLng(cLat, cLng);

        if (!customerMarker) {
            customerMarker = L.marker(cLatLng, { icon: customerIcon })
                .bindPopup("<div class='font-semibold text-xs'>Your Delivery Address</div>")
                .addTo(map);
        } else {
            customerMarker.setLatLng(cLatLng);
        }
        latLngs.push(cLatLng);
    }

    // Rider Marker
    if (rValid) {
        const rLat = parseFloat(props.riderLocation.lat);
        const rLng = parseFloat(props.riderLocation.lng);
        const rLatLng = L.latLng(rLat, rLng);

        if (!riderMarker) {
            riderMarker = L.marker(rLatLng, { icon: riderIcon })
                .bindPopup("<div class='font-semibold text-xs'>Delivery Rider</div>")
                .addTo(map);
        } else {
            riderMarker.setLatLng(rLatLng);
        }
        latLngs.push(rLatLng);
    }

    // Draw route line
    if (rValid && cValid) {
        const rLat = parseFloat(props.riderLocation.lat);
        const rLng = parseFloat(props.riderLocation.lng);
        const cLat = parseFloat(props.customerLocation.lat);
        const cLng = parseFloat(props.customerLocation.lng);

        if (!routePolyline) {
            routePolyline = L.polyline([[rLat, rLng], [cLat, cLng]], {
                color: "#ff6b00",
                weight: 4,
                opacity: 0.9,
                dashArray: "8, 6",
            }).addTo(map);
        } else {
            routePolyline.setLatLngs([[rLat, rLng], [cLat, cLng]]);
        }

        if (latLngs.length > 0) {
            const bounds = L.latLngBounds(latLngs);
            map.fitBounds(bounds, { padding: [60, 60], maxZoom: 16 });
        }
    } else if (cValid) {
        map.setView([parseFloat(props.customerLocation.lat), parseFloat(props.customerLocation.lng)], 14);
    }
}

function initMap() {
    if (!mapContainer.value) return;

    if (map) {
        map.remove();
        map = null;
    }

    const rValid = isValidCoord(props.riderLocation);
    const cValid = isValidCoord(props.customerLocation);

    const defaultLat = rValid ? parseFloat(props.riderLocation.lat) : (cValid ? parseFloat(props.customerLocation.lat) : 27.0056949);
    const defaultLng = rValid ? parseFloat(props.riderLocation.lng) : (cValid ? parseFloat(props.customerLocation.lng) : 75.7775497);

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
        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 500);
    });
}

watch(
    () => props.riderLocation,
    (newLoc) => {
        if (isValidCoord(newLoc) && riderMarker) {
            riderMarker.setLatLng([parseFloat(newLoc.lat), parseFloat(newLoc.lng)]);
            if (routePolyline && isValidCoord(props.customerLocation)) {
                routePolyline.setLatLngs([
                    [parseFloat(newLoc.lat), parseFloat(newLoc.lng)],
                    [parseFloat(props.customerLocation.lat), parseFloat(props.customerLocation.lng)],
                ]);
            }
        }
    },
    { deep: true }
);

watch(
    () => props.customerLocation,
    () => {
        updateMapLayers();
    },
    { deep: true }
);

onMounted(() => {
    initMap();
});

onUnmounted(() => {
    if (map) {
        map.remove();
        map = null;
    }
});
</script>
