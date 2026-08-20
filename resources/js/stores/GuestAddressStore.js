import axios from "axios";
import { defineStore } from "pinia";

export const useGuestAddress = defineStore("guestAddress", {
    state: () => ({
        name: null,
        email: null,
        phone: null,
        state: "Rajasthan",
        city: "Jaipur",
        area_id: null,
        post_code: null,
        address_line: null,
        address_type: "home",
        latitude: null,
        longitude: null,
        errors: {}
    }),
    actions: {
        clearGuestAddress() {
            this.name = null;
            this.email = null;
            this.phone = null;
            this.state = "Rajasthan";
            this.city = "Jaipur";
            this.area_id = null;
            this.post_code = null;
            this.address_line = null;
            this.latitude = null;
            this.longitude = null;
            this.errors = {};
        },
    },

});
