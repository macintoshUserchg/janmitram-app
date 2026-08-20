<template>
    <div class="p-6 bg-white rounded-2xl border border-slate-200 mt-3">
        <form>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="form-label mb-2">
                        {{ $t("Name") }}
                        <small class="text-red-500">*</small>
                    </label>
                    <input
                        type="text"
                        id="name"
                        v-model="guestAddressStore.name"
                        :placeholder="$t('Enter name')"
                        class="form-input"
                        :class="
                            guestAddressStore.errors &&
                            guestAddressStore.errors?.name
                                ? 'border-red-500'
                                : 'border-slate-200'
                        "
                    />
                    <span
                        v-if="
                            guestAddressStore.errors &&
                            guestAddressStore.errors?.name
                        "
                        class="text-red-500 text-sm"
                        >{{ guestAddressStore.errors?.name[0] }}</span
                    >
                </div>
                <div>
                    <label for="email" class="form-label mb-2">
                        {{ $t("Email") }}
                        <small class="text-red-500">*</small>
                    </label>
                    <input
                        type="email"
                        id="email"
                        v-model="guestAddressStore.email"
                        :placeholder="$t('Enter email')"
                        class="form-input"
                        :class="
                            guestAddressStore.errors &&
                            guestAddressStore.errors?.email
                                ? 'border-red-500'
                                : 'border-slate-200'
                        "
                    />
                    <span
                        v-if="
                            guestAddressStore.errors &&
                            guestAddressStore.errors?.email
                        "
                        class="text-red-500 text-sm"
                        >{{ guestAddressStore.errors?.email[0] }}</span
                    >
                </div>
                <div>
                    <label for="Phone" class="form-label mb-2">
                        {{ $t("Phone") }}
                        <small class="text-red-500">*</small>
                    </label>
                    <input
                        type="text"
                        id="Phone"
                        :placeholder="$t('Enter phone')"
                        value="0123456789"
                        class="form-input"
                        v-model="guestAddressStore.phone"
                        :class="
                            guestAddressStore.errors &&
                            guestAddressStore.errors?.phone
                                ? 'border-red-500'
                                : 'border-slate-200'
                        "
                        :maxlength="masterStore.phoneMaxLength"
                        @input="
                            guestAddressStore.phone =
                                guestAddressStore.phone.replace(/[^\d]/g, '')
                        "
                    />
                    <span
                        v-if="
                            guestAddressStore.errors &&
                            guestAddressStore.errors?.phone
                        "
                        class="text-red-500 text-sm"
                        >{{ guestAddressStore.errors?.phone[0] }}</span
                    >
                </div>
            </div>

            <div class="mt-6">
                <MapDisplay
                    :enableSetLocation="true"
                    @location-updated="updateLocation"
                />
            </div>

            <!-- State, City, PIN Code Dependent Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mt-6">
                <!-- State -->
                <div>
                    <label for="GuestState" class="form-label mb-2">
                        {{ $t("State") }}
                        <small class="text-red-500">*</small>
                    </label>
                    <select
                        id="GuestState"
                        v-model="guestAddressStore.state"
                        @change="onStateChange"
                        :class="[
                            'form-input',
                            guestAddressStore.errors &&
                            guestAddressStore.errors?.state
                                ? 'border-red-500'
                                : 'border-slate-200',
                        ]"
                    >
                        <option value="" disabled>{{ $t("Select State") }}</option>
                        <option v-for="state in INDIAN_STATES" :key="state" :value="state">
                            {{ state }}
                        </option>
                    </select>
                    <span
                        v-if="
                            guestAddressStore.errors &&
                            guestAddressStore.errors?.state
                        "
                        class="text-red-500 text-sm"
                        >{{ guestAddressStore.errors?.state[0] }}</span
                    >
                </div>

                <!-- City (Dependent on State) -->
                <div>
                    <label for="GuestCity" class="form-label mb-2">
                        {{ $t("City") }}
                        <small class="text-red-500">*</small>
                    </label>
                    <select
                        id="GuestCity"
                        v-model="guestAddressStore.city"
                        :class="[
                            'form-input',
                            guestAddressStore.errors &&
                            guestAddressStore.errors?.city
                                ? 'border-red-500'
                                : 'border-slate-200',
                        ]"
                    >
                        <option value="" disabled>{{ guestAddressStore.state ? $t("Select City") : $t("Select State first") }}</option>
                        <option v-for="city in availableCities" :key="city" :value="city">
                            {{ city }}
                        </option>
                        <option value="Other">{{ $t("Other (Type City)") }}</option>
                    </select>
                    <input
                        v-if="guestAddressStore.city === 'Other' || isCustomCity"
                        type="text"
                        v-model="customCityName"
                        @input="guestAddressStore.city = customCityName"
                        :placeholder="$t('Type city name')"
                        class="form-input mt-2"
                    />
                    <span
                        v-if="
                            guestAddressStore.errors &&
                            guestAddressStore.errors?.city
                        "
                        class="text-red-500 text-sm"
                        >{{ guestAddressStore.errors?.city[0] }}</span
                    >
                </div>

                <!-- Postal / PIN Code -->
                <div>
                    <label for="guest_post_code" class="form-label mb-2">
                        {{ $t("PIN Code") }}
                    </label>
                    <input
                        type="text"
                        id="guest_post_code"
                        v-model="guestAddressStore.post_code"
                        :placeholder="$t('Enter 6-digit PIN')"
                        maxlength="6"
                        @input="guestAddressStore.post_code = (guestAddressStore.post_code || '').replace(/[^\d]/g, '')"
                        class="form-input"
                        :class="[
                            guestAddressStore.errors &&
                            guestAddressStore.errors?.post_code
                                ? 'border-red-500'
                                : 'border-slate-200',
                        ]"
                    />
                    <span
                        v-if="
                            guestAddressStore.errors &&
                            guestAddressStore.errors?.post_code
                        "
                        class="text-red-500 text-sm"
                        >{{ guestAddressStore.errors?.post_code[0] }}</span
                    >
                </div>
            </div>

            <!-- Address Line -->
            <div class="mt-4">
                <label for="address" class="form-label mb-2">
                    {{ $t("House No. / Flat / Building / Street Address") }}
                    <small class="text-red-500">*</small>
                </label>
                <input
                    type="text"
                    id="address"
                    v-model="guestAddressStore.address_line"
                    :placeholder="$t('Enter house no., building, street, landmark...')"
                    class="form-input"
                    :class="
                        guestAddressStore.errors &&
                        guestAddressStore.errors?.address_line
                            ? 'border-red-500'
                            : 'border-slate-200'
                    "
                />
                <span
                    v-if="
                        guestAddressStore.errors &&
                        guestAddressStore.errors?.address_line
                    "
                    class="text-red-500 text-sm"
                    >{{
                        guestAddressStore.errors?.address_line[0]
                    }}</span
                >
            </div>

            <div class="mt-4">
                <div
                    class="text-slate-950 text-base font-medium leading-normal"
                >
                    {{ $t("Address Tag") }}
                </div>

                <div
                    class="flex justify-between items-center gap-2 mt-2 flex-wrap"
                >
                    <div class="flex items-center flex-wrap gap-2">
                        <label
                            for="home"
                            class="px-3 py-2 bg-white rounded-[42px] border flex gap-2 items-center text-slate-600 text-base font-normal leading-normal cursor-pointer has-[:checked]:border-primary has-[:checked]:text-primary"
                        >
                            <input
                                type="radio"
                                id="home"
                                v-model="guestAddressStore.address_type"
                                name="tag"
                                value="home"
                                class="radio-btn"
                                :checked="
                                    guestAddressStore.address_type === 'home'
                                "
                            />
                            <span class="text-base font-normal">{{
                                $t("HOME")
                            }}</span>
                        </label>

                        <label
                            for="office"
                            class="px-3 py-2 bg-white rounded-[42px] border flex gap-2 items-center text-slate-600 text-base font-normal leading-normal cursor-pointer has-[:checked]:border-primary has-[:checked]:text-primary"
                        >
                            <input
                                type="radio"
                                id="office"
                                v-model="guestAddressStore.address_type"
                                name="tag"
                                value="office"
                                class="radio-btn"
                                :checked="
                                    guestAddressStore.address_type === 'office'
                                "
                            />
                            <span class="text-base font-normal">{{
                                $t("OFFICE")
                            }}</span>
                        </label>

                        <label
                            for="other"
                            class="px-3 py-2 bg-white rounded-[42px] border flex gap-2 items-center text-slate-600 text-base font-normal leading-normal cursor-pointer has-[:checked]:border-primary has-[:checked]:text-primary"
                        >
                            <input
                                type="radio"
                                id="other"
                                v-model="guestAddressStore.address_type"
                                name="tag"
                                value="other"
                                class="radio-btn"
                                :checked="
                                    guestAddressStore.address_type === 'other'
                                "
                            />
                            <span class="text-base font-normal">{{
                                $t("OTHER")
                            }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import axios from "axios";
import { ref, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import { useAuth } from "../stores/AuthStore";
import { useGuestAddress } from "../stores/GuestAddressStore";
import ToastSuccessMessage from "./ToastSuccessMessage.vue";
import LoadingSpin from "./LoadingSpin.vue";

import { useMaster } from "../stores/MasterStore";
import MapDisplay from "./MapDisplay.vue";
import { useBasketStore } from "../stores/BasketStore";
import {
    INDIAN_STATES,
    getCitiesForState,
} from "../data/indiaStatesCities";

const masterStore = useMaster();
const guestAddressStore = useGuestAddress();
const basketStore = useBasketStore();

const toast = useToast();
const route = useRoute();
const router = useRouter();
const authStore = useAuth();

const customCityName = ref("");
const isCustomCity = ref(false);

const availableCities = computed(() => {
    return getCitiesForState(guestAddressStore.state);
});

const onStateChange = () => {
    const cities = availableCities.value;
    if (cities.length > 0) {
        if (!cities.includes(guestAddressStore.city)) {
            guestAddressStore.city = cities[0];
            customCityName.value = "";
            isCustomCity.value = false;
        }
    } else {
        guestAddressStore.city = "";
    }
};

const updateLocation = (coords) => {
    guestAddressStore.latitude = coords.lat;
    guestAddressStore.longitude = coords.lng;

    if (coords.address) {
        // Auto-detect PIN code
        const pinMatch = coords.address.match(/\b\d{6}\b/);
        if (pinMatch && !guestAddressStore.post_code) {
            guestAddressStore.post_code = pinMatch[0];
        }

        // Auto-detect State & City from reverse geocode address
        for (const state of INDIAN_STATES) {
            const stateRegex = new RegExp(`\\b${state}\\b`, "i");
            if (stateRegex.test(coords.address)) {
                guestAddressStore.state = state;
                const cities = getCitiesForState(state);
                for (const city of cities) {
                    const cityRegex = new RegExp(`\\b${city}\\b`, "i");
                    if (cityRegex.test(coords.address)) {
                        guestAddressStore.city = city;
                        isCustomCity.value = false;
                        break;
                    }
                }
                break;
            }
        }
    }
};
</script>

<style scoped>
.form-label {
    @apply text-slate-700 text-base font-normal leading-normal;
}

.form-input {
    @apply p-3 rounded-lg border focus:border-primary w-full outline-none text-base font-normal leading-normal placeholder:text-slate-400;
}

.formInputCoupon {
    @apply rounded-lg border border-slate-200 focus:border-primary w-full outline-none text-base font-normal leading-normal placeholder:text-slate-400;
}

.radio-btn {
    @apply w-4 h-4 border appearance-none border-slate-300 rounded-full checked:bg-primary ring-primary checked:outline-1 outline-offset-1 checked:outline-primary checked:outline transition duration-100 ease-in-out m-0;
}

.radioBtn2 {
    @apply w-4 h-4 border appearance-none border-slate-300 rounded-full checked:bg-primary ring-primary checked:outline-1 outline-offset-1 checked:outline-primary checked:outline transition duration-100 ease-in-out m-0;
}
</style>
