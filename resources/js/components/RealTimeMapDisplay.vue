<template>
    <div class="ola-realtime-map-wrapper w-full relative">
        <div
            ref="mapContainer"
            :style="{ width: width, height: height }"
            class="rounded-xl overflow-hidden shadow-inner border border-gray-200 bg-gray-100 relative"
        ></div>

        <!-- Floating Status Overlay -->
        <div class="absolute top-3 left-3 z-10 bg-white/95 backdrop-blur px-3 py-1.5 rounded-lg shadow-md border border-gray-100 flex items-center gap-2 text-xs">
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
import * as maplibregl from "maplibre-gl";
import "maplibre-gl/dist/maplibre-gl.css";
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

function isValidCoord(loc) {
    if (!loc) return false;
    const lat = parseFloat(loc.lat);
    const lng = parseFloat(loc.lng);
    return !isNaN(lat) && !isNaN(lng) && (lat !== 0 || lng !== 0);
}

function createCustomIconElement(iconUrl, size = 38) {
    const el = document.createElement("div");
    el.className = "custom-map-pin transition-transform duration-300";
    el.style.width = `${size}px`;
    el.style.height = `${size}px`;
    el.style.backgroundImage = `url(${iconUrl})`;
    el.style.backgroundSize = "contain";
    el.style.backgroundRepeat = "no-repeat";
    el.style.backgroundPosition = "center";
    el.style.cursor = "pointer";
    el.style.filter = "drop-shadow(0 4px 6px rgba(0,0,0,0.25))";
    return el;
}

async function fetchAndDrawRoute(rLng, rLat, cLng, cLat) {
    if (!map) return;

    try {
        const res = await axios.get("/api/maps/directions", {
            params: {
                origin_lat: rLat,
                origin_lng: rLng,
                dest_lat: cLat,
                dest_lng: cLng,
            },
        });

        const geo = res.data?.data?.geometry;
        let coordinates = [];

        if (Array.isArray(geo)) {
            coordinates = geo;
        } else if (geo && geo.coordinates) {
            coordinates = geo.coordinates;
        } else {
            coordinates = [
                [rLng, rLat],
                [cLng, cLat],
            ];
        }

        const routeGeoJSON = {
            type: "Feature",
            properties: {},
            geometry: {
                type: "LineString",
                coordinates: coordinates,
            },
        };

        if (map.getSource("route")) {
            map.getSource("route").setData(routeGeoJSON);
        } else {
            map.addSource("route", {
                type: "geojson",
                data: routeGeoJSON,
            });

            map.addLayer({
                id: "route-casing",
                type: "line",
                source: "route",
                layout: { "line-join": "round", "line-cap": "round" },
                paint: {
                    "line-color": "#ffffff",
                    "line-width": 7,
                    "line-opacity": 0.9,
                },
            });

            map.addLayer({
                id: "route-line",
                type: "line",
                source: "route",
                layout: { "line-join": "round", "line-cap": "round" },
                paint: {
                    "line-color": "#ff6b00", // Ola Orange route
                    "line-width": 4,
                    "line-opacity": 0.95,
                },
            });
        }
    } catch (e) {
        console.warn("Could not fetch directions polyline:", e);
    }
}

async function updateMapLayers() {
    if (!map) return;

    const rValid = isValidCoord(props.riderLocation);
    const cValid = isValidCoord(props.customerLocation);

    const bounds = new maplibregl.LngLatBounds();

    // Customer Marker
    if (cValid) {
        const cLat = parseFloat(props.customerLocation.lat);
        const cLng = parseFloat(props.customerLocation.lng);

        if (!customerMarker) {
            const cIconEl = createCustomIconElement("/assets/icons/home.png", 38);
            customerMarker = new maplibregl.Marker({ element: cIconEl, anchor: "bottom" })
                .setLngLat([cLng, cLat])
                .setPopup(new maplibregl.Popup({ offset: 25 }).setHTML("<div class='p-1 font-semibold text-xs'>Your Delivery Address</div>"))
                .addTo(map);
        } else {
            customerMarker.setLngLat([cLng, cLat]);
        }
        bounds.extend([cLng, cLat]);
    }

    // Rider Marker
    if (rValid) {
        const rLat = parseFloat(props.riderLocation.lat);
        const rLng = parseFloat(props.riderLocation.lng);

        if (!riderMarker) {
            const rIconEl = createCustomIconElement("/assets/icons/pin-map.png", 42);
            riderMarker = new maplibregl.Marker({ element: rIconEl, anchor: "bottom" })
                .setLngLat([rLng, rLat])
                .setPopup(new maplibregl.Popup({ offset: 25 }).setHTML("<div class='p-1 font-semibold text-xs'>Delivery Rider</div>"))
                .addTo(map);
        } else {
            riderMarker.setLngLat([rLng, rLat]);
        }
        bounds.extend([rLng, rLat]);
    }

    // Draw route if both valid
    if (rValid && cValid) {
        fetchAndDrawRoute(
            parseFloat(props.riderLocation.lng),
            parseFloat(props.riderLocation.lat),
            parseFloat(props.customerLocation.lng),
            parseFloat(props.customerLocation.lat)
        );

        if (!bounds.isEmpty()) {
            map.fitBounds(bounds, { padding: 60, maxZoom: 16, duration: 1000 });
        }
    } else if (cValid) {
        map.flyTo({ center: [parseFloat(props.customerLocation.lng), parseFloat(props.customerLocation.lat)], zoom: 14 });
    }
}

async function initMap() {
    if (!mapContainer.value) return;

    const rValid = isValidCoord(props.riderLocation);
    const cValid = isValidCoord(props.customerLocation);

    const defaultLat = rValid ? parseFloat(props.riderLocation.lat) : (cValid ? parseFloat(props.customerLocation.lat) : 27.0056949);
    const defaultLng = rValid ? parseFloat(props.riderLocation.lng) : (cValid ? parseFloat(props.customerLocation.lng) : 75.7775497);

    // Fetch Ola Maps API config
    let mapStyle = "https://demotiles.maplibre.org/style.json";
    try {
        const configRes = await axios.get("/api/maps/config");
        if (configRes.data?.data) {
            const cfg = configRes.data.data;
            if (cfg.api_key) {
                mapStyle = `${cfg.tiles_url}?api_key=${cfg.api_key}`;
            }
        }
    } catch (e) {
        console.warn("Maps config fetch error, using fallback style:", e);
    }

    map = new maplibregl.Map({
        container: mapContainer.value,
        style: mapStyle,
        center: [defaultLng, defaultLat],
        zoom: 13,
        attributionControl: false,
    });

    map.addControl(new maplibregl.NavigationControl({ showCompass: false }), "top-right");

    map.on("load", () => {
        updateMapLayers();
    });

    nextTick(() => {
        setTimeout(() => {
            if (map) map.resize();
        }, 200);
    });
}

watch(
    () => props.riderLocation,
    (newLoc) => {
        if (isValidCoord(newLoc) && riderMarker) {
            riderMarker.setLngLat([parseFloat(newLoc.lng), parseFloat(newLoc.lat)]);
            if (isValidCoord(props.customerLocation)) {
                fetchAndDrawRoute(
                    parseFloat(newLoc.lng),
                    parseFloat(newLoc.lat),
                    parseFloat(props.customerLocation.lng),
                    parseFloat(props.customerLocation.lat)
                );
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
