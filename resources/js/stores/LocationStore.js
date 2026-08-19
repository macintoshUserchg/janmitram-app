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
            return state.nearestShop ? state.nearestShop.name : "Janmitram Store";
        },

        nearestShopId: (state) => {
            return state.nearestShop ? state.nearestShop.id : null;
        },
    },

    actions: {
        /**
         * Initialize and resolve user location on application startup via IP Geolocation.
         */
        async initLocation() {
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved) {
                    const parsed = JSON.parse(saved);
                    if (parsed && parsed.city) {
                        this.city = parsed.city || this.city;
                        this.state = parsed.state || this.state;
                        this.pincode = parsed.pincode || this.pincode;
                        this.source = parsed.source || "saved";
                        this.nearestShop = parsed.nearest_shop || null;
                        this.nearbyShops = parsed.nearby_shops || [];
                    }
                }
            } catch (e) {
                console.warn("Could not read location from storage:", e);
            }

            // Fresh server-side IP resolution
            await this.resolveByIp();
        },

        /**
         * Automated IP Geolocation resolution to match nearby city shops
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
                console.warn("IP Geolocation notice:", error);
            } finally {
                this.isResolving = false;
            }
        },

        /**
         * Manual PIN code or City change
         */
        async resolveByPincode(pincode) {
            if (!pincode || pincode.trim().length < 4) {
                toast.error("Please enter a valid PIN code.");
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
                    toast.success(`Location set to ${data.city} (${pincode})`);
                    this.showLocationModal = false;
                    return true;
                }
            } catch (error) {
                toast.error(error.response?.data?.message || "Could not resolve PIN code.");
                return false;
            } finally {
                this.isResolving = false;
            }
        },

        /**
         * Set custom manual shop directly from dropdown
         */
        selectShopDirectly(shop) {
            if (shop) {
                this.nearestShop = shop;
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
