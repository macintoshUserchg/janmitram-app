<template>
    <div class="ola-map-wrapper w-full">
        <!-- Floating / Integrated Search & Geolocation Control Bar -->
        <div v-if="enableSetLocation" class="mb-2 flex flex-wrap gap-2 items-center">
            <!-- Search Input with Autocomplete Dropdown -->
            <div class="relative flex-1 min-w-[240px]">
                <div class="relative">
                    <input
                        type="text"
                        v-model="searchQuery"
                        @input="onSearchInput"
                        @focus="showResults = searchResults.length > 0"
                        placeholder="Search area, landmark, or street in India..."
                        class="w-full pl-9 pr-8 py-2 text-sm bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                    />
                    <!-- Search Icon -->
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <!-- Clear / Loading Icon -->
                    <span v-if="isSearching" class="absolute right-3 top-2.5 text-gray-400">
                        <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <button
                        v-else-if="searchQuery"
                        type="button"
                        @click="clearSearch"
                        class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
                    >
                        &times;
                    </button>
                </div>

                <!-- Autocomplete Dropdown Menu -->
                <div
                    v-if="showResults && searchResults.length > 0"
                    class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                >
                    <button
                        type="button"
                        v-for="(item, idx) in searchResults"
                        :key="idx"
                        @click="selectSearchResult(item)"
                        class="w-full text-left px-3 py-2.5 text-xs hover:bg-gray-50 border-b border-gray-100 flex items-start gap-2 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-700 leading-snug">{{ item.display_name }}</span>
                    </button>
                </div>
            </div>

            <!-- GPS Auto-Detect Button -->
            <button
                type="button"
                @click="detectCurrentGPSLocation"
                :disabled="isLocating"
                class="px-3 py-2 text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg border border-gray-300 shadow-sm flex items-center gap-1.5 transition-colors disabled:opacity-50"
                title="Detect My GPS Location"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
                <span>{{ isLocating ? 'Detecting...' : 'Use My GPS' }}</span>
            </button>
        </div>

        <!-- Ola Maps Vector Container -->
        <div
            ref="mapContainer"
            :style="{ width: width, height: height }"
            class="rounded-xl overflow-hidden shadow-inner border border-gray-200 relative bg-gray-100"
        ></div>

        <!-- Lat/Lng Quick Coordinate Inputs (if enabled) -->
        <div v-if="enableSetLocation" class="mt-2 grid grid-cols-2 gap-2 text-xs">
            <div>
                <label class="block text-gray-500 font-medium mb-1">Latitude</label>
                <input
                    type="number"
                    step="0.0000001"
                    v-model.number="inputLat"
                    @change="applyManualCoordinates"
                    class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-300 rounded font-mono text-gray-700 text-xs"
                />
            </div>
            <div>
                <label class="block text-gray-500 font-medium mb-1">Longitude</label>
                <input
                    type="number"
                    step="0.0000001"
                    v-model.number="inputLng"
                    @change="applyManualCoordinates"
                    class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-300 rounded font-mono text-gray-700 text-xs"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from "vue";
import * as maplibregl from "maplibre-gl";
import "maplibre-gl/dist/maplibre-gl.css";
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
        default: "300px",
    },
    latitude: {
        type: Number,
    },
    longitude: {
        type: Number,
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

const searchQuery = ref("");
const searchResults = ref([]);
const isSearching = ref(false);
const showResults = ref(false);
const isLocating = ref(false);

// Default Jaipur, India coordinates
const currentLat = ref(props.latitude || 27.0056949);
const currentLng = ref(props.longitude || 75.7775497);

const inputLat = ref(currentLat.value);
const inputLng = ref(currentLng.value);

let searchTimeout = null;

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
        marker.setLngLat([coords.lng, coords.lat]);
    }
    if (map) {
        map.flyTo({ center: [coords.lng, coords.lat], zoom: map.getZoom() || 14, essential: true });
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
    }, 350);
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

            // Fetch address from reverse geocode
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
            alert("Could not detect GPS location. Please check your browser permissions.");
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

async function initMap() {
    if (!mapContainer.value) return;

    const coords = sanitizeCoords(props.latitude, props.longitude);
    currentLat.value = coords.lat;
    currentLng.value = coords.lng;
    inputLat.value = parseFloat(coords.lat.toFixed(7));
    inputLng.value = parseFloat(coords.lng.toFixed(7));

    // Fetch Ola Maps API config from backend
    let mapStyle = "https://demotiles.maplibre.org/style.json";
    let apiKey = import.meta.env.VITE_OLA_MAPS_API_KEY || "";

    try {
        const configRes = await axios.get("/api/maps/config");
        if (configRes.data?.data) {
            const cfg = configRes.data.data;
            if (cfg.api_key) {
                apiKey = cfg.api_key;
                mapStyle = `${cfg.tiles_url}?api_key=${cfg.api_key}`;
            }
        }
    } catch (e) {
        console.warn("Maps config fetch error, using fallback style:", e);
    }

    // Initialize MapLibre GL with Ola Maps Vector Style
    map = new maplibregl.Map({
        container: mapContainer.value,
        style: mapStyle,
        center: [coords.lng, coords.lat],
        zoom: 14,
        attributionControl: false,
    });

    // Add navigation controls (zoom in/out)
    map.addControl(new maplibregl.NavigationControl({ showCompass: false }), "top-right");

    // Add draggable pin marker
    marker = new maplibregl.Marker({
        draggable: props.enableSetLocation,
        color: "#ff6b00", // Ola Orange
    })
        .setLngLat([coords.lng, coords.lat])
        .addTo(map);

    if (props.enableSetLocation) {
        // Marker dragend
        marker.on("dragend", () => {
            const lngLat = marker.getLngLat();
            updateLocation(lngLat.lat, lngLat.lng, true);
        });

        // Map click
        map.on("click", (e) => {
            updateLocation(e.lngLat.lat, e.lngLat.lng, true);
        });
    }

    nextTick(() => {
        setTimeout(() => {
            if (map) map.resize();
        }, 200);
    });
}

watch(
    () => [props.latitude, props.longitude],
    ([newLat, newLng]) => {
        if (newLat && newLng) {
            updateLocation(newLat, newLng, false);
        }
    }
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

<style scoped>
.ola-map-wrapper {
    font-family: inherit;
}
</style>
