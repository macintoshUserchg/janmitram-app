import axios from "axios";
import { defineStore } from "pinia";
import { useToast } from "vue-toastification";

const toast = useToast();
const STORAGE_KEY = "janmitram_user_location";

export const useLocationStore = defineStore("locationStore", {
    state: () => ({
        city: "Jaipur",
        state: "Rajasthan",
        pincode: "302013",
        latitude: 26.9985869,
        longitude: 75.7680702,
        source: "ip",
        nearestShop: null,
        nearbyShops: [],
        isResolving: false,
        showLocationModal: false,
    }),

    getters: {
        currentLocationLabel: (state) => {
            if (state.city && state.pincode) {
                return `${state.city} (${state.pincode})`;
            }
            return state.city || "Select Location";
        },

        nearestShopName: (state) => {
            return state.nearestShop ? state.nearestShop.name : "Central Warehouse";
        },

        nearestShopDistance: (state) => {
            if (!state.nearestShop) return null;
            return state.nearestShop.distance_km;
        },

        nearestShopId: (state) => {
            return state.nearestShop ? state.nearestShop.id : null;
        },
    },

    actions: {
        /**
         * Initialize and resolve user location on application startup.
         * Priority 1: Automated IP Geolocation (Zero Permission Required)
         */
        async initLocation() {
            // Check if user previously refined location in localStorage
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved) {
                    const parsed = JSON.parse(saved);
                    if (parsed && parsed.latitude && parsed.longitude) {
                        this.city = parsed.city || this.city;
                        this.state = parsed.state || this.state;
                        this.pincode = parsed.pincode || this.pincode;
                        this.latitude = parsed.latitude;
                        this.longitude = parsed.longitude;
                        this.source = parsed.source || "saved";
                        this.nearestShop = parsed.nearest_shop || null;
                        this.nearbyShops = parsed.nearby_shops || [];
                    }
                }
            } catch (e) {
                console.warn("Could not read location from storage:", e);
            }

            // Always perform fast server-side IP resolution to ensure freshness
            await this.resolveByIp();
        },

        /**
         * 1st Priority: Automated IP Geolocation resolution
         */
        async resolveByIp() {
            this.isResolving = true;
            try {
                const response = await axios.get("/location/resolve");
                const data = response.data?.data;
                if (data) {
                    this.applyLocationData(data);
                }
            } catch (error) {
                console.warn("IP Geolocation resolution notice:", error);
            } finally {
                this.isResolving = false;
            }
        },

        /**
         * 2nd Priority: GPS Geolocation Refinement (When user requests high precision)
         */
        detectGPSLocation() {
            if (!navigator.geolocation) {
                toast.error("Geolocation is not supported by your browser.");
                return;
            }

            this.isResolving = true;
            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    try {
                        const response = await axios.get("/location/resolve", {
                            params: {
                                latitude: lat,
                                longitude: lng,
                                source: "gps",
                            },
                        });
                        const data = response.data?.data;
                        if (data) {
                            this.applyLocationData(data);
                            toast.success(`Location updated to ${this.city} via GPS`);
                        }
                    } catch (error) {
                        toast.error("Could not resolve GPS location details.");
                    } finally {
                        this.isResolving = false;
                    }
                },
                (error) => {
                    this.isResolving = false;
                    console.warn("GPS error:", error);
                    toast.warning("GPS access denied or unavailable. Using IP location.");
                },
                { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 }
            );
        },

        /**
         * Manual Override: Resolve location and nearest shop by postal Pin-code
         */
        async resolveByPincode(pincode) {
            if (!pincode || pincode.trim().length < 4) {
                toast.error("Please enter a valid 6-digit Pincode.");
                return false;
            }

            this.isResolving = true;
            try {
                const response = await axios.get("/location/by-pincode", {
                    params: { pincode: pincode.trim() },
                });
                const data = response.data?.data;
                if (data) {
                    this.applyLocationData(data);
                    toast.success(`Location updated to ${data.city} (${pincode})`);
                    this.showLocationModal = false;
                    return true;
                }
            } catch (error) {
                toast.error(error.response?.data?.message || "Could not resolve pincode.");
                return false;
            } finally {
                this.isResolving = false;
            }
        },

        /**
         * Set custom manual shop or location coordinates
         */
        selectShopDirectly(shop) {
            if (shop) {
                this.nearestShop = shop;
                this.latitude = shop.latitude;
                this.longitude = shop.longitude;
                this.city = shop.name;
                this.source = "manual_shop";
                this.saveToStorage();
                toast.success(`Serving products from ${shop.name}`);
                this.showLocationModal = false;
            }
        },

        applyLocationData(data) {
            this.city = data.city || this.city;
            this.state = data.state || this.state;
            this.pincode = data.pincode || this.pincode;
            this.latitude = data.latitude || this.latitude;
            this.longitude = data.longitude || this.longitude;
            this.source = data.source || this.source;
            this.nearestShop = data.nearest_shop || null;
            this.nearbyShops = data.nearby_shops || [];
            this.saveToStorage();
        },

        saveToStorage() {
            try {
                localStorage.setItem(
                    STORAGE_KEY,
                    JSON.stringify({
                        city: this.city,
                        state: this.state,
                        pincode: this.pincode,
                        latitude: this.latitude,
                        longitude: this.longitude,
                        source: this.source,
                        nearest_shop: this.nearestShop,
                        nearby_shops: this.nearbyShops,
                    })
                );
            } catch (e) {
                // Ignore storage quota errors
            }
        },
    },
});
