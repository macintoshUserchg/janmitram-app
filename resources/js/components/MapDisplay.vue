<template>
    <div class="modern-ola-map-wrapper w-full">
        <!-- Floating / Integrated Search & Geolocation Control Bar -->
        <div v-if="enableSetLocation" class="mb-3 flex flex-wrap gap-2 items-center">
            <!-- Search Autocomplete Input -->
            <div class="relative flex-1 min-w-[240px]">
                <div class="relative">
                    <input
                        type="text"
                        v-model="searchQuery"
                        @input="onSearchInput"
                        @focus="showResults = searchResults.length > 0"
                        placeholder="Search area, landmark, or street in India..."
                        class="w-full pl-10 pr-9 py-2.5 text-sm bg-white border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all font-medium text-slate-800 placeholder:text-slate-400"
                    />
                    <!-- Search Icon -->
                    <span class="absolute left-3.5 top-3 text-amber-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <!-- Spinner / Clear Icon -->
                    <span v-if="isSearching" class="absolute right-3 top-3 text-amber-500">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <button
                        v-else-if="searchQuery"
                        type="button"
                        @click="clearSearch"
                        class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-700 text-base font-bold"
                    >
                        &times;
                    </button>
                </div>

                <!-- Dropdown Search Results -->
                <div
                    v-if="showResults && searchResults.length > 0"
                    class="absolute z-[1000] left-0 right-0 mt-1 bg-white border border-slate-100 rounded-xl shadow-2xl max-h-60 overflow-y-auto divide-y divide-slate-100"
                >
                    <button
                        type="button"
                        v-for="(item, idx) in searchResults"
                        :key="idx"
                        @click="selectSearchResult(item)"
                        class="w-full text-left px-3.5 py-2.5 text-xs hover:bg-amber-50/80 flex items-start gap-2.5 transition-colors cursor-pointer"
                    >
                        <div class="w-5 h-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-slate-700 font-medium leading-snug">{{ item.display_name }}</span>
                    </button>
                </div>
            </div>

            <!-- GPS Auto-Detect Button -->
            <button
                type="button"
                @click="detectCurrentGPSLocation"
                :disabled="isLocating"
                class="px-4 py-2.5 text-xs font-semibold bg-white hover:bg-slate-50 text-slate-800 rounded-xl border border-slate-200 shadow-sm flex items-center gap-2 transition-all hover:shadow active:scale-95 disabled:opacity-50 cursor-pointer"
                title="Detect My GPS Location"
            >
                <div class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </div>
                <span>{{ isLocating ? 'Locating...' : 'Use My GPS' }}</span>
            </button>
        </div>

        <!-- Interactive Map Container -->
        <div class="relative rounded-2xl overflow-hidden shadow-inner border border-slate-200 bg-slate-100">
            <div
                ref="mapContainer"
                :style="{ width: width, height: height, minHeight: '320px' }"
                class="w-full relative z-0"
            ></div>

            <!-- Floating Coordinates & Location Badge -->
            <div class="absolute bottom-3 left-3 z-[400] bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-lg shadow-sm border border-slate-200/80 flex items-center gap-2 text-[11px] text-slate-700 pointer-events-none">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="font-mono font-medium">{{ currentLat.toFixed(5) }}, {{ currentLng.toFixed(5) }}</span>
            </div>
        </div>

        <!-- Lat/Lng Coordinate Manual Adjustment Inputs -->
        <div v-if="enableSetLocation" class="mt-2.5 grid grid-cols-2 gap-3 text-xs">
            <div>
                <label class="block text-slate-500 font-medium mb-1">Latitude</label>
                <input
                    type="number"
                    step="0.0000001"
                    v-model.number="inputLat"
                    @change="applyManualCoordinates"
                    class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-mono text-slate-700 text-xs focus:ring-1 focus:ring-amber-500 focus:outline-none"
                />
            </div>
            <div>
                <label class="block text-slate-500 font-medium mb-1">Longitude</label>
                <input
                    type="number"
                    step="0.0000001"
                    v-model.number="inputLng"
                    @change="applyManualCoordinates"
                    class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-mono text-slate-700 text-xs focus:ring-1 focus:ring-amber-500 focus:outline-none"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import axios from "axios";

