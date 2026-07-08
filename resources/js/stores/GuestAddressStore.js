import axios from "axios";
import { defineStore } from "pinia";

export const useGuestAddress = defineStore("guestAddress", {
    state: () => ({
        name: null,
        email: null,
        phone: null,
        area_id: null,
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
            this.area_id = null;
            this.address_line = null;
            this.latitude = null;
            this.longitude = null;
            this.errors = {};
        },
    },

});
