<script setup>
import L from "leaflet";
import { ref, onMounted, onUnmounted, watch, nextTick } from "vue";

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

// Default coordinates: India or prop coordinates
const currentLat = ref(props.latitude || 27.005694931660006);
const currentLng = ref(props.longitude || 75.77754972401056);

const inputLat = ref(currentLat.value);
const inputLng = ref(currentLng.value);

function sanitizeCoords(lat, lng) {
    let pLat = parseFloat(lat);
    let pLng = parseFloat(lng);
    if (isNaN(pLat) || isNaN(pLng) || (pLat === 0 && pLng === 0)) {
        pLat = 27.005694931660006;
        pLng = 75.77754972401056;
    }
    return { lat: pLat, lng: pLng };
}

const customIcon = L.icon({
    iconUrl: "/assets/icons/home.png",
    iconSize: [35, 35],
    iconAnchor: [17, 35],
    popupAnchor: [0, -30],
});

function updateLocation(lat, lng, triggerEmit = true) {
    const coords = sanitizeCoords(lat, lng);
    currentLat.value = coords.lat;
    currentLng.value = coords.lng;
    inputLat.value = coords.lat;
    inputLng.value = coords.lng;

    if (marker) {
        marker.setLatLng([coords.lat, coords.lng]);
    }
    if (map) {
        map.setView([coords.lat, coords.lng], map.getZoom() || 15);
    }

    if (triggerEmit) {
        emit("location-updated", { lat: coords.lat, lng: coords.lng });
    }
}

function applyCustomCoordinates() {
    let pLat = parseFloat(inputLat.value);
    let pLng = parseFloat(inputLng.value);

    if (isNaN(pLat) || isNaN(pLng)) {
        alert("Please enter valid numeric latitude and longitude values.");
        return;
    }

    if (pLat < -90 || pLat > 90) {
        alert("Latitude must be between -90 and 90.");
        return;
    }

    if (pLng < -180 || pLng > 180) {
        alert("Longitude must be between -180 and 180.");
        return;
    }

    updateLocation(pLat, pLng, true);
}

function initMap() {
    if (!mapContainer.value) return;

    const coords = sanitizeCoords(currentLat.value, currentLng.value);

    map = L.map(mapContainer.value).setView([coords.lat, coords.lng], 15);

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

    marker = L.marker([coords.lat, coords.lng], {
        draggable: props.enableSetLocation,
        icon: customIcon,
    }).addTo(map);

    if (props.enableSetLocation) {
        map.on("click", (e) => {
            updateLocation(e.latlng.lat, e.latlng.lng);
        });

        marker.on("dragend", () => {
            const pos = marker.getLatLng();
            updateLocation(pos.lat, pos.lng);
        });
    }

    nextTick(() => {
        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 200);
    });
}

