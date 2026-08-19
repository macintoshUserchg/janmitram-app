<template>
    <div class="google-map-component w-full select-none" ref="mapWrapperRef">
        <!-- Top Search Bar & GPS Geolocation Control -->
        <div v-if="enableSetLocation" class="mb-3 flex flex-wrap gap-2 items-center">
            <!-- Search Autocomplete Input -->
            <div class="relative flex-1 min-w-[260px]">
                <div class="relative">
                    <input
                        ref="searchInputRef"
                        type="text"
                        v-model="searchQuery"
                        @input="onSearchInput"
                        @focus="onInputFocus"
                        @keydown.enter.prevent.stop="handleEnterSearch"
                        @keydown.down.prevent.stop="navigateResults(1)"
                        @keydown.up.prevent.stop="navigateResults(-1)"
                        @keydown.esc.prevent.stop="clearSearch"
                        placeholder="Search colony, street, gali, shop, or pin code in India..."
                        class="w-full pl-10 pr-9 py-2.5 text-sm bg-white border border-slate-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all font-medium text-slate-800 placeholder:text-slate-400"
                    />
                    <!-- Search Icon -->
                    <span class="absolute left-3.5 top-3 text-amber-500 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <!-- Spinner / Clear Icon -->
                    <span v-if="isSearching" class="absolute right-3 top-3 text-amber-500 pointer-events-none">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <button
                        v-else-if="searchQuery"
                        type="button"
                        @click="clearSearch"
                        class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-700 text-base font-bold cursor-pointer"
                    >
                        &times;
                    </button>
                </div>

                <!-- Dropdown Search Results (Google Places) -->
                <div
                    v-if="showResults && searchResults.length > 0"
                    class="absolute z-[1000] left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-2xl max-h-72 overflow-y-auto divide-y divide-slate-100"
                >
                    <button
                        type="button"
                        v-for="(item, idx) in searchResults"
                        :key="idx"
                        @click="selectSearchResult(item)"
                        :class="selectedSearchIndex === idx ? 'bg-amber-100/90 font-bold' : 'hover:bg-amber-50'"
                        class="w-full text-left px-3.5 py-2.5 text-xs flex items-start gap-2.5 transition-colors cursor-pointer"
                    >
                        <div class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-900 font-semibold text-xs leading-snug">
                                {{ item.main_text || item.display_name.split(',')[0] }}
                            </p>
                            <p class="text-slate-500 text-[11px] truncate leading-tight mt-0.5">
                                {{ item.secondary_text || item.display_name }}
                            </p>
                        </div>
                    </button>
                </div>
            </div>

            <!-- GPS Auto-Detect Button -->
            <button
                type="button"
                @click="detectCurrentGPSLocation"
                :disabled="isLocating"
                class="px-4 py-2.5 text-xs font-semibold bg-white hover:bg-slate-50 text-slate-800 rounded-xl border border-slate-300 shadow-sm flex items-center gap-2 transition-all hover:shadow active:scale-95 disabled:opacity-50 cursor-pointer"
                title="Detect My GPS Location"
            >
                <div class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </div>
                <span>{{ isLocating ? 'Detecting GPS...' : 'Use My GPS' }}</span>
            </button>
        </div>

        <!-- Google Map Container with Layer Toggle & Descriptive Overlays -->
        <div class="relative rounded-2xl overflow-hidden shadow-md border border-slate-300 bg-slate-100">
            <!-- Map Mount Div -->
            <div
                ref="mapContainer"
                :style="{ width: width, height: height, minHeight: '340px' }"
                class="w-full relative z-0"
            ></div>

            <!-- Map Layer Switcher (Roadmap vs Satellite) -->
            <div class="absolute top-3 right-3 z-[400] bg-white/95 backdrop-blur-md p-1 rounded-xl shadow-md border border-slate-200 flex items-center gap-1 text-xs">
                <button
                    type="button"
                    @click="setMapLayer('streets')"
                    :class="activeLayer === 'streets' ? 'bg-amber-500 text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-100 font-medium'"
                    class="px-2.5 py-1 rounded-lg transition-all cursor-pointer flex items-center gap-1 text-[11px]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    Roadmap
                </button>
                <button
                    type="button"
                    @click="setMapLayer('satellite')"
                    :class="activeLayer === 'satellite' ? 'bg-amber-500 text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-100 font-medium'"
                    class="px-2.5 py-1 rounded-lg transition-all cursor-pointer flex items-center gap-1 text-[11px]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Satellite
                </button>
            </div>

            <!-- Drag Instruction Tip -->
            <div v-if="enableSetLocation && !heuristicNotice" class="absolute top-3 left-3 z-[400] bg-slate-900/80 backdrop-blur-md text-white px-3 py-1.5 rounded-lg text-[11px] font-medium shadow flex items-center gap-1.5 pointer-events-none">
                <span class="animate-bounce">📍</span>
                <span>Drag pin or click map to set exact delivery doorstep</span>
            </div>

            <!-- Heuristic Search Notification Banner -->
            <div v-if="heuristicNotice" class="absolute top-3 left-3 right-32 z-[400] bg-amber-500/95 backdrop-blur-md text-white px-3.5 py-2 rounded-xl text-xs font-semibold shadow-lg flex items-center gap-2 transition-all">
                <span>{{ heuristicNotice }}</span>
            </div>

            <!-- Rich Descriptive Address Overlay Card at Bottom -->
            <div class="absolute bottom-3 left-3 right-16 z-[400] bg-white/95 backdrop-blur-md px-3.5 py-2.5 rounded-xl shadow-lg border border-slate-200/90 text-xs">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 text-xs">Selected Doorstep Location</span>
                            <span v-if="isGeocodingAddress" class="text-[10px] text-amber-600 font-semibold animate-pulse">Resolving address...</span>
                        </div>
                        <p class="text-slate-700 font-medium text-[11px] leading-snug line-clamp-2 mt-0.5">
                            {{ descriptiveAddress || 'Click or drag the pin anywhere to select location' }}
                        </p>
                        <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-500 font-mono">
                            <span class="bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">Lat: {{ currentLat.toFixed(6) }}</span>
                            <span class="bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">Lng: {{ currentLng.toFixed(6) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lat/Lng Manual Adjustment Fields -->
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
        default: "360px",
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

const mapWrapperRef = ref(null);
const mapContainer = ref(null);
const searchInputRef = ref(null);

let gMap = null;
let gMarker = null;
let resizeObserver = null;

const activeLayer = ref("streets");

const searchQuery = ref("");
const searchResults = ref([]);
const selectedSearchIndex = ref(-1);
const isSearching = ref(false);
const showResults = ref(false);
const isLocating = ref(false);
const isGeocodingAddress = ref(false);
const descriptiveAddress = ref("");
const heuristicNotice = ref("");

// Client-side instant in-memory search cache & AbortController
const searchCache = new Map();
let currentAbortController = null;

// Default Jaipur, India coordinates
const currentLat = ref(27.0056949);
const currentLng = ref(75.7775497);

const inputLat = ref(currentLat.value);
const inputLng = ref(currentLng.value);

let searchTimeout = null;
let geocodeTimeout = null;

function sanitizeCoords(lat, lng) {
    let pLat = parseFloat(lat);
    let pLng = parseFloat(lng);
    if (isNaN(pLat) || isNaN(pLng) || (pLat === 0 && pLng === 0)) {
        pLat = 27.0056949;
        pLng = 75.7775497;
    }
    return { lat: pLat, lng: pLng };
}

async function reverseGeocodeCoords(lat, lng) {
    clearTimeout(geocodeTimeout);
    isGeocodingAddress.value = true;

    geocodeTimeout = setTimeout(async () => {
        try {
            const res = await axios.get("/maps/reverse-geocode", {
                params: { lat, lng },
            });
            if (res.data?.data?.display_name) {
                descriptiveAddress.value = res.data.data.display_name;
                emit("location-updated", {
                    lat,
                    lng,
                    address: res.data.data.display_name,
                });
            }
        } catch (e) {
            console.warn("Reverse geocode notice:", e);
        } finally {
            isGeocodingAddress.value = false;
        }
    }, 300);
}

function updateLocation(lat, lng, triggerEmit = true, address = "") {
    const coords = sanitizeCoords(lat, lng);
    currentLat.value = coords.lat;
    currentLng.value = coords.lng;
    inputLat.value = parseFloat(coords.lat.toFixed(7));
    inputLng.value = parseFloat(coords.lng.toFixed(7));

    if (address) {
        descriptiveAddress.value = address;
    }

    if (gMap && gMarker && window.google && window.google.maps) {
        const gLatLng = new window.google.maps.LatLng(coords.lat, coords.lng);
        gMarker.setPosition(gLatLng);
        gMap.panTo(gLatLng);
    }

    if (triggerEmit) {
        emit("location-updated", {
            lat: coords.lat,
            lng: coords.lng,
            address: address || descriptiveAddress.value,
        });

        if (!address) {
            reverseGeocodeCoords(coords.lat, coords.lng);
        }
    }
}

function setMapLayer(layerType) {
    activeLayer.value = layerType;

    if (gMap && window.google && window.google.maps) {
        gMap.setMapTypeId(
            layerType === "satellite"
                ? window.google.maps.MapTypeId.HYBRID
                : window.google.maps.MapTypeId.ROADMAP
        );
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

function onInputFocus() {
    if (searchResults.value.length > 0) {
        showResults.value = true;
    }
}

function onSearchInput() {
    clearTimeout(searchTimeout);
    selectedSearchIndex.value = -1;

    const val = (searchQuery.value || "").trim();
    if (val.length < 2) {
        searchResults.value = [];
        showResults.value = false;
        isSearching.value = false;
        return;
    }

    // Check instant in-memory cache
    if (searchCache.has(val)) {
        searchResults.value = searchCache.get(val);
        showResults.value = searchResults.value.length > 0;
        return;
    }

    isSearching.value = true;
    searchTimeout = setTimeout(async () => {
        if (currentAbortController) {
            currentAbortController.abort();
        }
        currentAbortController = new AbortController();

        try {
            const response = await axios.get("/maps/autocomplete", {
                params: {
                    input: val,
                    lat: currentLat.value,
                    lng: currentLng.value,
                    limit: 6,
                },
                signal: currentAbortController.signal,
            });

            if (response.data?.data) {
                searchResults.value = response.data.data;
                searchCache.set(val, response.data.data);
                showResults.value = searchResults.value.length > 0;
            }
        } catch (e) {
            if (!axios.isCancel(e)) {
                console.warn("Autocomplete notice:", e);
            }
        } finally {
            isSearching.value = false;
        }
    }, 180);
}

function navigateResults(dir) {
    if (!searchResults.value || searchResults.value.length === 0) return;
    showResults.value = true;
    selectedSearchIndex.value += dir;
    if (selectedSearchIndex.value < 0) {
        selectedSearchIndex.value = searchResults.value.length - 1;
    } else if (selectedSearchIndex.value >= searchResults.value.length) {
        selectedSearchIndex.value = 0;
    }
}

async function handleEnterSearch() {
    clearTimeout(searchTimeout);

    if (selectedSearchIndex.value >= 0 && searchResults.value[selectedSearchIndex.value]) {
        selectSearchResult(searchResults.value[selectedSearchIndex.value]);
        return;
    }

    if (searchResults.value && searchResults.value.length > 0) {
        selectSearchResult(searchResults.value[0]);
        return;
    }

    const val = (searchQuery.value || "").trim();
    if (val.length < 2) return;

    isSearching.value = true;
    heuristicNotice.value = "";

    try {
        let response = await axios.get("/maps/autocomplete", {
            params: {
                input: val,
                lat: currentLat.value,
                lng: currentLng.value,
                limit: 8,
            },
        });

        if (response.data?.data && response.data.data.length > 0) {
            searchResults.value = response.data.data;
            searchCache.set(val, response.data.data);
            showResults.value = true;
            selectSearchResult(response.data.data[0]);
            return;
        }

        let heuristicRes = await axios.get("/maps/heuristic-search", {
            params: {
                query: val,
                lat: currentLat.value,
                lng: currentLng.value,
            },
        });

        if (heuristicRes.data?.data && heuristicRes.data.data.length > 0) {
            const firstResult = heuristicRes.data.data[0];
            searchResults.value = heuristicRes.data.data;
            showResults.value = true;
            selectSearchResult(firstResult);

            heuristicNotice.value = "🎯 Matched nearest area: " + (firstResult.main_text || firstResult.display_name.split(',')[0] || val) + ". Drag pin to your exact building.";
            setTimeout(() => {
                heuristicNotice.value = "";
            }, 6000);
            return;
        }

        heuristicNotice.value = "⚠️ Exact landmark not found. Please click or drag the pin on the map.";
        setTimeout(() => {
            heuristicNotice.value = "";
        }, 5000);
    } catch (e) {
        console.warn("Deep search notice:", e);
    } finally {
        isSearching.value = false;
    }
}

async function selectSearchResult(item) {
    searchQuery.value = item.display_name;
    descriptiveAddress.value = item.display_name;
    showResults.value = false;
    selectedSearchIndex.value = -1;

    if (item.lat && item.lng) {
        updateLocation(item.lat, item.lng, true, item.display_name);
    } else if (item.place_id) {
        isSearching.value = true;
        try {
            const res = await axios.get("/maps/place-details", {
                params: { place_id: item.place_id },
            });
            if (res.data?.data?.lat && res.data?.data?.lng) {
                const lat = res.data.data.lat;
                const lng = res.data.data.lng;
                const addr = res.data.data.formatted_address || item.display_name;
                updateLocation(lat, lng, true, addr);
            }
        } catch (e) {
            console.warn("Place details lookup notice:", e);
        } finally {
            isSearching.value = false;
        }
    }
}

function clearSearch() {
    searchQuery.value = "";
    searchResults.value = [];
    showResults.value = false;
    selectedSearchIndex.value = -1;
}

function handleDocumentClick(e) {
    if (mapWrapperRef.value && !mapWrapperRef.value.contains(e.target)) {
        showResults.value = false;
    }
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
        },
        (error) => {
            isLocating.value = false;
            console.warn("GPS Geolocation error:", error);
            alert("Could not detect GPS location. Please check browser permissions.");
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

function loadGoogleMapsSdk(apiKey) {
    return new Promise((resolve, reject) => {
        if (window.google && window.google.maps) {
            resolve(window.google.maps);
            return;
        }

        const existingScript = document.getElementById("google-maps-sdk");
        if (existingScript) {
            existingScript.addEventListener("load", () => resolve(window.google.maps));
            existingScript.addEventListener("error", reject);
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

async function initGoogleMap(apiKey, coords) {
    try {
        await loadGoogleMapsSdk(apiKey);
        if (!mapContainer.value || !window.google || !window.google.maps) return false;

        const center = { lat: coords.lat, lng: coords.lng };

        gMap = new window.google.maps.Map(mapContainer.value, {
            center: center,
            zoom: 16,
            mapTypeId: window.google.maps.MapTypeId.ROADMAP,
            disableDefaultUI: false,
            zoomControl: true,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
        });

        gMarker = new window.google.maps.Marker({
            position: center,
            map: gMap,
            draggable: props.enableSetLocation,
            animation: window.google.maps.Animation.DROP,
        });

        if (props.enableSetLocation) {
            gMarker.addListener("dragend", () => {
                const pos = gMarker.getPosition();
                updateLocation(pos.lat(), pos.lng(), true);
            });

            gMap.addListener("click", (e) => {
                updateLocation(e.latLng.lat(), e.latLng.lng(), true);
            });
        }

        return true;
    } catch (e) {
        console.warn("Could not initialize Google Maps SDK:", e);
        return false;
    }
}

async function initMap() {
    if (!mapContainer.value) return;

    const coords = sanitizeCoords(props.latitude, props.longitude);
    currentLat.value = coords.lat;
    currentLng.value = coords.lng;
    inputLat.value = parseFloat(coords.lat.toFixed(7));
    inputLng.value = parseFloat(coords.lng.toFixed(7));

    let googleKey = null;
    try {
        const configRes = await axios.get("/maps/config");
        if (configRes.data?.data?.api_key) {
            googleKey = configRes.data.data.api_key;
        }
    } catch (e) {
        console.warn("Could not fetch maps config:", e);
    }

    if (!googleKey) {
        googleKey = import.meta.env.VITE_GOOGLE_MAPS_KEY || "";
    }

    if (googleKey) {
        await initGoogleMap(googleKey, coords);
    }

    reverseGeocodeCoords(coords.lat, coords.lng);

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
    () => [props.latitude, props.longitude],
    ([newLat, newLng]) => {
        if (newLat && newLng) {
            const parsedLat = parseFloat(newLat);
            const parsedLng = parseFloat(newLng);
            if (!isNaN(parsedLat) && !isNaN(parsedLng) && (parsedLat !== 0 || parsedLng !== 0)) {
                updateLocation(parsedLat, parsedLng, false);
                reverseGeocodeCoords(parsedLat, parsedLng);
            }
        }
    },
    { immediate: true }
);

onMounted(() => {
    initMap();
    document.addEventListener("click", handleDocumentClick);
});

onUnmounted(() => {
    document.removeEventListener("click", handleDocumentClick);
    if (currentAbortController) {
        currentAbortController.abort();
    }
    if (resizeObserver) {
        resizeObserver.disconnect();
        resizeObserver = null;
    }
    if (gMarker) {
        gMarker.setMap(null);
        gMarker = null;
    }
    gMap = null;
});
</script>
