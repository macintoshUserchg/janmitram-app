<template>
    <div>
        <TransitionRoot as="template" :show="authStore.showAddressModal">
            <Dialog
                as="div"
                class="relative z-10"
                @close="authStore.showAddressModal = false"
            >
                <TransitionChild
                    as="template"
                    enter="ease-out duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="ease-in duration-200"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div
                        class="fixed inset-0 bg-gray-500 bg-opacity-50 transition-opacity"
                    />
                </TransitionChild>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div
                        class="flex min-h-full items-center justify-center p-4 text-center sm:p-0"
                    >
                        <TransitionChild
                            as="template"
                            enter="ease-out duration-300"
                            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to="opacity-100 translate-y-0 sm:scale-100"
                            leave="ease-in duration-200"
                            leave-from="opacity-100 translate-y-0 sm:scale-100"
                            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        >
                            <DialogPanel
                                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all my-8 md:my-0 w-full md:max-w-3xl"
                            >
                                <div class="bg-white p-5 sm:p-8 relative">
                                    <!-- close button -->
                                    <div
                                        class="w-9 h-9 bg-slate-100 rounded-[32px] absolute top-4 right-4 flex justify-center items-center cursor-pointer"
                                        @click="
                                            authStore.showAddressModal = false
                                        "
                                    >
                                        <XMarkIcon
                                            class="w-6 h-6 text-slate-600"
                                        />
                                    </div>
                                    <!-- end close button -->

                                    <div
                                        class="text-slate-950 text-2xl font-medium"
                                    >
                                        {{ $t("New Address") }}
                                    </div>

                                    <form
                                        @submit.prevent="addressFormSubmit()"
                                        class="mt-4"
                                    >
                                        <div
                                            class="grid grid-cols-1 sm:grid-cols-2 gap-6"
                                        >
                                            <div>
                                                <label
                                                    for="name"
                                                    class="form-label mb-2"
                                                >
                                                    {{ $t("Name") }}
                                                    <small class="text-red-500"
                                                        >*</small
                                                    >
                                                </label>
                                                <input
                                                    type="text"
                                                    id="name"
                                                    v-model="formData.name"
                                                    :placeholder="
                                                        $t('Enter name')
                                                    "
                                                    class="form-input"
                                                    :class="
                                                        errors && errors?.name
                                                            ? 'border-red-500'
                                                            : 'border-slate-200'
                                                    "
                                                />
                                                <span
                                                    v-if="
                                                        errors && errors?.name
                                                    "
                                                    class="text-red-500 text-sm"
                                                    >{{ errors?.name[0] }}</span
                                                >
                                            </div>
                                            <div>
                                                <label
                                                    for="Phone"
                                                    class="form-label mb-2"
                                                >
                                                    {{ $t("Phone") }}
                                                    <small class="text-red-500"
                                                        >*</small
                                                    >
                                                </label>
                                                <input
                                                    type="text"
                                                    id="Phone"
                                                    :placeholder="
                                                        $t('Enter phone')
                                                    "
                                                    value="0123456789"
                                                    class="form-input"
                                                    v-model="formData.phone"
                                                    :class="
                                                        errors && errors?.phone
                                                            ? 'border-red-500'
                                                            : 'border-slate-200'
                                                    "
                                                    :maxlength="
                                                        masterStore.phoneMaxLength
                                                    "
                                                    @input="
                                                        formData.phone =
                                                            formData.phone.replace(
                                                                /[^\d]/g,
                                                                ''
                                                            )
                                                    "
                                                />
                                                <span
                                                    v-if="
                                                        errors && errors?.phone
                                                    "
                                                    class="text-red-500 text-sm"
                                                    >{{
                                                        errors?.phone[0]
                                                    }}</span
                                                >
                                            </div>
                                        </div>

                                        <!-- <div
                                            class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6"
                                        >
                                            <div>
                                                <label
                                                    for="Area"
                                                    class="form-label mb-2"
                                                >
                                                    {{ $t("Area") }}</label
                                                >
                                                <input
                                                    type="text"
                                                    id="Area"
                                                    :placeholder="
                                                        $t('Enter Area')
                                                    "
                                                    class="form-input"
                                                    v-model="formData.area"
                                                    :class="
                                                        errors && errors?.area
                                                            ? 'border-red-500'
                                                            : 'border-slate-200'
                                                    "
                                                />
                                                <span
                                                    v-if="
                                                        errors && errors?.area
                                                    "
                                                    class="text-red-500 text-sm"
                                                    >{{ errors?.area[0] }}</span
                                                >
                                            </div>
                                            <div>
                                                <label
                                                    for="Flat"
                                                    class="form-label mb-2"
                                                >
                                                    {{ $t("Flat") }}</label
                                                >
                                                <input
                                                    type="text"
                                                    id="Flat"
                                                    :placeholder="
                                                        $t('Enter Flat no')
                                                    "
                                                    value=""
                                                    class="form-input"
                                                    v-model="formData.flat_no"
                                                    :class="
                                                        errors &&
                                                        errors?.flat_no
                                                            ? 'border-red-500'
                                                            : 'border-slate-200'
                                                    "
                                                />
                                                <span
                                                    v-if="
                                                        errors &&
                                                        errors?.flat_no
                                                    "
                                                    class="text-red-500 text-sm"
                                                    >{{
                                                        errors?.flat_no[0]
                                                    }}</span
                                                >
                                            </div>

                                            <div>
                                                <label
                                                    for="Postal"
                                                    class="form-label mb-2"
                                                >
                                                    {{ $t("Postal Code") }}
                                                </label>
                                                <input
                                                    type="text"
                                                    id="Postal"
                                                    v-model="formData.post_code"
                                                    :placeholder="
                                                        $t('Enter Postal Code')
                                                    "
                                                    value=""
                                                    class="form-input"
                                                    :class="
                                                        errors &&
                                                        errors?.post_code
                                                            ? 'border-red-500'
                                                            : 'border-slate-200'
                                                    "
                                                />
                                                <span
                                                    v-if="
                                                        errors &&
                                                        errors?.post_code
                                                    "
                                                    class="text-red-500 text-sm"
                                                    >{{
                                                        errors?.post_code[0]
                                                    }}</span
                                                >
                                            </div>
                                        </div> -->

                                        <div class="mt-6">
                                            <MapDisplay
                                                :enableSetLocation="true"
                                                @location-updated="
                                                    updateLocation
                                                "
                                            />
                                        </div>

                                        <!-- State, City, PIN Code Dependent Grid -->
                                        <div
                                            class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mt-6"
                                        >
                                            <!-- State -->
                                            <div>
                                                <label
                                                    for="StateModal"
                                                    class="form-label mb-2"
                                                >
                                                    {{ $t("State") }}
                                                    <small class="text-red-500">*</small>
                                                </label>
                                                <select
                                                    id="StateModal"
                                                    v-model="formData.state"
                                                    @change="onStateChange"
                                                    :class="[
                                                        'form-input',
                                                        errors && errors?.state
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
                                                    v-if="errors && errors?.state"
                                                    class="text-red-500 text-sm"
                                                    >{{ errors?.state[0] }}</span
                                                >
                                            </div>

                                            <!-- City (Dependent on State) -->
                                            <div>
                                                <label
                                                    for="CityModal"
                                                    class="form-label mb-2"
                                                >
                                                    {{ $t("City") }}
                                                    <small class="text-red-500">*</small>
                                                </label>
                                                <select
                                                    id="CityModal"
                                                    v-model="formData.city"
                                                    :class="[
                                                        'form-input',
                                                        errors && errors?.city
                                                            ? 'border-red-500'
                                                            : 'border-slate-200',
                                                    ]"
                                                >
                                                    <option value="" disabled>{{ formData.state ? $t("Select City") : $t("Select State first") }}</option>
                                                    <option v-for="city in availableCities" :key="city" :value="city">
                                                        {{ city }}
                                                    </option>
                                                    <option value="Other">{{ $t("Other (Type City)") }}</option>
                                                </select>
                                                <input
                                                    v-if="formData.city === 'Other' || isCustomCity"
                                                    type="text"
                                                    v-model="customCityName"
                                                    @input="formData.city = customCityName"
                                                    :placeholder="$t('Type city name')"
                                                    class="form-input mt-2"
                                                />
                                                <span
                                                    v-if="errors && errors?.city"
                                                    class="text-red-500 text-sm"
                                                    >{{ errors?.city[0] }}</span
                                                >
                                            </div>

                                            <!-- Postal / PIN Code -->
                                            <div>
                                                <label for="post_code_modal" class="form-label mb-2">
                                                    {{ $t("PIN Code") }}
                                                </label>
                                                <input
                                                    type="text"
                                                    id="post_code_modal"
                                                    v-model="formData.post_code"
                                                    :placeholder="$t('Enter 6-digit PIN')"
                                                    maxlength="6"
                                                    @input="formData.post_code = formData.post_code.replace(/[^\d]/g, '')"
                                                    class="form-input"
                                                    :class="[
                                                        errors && errors?.post_code
                                                            ? 'border-red-500'
                                                            : 'border-slate-200',
                                                    ]"
                                                />
                                                <span
                                                    v-if="errors && errors?.post_code"
                                                    class="text-red-500 text-sm"
                                                    >{{ errors?.post_code[0] }}</span
                                                >
                                            </div>
                                        </div>

                                        <!-- Address Line -->
                                        <div class="mt-4">
                                            <label
                                                for="address"
                                                class="form-label mb-2"
                                            >
                                                {{ $t("House No. / Flat / Building / Street Address") }}
                                                <small class="text-red-500">*</small>
                                            </label>
                                            <input
                                                type="text"
                                                id="address"
                                                v-model="formData.address_line"
                                                :placeholder="$t('Enter house no., building, street, landmark...')"
                                                class="form-input"
                                                :class="
                                                    errors && errors?.address_line
                                                        ? 'border-red-500'
                                                        : 'border-slate-200'
                                                "
                                            />
                                            <span
                                                v-if="errors && errors?.address_line"
                                                class="text-red-500 text-sm"
                                                >{{ errors?.address_line[0] }}</span
                                            >
                                        </div>

                                        <div class="mt-5">
                                            <div
                                                class="text-slate-950 text-base font-medium leading-normal"
                                            >
                                                {{ $t("Address Tag") }}
                                            </div>

                                            <div
                                                class="flex justify-between items-center gap-2 mt-3 flex-wrap"
                                            >
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <label
                                                        for="home"
                                                        class="px-3 py-2 bg-white rounded-[42px] border flex gap-2 items-center text-slate-600 text-base font-normal leading-normal cursor-pointer has-[:checked]:border-primary has-[:checked]:text-primary"
                                                    >
                                                        <input
                                                            type="radio"
                                                            id="home"
                                                            v-model="
                                                                formData.address_type
                                                            "
                                                            name="tag"
                                                            value="home"
                                                            class="radio-btn"
                                                            :checked="
                                                                formData.address_type ===
                                                                'home'
                                                            "
                                                        />
                                                        <span
                                                            class="text-base font-normal"
                                                        >
                                                            {{
                                                                $t("HOME")
                                                            }}</span
                                                        >
                                                    </label>

                                                    <label
                                                        for="office"
                                                        class="px-3 py-2 bg-white rounded-[42px] border flex gap-2 items-center text-slate-600 text-base font-normal leading-normal cursor-pointer has-[:checked]:border-primary has-[:checked]:text-primary"
                                                    >
                                                        <input
                                                            type="radio"
                                                            id="office"
                                                            v-model="
                                                                formData.address_type
                                                            "
                                                            name="tag"
                                                            value="office"
                                                            class="radio-btn"
                                                            :checked="
                                                                formData.address_type ===
                                                                'office'
                                                            "
                                                        />
                                                        <span
                                                            class="text-base font-normal"
                                                            >{{
                                                                $t("OFFICE")
                                                            }}</span
                                                        >
                                                    </label>

                                                    <label
                                                        for="other"
                                                        class="px-3 py-2 bg-white rounded-[42px] border flex gap-2 items-center text-slate-600 text-base font-normal leading-normal cursor-pointer has-[:checked]:border-primary has-[:checked]:text-primary"
                                                    >
                                                        <input
                                                            type="radio"
                                                            id="other"
                                                            v-model="
                                                                formData.address_type
                                                            "
                                                            name="tag"
                                                            value="other"
                                                            class="radio-btn"
                                                            :checked="
                                                                formData.address_type ===
                                                                'other'
                                                            "
                                                        />
                                                        <span
                                                            class="text-base font-normal"
                                                            >{{
                                                                $t("OTHER")
                                                            }}</span
                                                        >
                                                    </label>
                                                </div>
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <input
                                                        id="default"
                                                        v-model="
                                                            formData.is_default
                                                        "
                                                        name="default"
                                                        type="checkbox"
                                                        class="w-4 h-4"
                                                        :checked="
                                                            formData.is_default
                                                        "
                                                    />
                                                    <label
                                                        for="default"
                                                        class="text-slate-500 text-sm font-normal leading-tight m-0"
                                                    >
                                                        {{
                                                            $t(
                                                                "Make it default address"
                                                            )
                                                        }}
                                                    </label>
                                                </div>

                                                <button
                                                    type="submit"
                                                    class="px-8 py-2 bg-primary text-white rounded-[42px]"
                                                >
                                                    {{ $t("Submit") }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script setup>
import {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import { XMarkIcon } from "@heroicons/vue/24/solid";
import { ref, computed } from "vue";

import { useAuth } from "../stores/AuthStore";
import { useToast } from "vue-toastification";
import { useMaster } from "../stores/MasterStore";
import MapDisplay from "./MapDisplay.vue";
import {
    INDIAN_STATES,
    getCitiesForState,
} from "../data/indiaStatesCities";

const masterStore = useMaster();
const toast = useToast();
const authStore = useAuth();

const customCityName = ref("");
const isCustomCity = ref(false);

const formData = ref({
    name: "",
    phone: "",
    state: "Rajasthan",
    city: "Jaipur",
    area_id: "",
    flat_no: "",
    post_code: "",
    address_line: "",
    address_line2: "",
    address_type: "home",
    latitude: "",
    longitude: "",
    is_default: false,
});

const availableCities = computed(() => {
    return getCitiesForState(formData.value.state);
});

const onStateChange = () => {
    const cities = availableCities.value;
    if (cities.length > 0) {
        if (!cities.includes(formData.value.city)) {
            formData.value.city = cities[0];
            customCityName.value = "";
            isCustomCity.value = false;
        }
    } else {
        formData.value.city = "";
    }
};

const errors = ref({});

const addressFormSubmit = () => {
    axios
        .post("/address/store", formData.value, {
            headers: {
                Authorization: authStore.token,
            },
        })
        .then((response) => {
            toast.success(response.data.message, {
                position:
                    masterStore.langDirection === "rtl"
                        ? "bottom-right"
                        : "bottom-left",
            });
            formData.value = {
                name: "",
                phone: "",
                state: "Rajasthan",
                city: "Jaipur",
                address_type: "home",
                address_line: "",
                post_code: "",
            };
            authStore.fetchAddresses();
            authStore.showAddressModal = false;
            authStore.showChangeAddressModal = true;
        })
        .catch((error) => {
            errors.value = error.response.data.errors;
            toast.error(error.response.data.message, {
                position:
                    masterStore.langDirection === "rtl"
                        ? "bottom-right"
                        : "bottom-left",
            });
        });
};

const updateLocation = (coords) => {
    formData.value.latitude = coords.lat;
    formData.value.longitude = coords.lng;

    if (coords.address) {
        // Auto-detect PIN code
        const pinMatch = coords.address.match(/\b\d{6}\b/);
        if (pinMatch && !formData.value.post_code) {
            formData.value.post_code = pinMatch[0];
        }

        // Auto-detect State & City from reverse geocode address
        for (const state of INDIAN_STATES) {
            const stateRegex = new RegExp(`\\b${state}\\b`, "i");
            if (stateRegex.test(coords.address)) {
                formData.value.state = state;
                const cities = getCitiesForState(state);
                for (const city of cities) {
                    const cityRegex = new RegExp(`\\b${city}\\b`, "i");
                    if (cityRegex.test(coords.address)) {
                        formData.value.city = city;
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
    @apply w-5 h-5 border appearance-none border-slate-300 rounded-full checked:bg-primary ring-primary checked:outline-1 outline-offset-1 checked:outline-primary checked:outline transition duration-100 ease-in-out m-0;
}

.radioBtn2 {
    @apply w-4 h-4 border appearance-none border-slate-300 rounded-full checked:bg-primary ring-primary checked:outline-1 outline-offset-1 checked:outline-primary checked:outline transition duration-100 ease-in-out m-0;
}
</style>
