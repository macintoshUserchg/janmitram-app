<template>
    <div class="janmitram-realtime-map-wrapper w-full relative">
        <div
            ref="mapContainer"
            :style="{ width: width, height: height, minHeight: '400px' }"
            class="rounded-2xl overflow-hidden shadow-md border border-slate-200 bg-slate-100 relative z-0"
        ></div>

        <!-- Floating Status Badge -->
        <div class="absolute top-3 left-3 z-[1000] bg-white/95 backdrop-blur px-3.5 py-2 rounded-xl shadow-lg border border-slate-200/80 flex items-center gap-2 text-xs">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span class="font-bold text-slate-800">Live Delivery Tracking</span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from "vue";
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
let gMap = null;
let riderMarker = null;
let customerMarker = null;
let routePolyline = null;
let resizeObserver = null;

function isValidCoord(loc) {
    if (!loc) return false;
    const lat = parseFloat(loc.lat);
    const lng = parseFloat(loc.lng);
    return !isNaN(lat) && !isNaN(lng) && (lat !== 0 || lng !== 0);
}

function loadGoogleMapsSdk(apiKey) {
    return new Promise((resolve, reject) => {
        if (window.google && window.google.maps) {
            resolve(window.google.maps);
            return;
        }

        const existing = document.getElementById("google-maps-sdk");
        if (existing) {
            existing.addEventListener("load", () => resolve(window.google.maps));
            existing.addEventListener("error", reject);
            return;
        }

        const script = document.createElement("script");
        script.id = "google-maps-sdk";
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places,geometry`;
        script.async = true;
        script.defer = true;
        script.onload = () => resolve(window.google.maps);
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

function updateMapLayers() {
    if (!gMap || !window.google || !window.google.maps) return;

    const rValid = isValidCoord(props.riderLocation);
    const cValid = isValidCoord(props.customerLocation);

    const bounds = new window.google.maps.LatLngBounds();
    let pointsCount = 0;

    // Customer Doorstep Marker
    if (cValid) {
        const cLat = parseFloat(props.customerLocation.lat);
        const cLng = parseFloat(props.customerLocation.lng);
        const cPos = new window.google.maps.LatLng(cLat, cLng);

        if (!customerMarker) {
            customerMarker = new window.google.maps.Marker({
                position: cPos,
                map: gMap,
                title: "Your Delivery Address",
                icon: {
                    url: "/assets/icons/home.png",
                    scaledSize: new window.google.maps.Size(40, 40),
                },
            });
        } else {
            customerMarker.setPosition(cPos);
        }
        bounds.extend(cPos);
        pointsCount++;
    }

    // Rider Live Location Marker
    if (rValid) {
        const rLat = parseFloat(props.riderLocation.lat);
        const rLng = parseFloat(props.riderLocation.lng);
        const rPos = new window.google.maps.LatLng(rLat, rLng);

        if (!riderMarker) {
            riderMarker = new window.google.maps.Marker({
                position: rPos,
                map: gMap,
                title: "Delivery Rider",
                icon: {
                    url: "/assets/icons/pin-map.png",
                    scaledSize: new window.google.maps.Size(42, 42),
                },
            });
        } else {
            riderMarker.setPosition(rPos);
        }
        bounds.extend(rPos);
        pointsCount++;
    }

    // Route Polyline
    if (rValid && cValid) {
        const rPos = new window.google.maps.LatLng(parseFloat(props.riderLocation.lat), parseFloat(props.riderLocation.lng));
        const cPos = new window.google.maps.LatLng(parseFloat(props.customerLocation.lat), parseFloat(props.customerLocation.lng));

        const path = [rPos, cPos];

        if (!routePolyline) {
            routePolyline = new window.google.maps.Polyline({
                path: path,
                geodesic: true,
                strokeColor: "#f59e0b",
                strokeOpacity: 0.85,
                strokeWeight: 4,
                map: gMap,
            });
        } else {
            routePolyline.setPath(path);
        }

        gMap.fitBounds(bounds, 70);
    } else if (pointsCount === 1) {
        gMap.setCenter(bounds.getCenter());
        gMap.setZoom(16);
    }
}

async function initMap() {
    if (!mapContainer.value) return;

    let googleKey = "";
    try {
        const configRes = await axios.get("/maps/config");
        googleKey = configRes.data?.data?.api_key || "";
    } catch (e) {
        console.warn("Maps config error:", e);
    }

    if (!googleKey) {
        googleKey = import.meta.env.VITE_GOOGLE_MAPS_KEY || "";
    }

    await loadGoogleMapsSdk(googleKey);
    if (!mapContainer.value || !window.google || !window.google.maps) return;

    const rValid = isValidCoord(props.riderLocation);
    const cValid = isValidCoord(props.customerLocation);

    const defaultLat = rValid ? parseFloat(props.riderLocation.lat) : (cValid ? parseFloat(props.customerLocation.lat) : 27.0056949);
    const defaultLng = rValid ? parseFloat(props.riderLocation.lng) : (cValid ? parseFloat(props.customerLocation.lng) : 75.7775497);

    gMap = new window.google.maps.Map(mapContainer.value, {
        center: { lat: defaultLat, lng: defaultLng },
        zoom: 14,
        mapTypeId: window.google.maps.MapTypeId.ROADMAP,
        disableDefaultUI: false,
        zoomControl: true,
        mapTypeControl: false,
        streetViewControl: false,
    });

    updateMapLayers();

    if (window.ResizeObserver && mapContainer.value) {
        resizeObserver = new ResizeObserver(() => {
            if (gMap && window.google) {
                window.google.maps.event.trigger(gMap, "resize");
            }
        });
        resizeObserver.observe(mapContainer.value);
    }

    nextTick(() => {
        setTimeout(() => {
            if (gMap && window.google) {
                window.google.maps.event.trigger(gMap, "resize");
            }
        }, 300);
    });
}

watch(
    () => [props.riderLocation, props.customerLocation],
    () => {
        updateMapLayers();
    },
    { deep: true }
);

onMounted(() => {
    initMap();
});

onUnmounted(() => {
    if (resizeObserver) {
        resizeObserver.disconnect();
        resizeObserver = null;
    }
    if (routePolyline) {
        routePolyline.setMap(null);
        routePolyline = null;
    }
    if (riderMarker) {
        riderMarker.setMap(null);
        riderMarker = null;
    }
    if (customerMarker) {
        customerMarker.setMap(null);
        customerMarker = null;
    }
    gMap = null;
});
</script>