const props = defineProps({
    enableSetLocation: {
        type: Boolean,
        default: false,
    },
    width: {
        type: String,
        default: "100%",
    },
    height: {
        type: String,
        default: "340px",
    },
    latitude: {
        type: [Number, String],
    },
    longitude: {
        type: [Number, String],
    },
    hasOldValue: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["location-updated"]);

const mapContainer = ref(null);
let map = null;
let marker = null;
let resizeObserver = null;

const searchQuery = ref("");
const searchResults = ref([]);
const isSearching = ref(false);
const showResults = ref(false);
const isLocating = ref(false);

// Default Jaipur, India coordinates
const currentLat = ref(27.0056949);
const currentLng = ref(75.7775497);

const inputLat = ref(currentLat.value);
const inputLng = ref(currentLng.value);

let searchTimeout = null;

// Modern SVG Location Pin
const customPinHtml = `
    <div style="position: relative; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
        <div style="position: absolute; width: 14px; height: 14px; background: rgba(245, 158, 11, 0.35); border-radius: 50%; bottom: 0; filter: blur(2px);"></div>
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3)); transform: translateY(-4px);">
            <path d="M12 2C8.13401 2 5 5.13401 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13401 15.866 2 12 2Z" fill="#f59e0b" stroke="#ffffff" stroke-width="1.5"/>
            <circle cx="12" cy="9" r="3.5" fill="#ffffff"/>
        </svg>
    </div>
`;

const modernPinIcon = L.divIcon({
    html: customPinHtml,
    className: "modern-custom-pin",
    iconSize: [38, 38],
    iconAnchor: [19, 36],
    popupAnchor: [0, -34],
});

function sanitizeCoords(lat, lng) {
    let pLat = parseFloat(lat);
    let pLng = parseFloat(lng);
    if (isNaN(pLat) || isNaN(pLng) || (pLat === 0 && pLng === 0)) {
        pLat = 27.0056949;
        pLng = 75.7775497;
    }
    return { lat: pLat, lng: pLng };
}

function updateLocation(lat, lng, triggerEmit = true, address = "") {
    const coords = sanitizeCoords(lat, lng);
    currentLat.value = coords.lat;
    currentLng.value = coords.lng;
    inputLat.value = parseFloat(coords.lat.toFixed(7));
    inputLng.value = parseFloat(coords.lng.toFixed(7));

    if (marker) {
        marker.setLatLng([coords.lat, coords.lng]);
    }
    if (map) {
        map.setView([coords.lat, coords.lng], map.getZoom() || 15);
    }

    if (triggerEmit) {
        emit("location-updated", {
            lat: coords.lat,
            lng: coords.lng,
            address: address,
        });
    }
}

function applyManualCoordinates() {
    let pLat = parseFloat(inputLat.value);
    let pLng = parseFloat(inputLng.value);

    if (isNaN(pLat) || isNaN(pLng) || pLat < -90 || pLat > 90 || pLng < -180 || pLng > 180) {
        return;
    }

    updateLocation(pLat, pLng, true);
}

function onSearchInput() {
    clearTimeout(searchTimeout);
    if (!searchQuery.value || searchQuery.value.trim().length < 2) {
        searchResults.value = [];
        showResults.value = false;
        return;
    }

    isSearching.value = true;
    searchTimeout = setTimeout(async () => {
        try {
            const response = await axios.get("/api/maps/autocomplete", {
                params: {
                    input: searchQuery.value,
                    lat: currentLat.value,
                    lng: currentLng.value,
                    limit: 5,
                },
            });

            if (response.data && response.data.data) {
                searchResults.value = response.data.data;
                showResults.value = searchResults.value.length > 0;
            }
        } catch (e) {
            console.warn("Autocomplete error:", e);
        } finally {
            isSearching.value = false;
        }
    }, 300);
}

function selectSearchResult(item) {
    if (item.lat && item.lng) {
        searchQuery.value = item.display_name;
        showResults.value = false;
        updateLocation(item.lat, item.lng, true, item.display_name);
    }
}

function clearSearch() {
    searchQuery.value = "";
    searchResults.value = [];
    showResults.value = false;
}

function detectCurrentGPSLocation() {
    if (!navigator.geolocation) {
        alert("Geolocation is not supported by your browser.");
        return;
    }

    isLocating.value = true;
    navigator.geolocation.getCurrentPosition(
        async (position) => {
            isLocating.value = false;
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            updateLocation(lat, lng, true);

            try {
                const res = await axios.get("/api/maps/reverse-geocode", {
                    params: { lat, lng },
                });
                if (res.data?.data?.display_name) {
                    searchQuery.value = res.data.data.display_name;
                }
            } catch (err) {
                console.warn("Reverse geocode warning:", err);
            }
        },
        (error) => {
            isLocating.value = false;
            console.warn("GPS Geolocation error:", error);
            alert("Could not detect GPS location. Please check browser permissions.");
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

function initMap() {
    if (!mapContainer.value) return;

    if (map) {
        map.remove();
        map = null;
    }

    const coords = sanitizeCoords(props.latitude, props.longitude);
    currentLat.value = coords.lat;
    currentLng.value = coords.lng;
    inputLat.value = parseFloat(coords.lat.toFixed(7));
    inputLng.value = parseFloat(coords.lng.toFixed(7));

    // Initialize Leaflet with smooth animations
    map = L.map(mapContainer.value, {
        center: [coords.lat, coords.lng],
        zoom: 15,
        zoomControl: false,
        attributionControl: false,
    });

    // Custom Minimal Zoom Control on Bottom Right
    L.control.zoom({ position: "bottomright" }).addTo(map);

    // High-definition modern CartoDB Voyager tiles (crisp streets, soft pastels, clean labels)
    const modernTiles = L.tileLayer(
        "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
        {
            maxZoom: 20,
            subdomains: "abcd",
        }
    );

    // Fallback standard OSM tiles
    const fallbackOsmTiles = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        subdomains: ["a", "b", "c"],
    });

    modernTiles.addTo(map);

    let tileErrors = 0;
    modernTiles.on("tileerror", () => {
        tileErrors++;
        if (tileErrors >= 3 && !map.hasLayer(fallbackOsmTiles)) {
            map.removeLayer(modernTiles);
            fallbackOsmTiles.addTo(map);
        }
    });

    // Add marker
    marker = L.marker([coords.lat, coords.lng], {
        draggable: props.enableSetLocation,
        icon: modernPinIcon,
    }).addTo(map);

    if (props.enableSetLocation) {
        marker.on("dragend", () => {
            const pos = marker.getLatLng();
            updateLocation(pos.lat, pos.lng, true);
        });

        map.on("click", (e) => {
            updateLocation(e.latlng.lat, e.latlng.lng, true);
        });
    }

    // Modal / Container dynamic resize observer
    if (window.ResizeObserver && mapContainer.value) {
        resizeObserver = new ResizeObserver(() => {
            if (map) map.invalidateSize();
        });
        resizeObserver.observe(mapContainer.value);
    }

    nextTick(() => {
        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 200);
        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 600);
    });
}