function handleSearch() {
    const query = searchQuery.value.trim();
    if (!query) return;

    isSearching.value = true;
    const apiUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&email=support@janmitram.com`;

    fetch(apiUrl)
        .then((res) => res.json())
        .then((data) => {
            isSearching.value = false;
            searchResults.value = data || [];
            showResults.value = true;
        })
        .catch((err) => {
            isSearching.value = false;
            console.error("Leaflet Geocoding Error:", err);
        });
}

function selectSearchResult(result) {
    const nLat = parseFloat(result.lat);
    const nLng = parseFloat(result.lon);
    updateLocation(nLat, nLng);
    searchQuery.value = result.display_name;
    showResults.value = false;
}

function handleGetCurrentLocation() {
    if (!navigator.geolocation) {
        alert("Geolocation is not supported by your browser.");
        return;
    }

    isLocating.value = true;
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            isLocating.value = false;
            updateLocation(pos.coords.latitude, pos.coords.longitude);
        },
        (err) => {
            isLocating.value = false;
            alert("Could not detect current location: " + err.message);
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

onMounted(() => {
    initMap();

    if (navigator.geolocation && !props.hasOldValue && !props.latitude) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                updateLocation(pos.coords.latitude, pos.coords.longitude);
            },
            (err) => {
                console.warn("Location access denied:", err.message);
            }
        );
    }
});

onUnmounted(() => {
    if (map) {
        map.remove();
        map = null;
    }
});

watch(
    [() => props.latitude, () => props.longitude],
    ([newLat, newLng]) => {
        if (newLat != null && newLng != null && !isNaN(newLat) && !isNaN(newLng)) {
            updateLocation(newLat, newLng, false);
            if (map) {
                map.setView([newLat, newLng], map.getZoom() || 15);
            }
        }
    }
);
</script>

<template>
    <div class="janmitram-map-wrapper w-full flex flex-col gap-2 position-relative">
        <!-- Optional Search Bar & GPS Controls when enableSetLocation is true -->
        <div v-if="enableSetLocation" class="flex flex-wrap sm:flex-nowrap gap-2 items-center z-[1000] bg-white p-2 rounded-lg border shadow-sm">
            <div class="relative grow">
                <div class="flex rounded-md shadow-sm">
                    <input
                        v-model="searchQuery"
                        type="text"
                        class="block w-full rounded-l-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm px-3 py-1.5 border"
                        placeholder="Search location (e.g. Raipur, Delhi)..."
                        @keyup.enter="handleSearch"
                    />
                    <button
                        type="button"
                        class="inline-flex items-center px-3 py-1.5 border border-l-0 border-gray-300 rounded-r-md bg-primary text-white text-sm font-medium hover:bg-primary-dark"
                        :disabled="isSearching"
                        @click="handleSearch"
                    >
                        <span v-if="!isSearching">Search</span>
                        <span v-else>Searching...</span>
                    </button>
                </div>

                <!-- Autocomplete Dropdown -->
                <div
                    v-if="showResults && searchResults.length"
                    class="absolute z-[2000] mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                >
                    <button
                        v-for="(item, index) in searchResults"
                        :key="index"
                        type="button"
                        class="w-full text-left px-4 py-2 hover:bg-gray-100 text-sm text-gray-700 flex items-start gap-2 border-b last:border-b-0"
                        @click="selectSearchResult(item)"
                    >
                        <span class="text-red-500 font-bold">📍</span>
                        <span>{{ item.display_name }}</span>
                    </button>
                </div>
            </div>

            <button
                type="button"
                class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-2 bg-green-50 text-green-700 border border-green-300 rounded-md hover:bg-green-100 shrink-0"
                :disabled="isLocating"
                @click="handleGetCurrentLocation"
            >
                <span>🎯</span>
                <span>{{ isLocating ? 'Locating...' : 'Use My Location' }}</span>
            </button>
        </div>

        <!-- Leaflet Map Container -->
        <div
            ref="mapContainer"
            :style="{ width: width, height: height }"
            class="rounded-xl border shadow-inner overflow-hidden z-10"
        ></div>

        <!-- Interactive Latitude & Longitude Input Bar -->
        <div v-if="enableSetLocation" class="mt-2 p-3 bg-slate-50 border rounded-lg shadow-sm flex flex-col sm:flex-row items-center gap-3">
            <div class="flex-1 w-full">
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    📍 Latitude
                </label>
                <input
                    v-model="inputLat"
                    type="number"
                    step="any"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-xs px-3 py-2 border bg-white"
                    placeholder="e.g. 21.251384"
                    @keyup.enter="applyCustomCoordinates"
                />
            </div>

            <div class="flex-1 w-full">
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    📍 Longitude
                </label>
                <input
                    v-model="inputLng"
                    type="number"
                    step="any"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-xs px-3 py-2 border bg-white"
                    placeholder="e.g. 81.629641"
                    @keyup.enter="applyCustomCoordinates"
                />
            </div>

            <div class="w-full sm:w-auto sm:self-end">
                <button
                    type="button"
                    class="w-full inline-flex items-center justify-center gap-1 text-xs font-medium px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark shadow-sm"
                    @click="applyCustomCoordinates"
                >
                    <span>📍</span>
                    <span>Locate on Map</span>
                </button>
            </div>
        </div>
    </div>
</template>