watch(
    () => [props.latitude, props.longitude],
    ([newLat, newLng]) => {
        if (newLat && newLng) {
            const parsedLat = parseFloat(newLat);
            const parsedLng = parseFloat(newLng);
            if (!isNaN(parsedLat) && !isNaN(parsedLng) && (parsedLat !== 0 || parsedLng !== 0)) {
                updateLocation(parsedLat, parsedLng, false);
            }
        }
    },
    { immediate: true }
);

onMounted(() => {
    initMap();
});

onUnmounted(() => {
    if (resizeObserver) {
        resizeObserver.disconnect();
        resizeObserver = null;
    }
    if (map) {
        map.remove();
        map = null;
    }
});
</script>

<style>
.modern-custom-pin {
    background: transparent !important;
    border: none !important;
}
.leaflet-control-zoom {
    border: none !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    border-radius: 12px !important;
    overflow: hidden;
}
.leaflet-control-zoom a {
    background-color: #ffffff !important;
    color: #334155 !important;
    border: none !important;
    width: 32px !important;
    height: 32px !important;
    line-height: 32px !important;
    font-size: 14px !important;
    font-weight: bold !important;
}
.leaflet-control-zoom a:hover {
    background-color: #f8fafc !important;
    color: #f59e0b !important;
}
</style>
