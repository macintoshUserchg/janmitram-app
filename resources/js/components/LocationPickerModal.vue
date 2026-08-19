<template>
    <TransitionRoot as="template" :show="locationStore.showLocationModal">
        <Dialog as="div" class="relative z-50" @close="locationStore.showLocationModal = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100"
                leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-50 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild as="template" enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel
                            class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-lg p-6">
                            
                            <!-- Header -->
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-primary">
                                        <MapPinIcon class="w-5 h-5 text-primary" />
                                    </div>
                                    <DialogTitle as="h3" class="text-lg font-bold text-slate-900">
                                        {{ $t('Delivery Location & Store') }}
                                    </DialogTitle>
                                </div>
                                <button class="p-1.5 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition"
                                    @click="locationStore.showLocationModal = false">
                                    <XMarkIcon class="w-5 h-5" />
                                </button>
                            </div>

                            <!-- Current Resolved Location Card -->
                            <div class="mt-4 p-4 rounded-2xl bg-gradient-to-br from-emerald-50/70 to-teal-50/40 border border-emerald-100 flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold uppercase tracking-wider text-emerald-800">
                                            {{ $t('Current Location') }}
                                        </span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-medium bg-emerald-100 text-emerald-800">
                                            {{ locationStore.source === 'ip' ? 'Auto-Detected (IP)' : 'Selected Location' }}
                                        </span>
                                    </div>
                                    <p class="text-base font-bold text-slate-900 mt-1">
                                        {{ locationStore.city }}, {{ locationStore.state }} <span v-if="locationStore.pincode">({{ locationStore.pincode }})</span>
                                    </p>
                                    <p v-if="locationStore.nearestShop" class="text-xs text-slate-600 mt-1 flex items-center gap-1.5">
                                        <span>🏬 {{ $t('Fulfilling Store') }}:</span>
                                        <strong class="text-slate-900">{{ locationStore.nearestShop.name }}</strong>
                                    </p>
                                </div>
                            </div>

                            <!-- Pincode Search Input -->
                            <div class="mt-5">
                                <label class="text-xs font-semibold text-slate-700 block mb-1.5">
                                    {{ $t('Enter Your Postal PIN Code') }}
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="text" v-model="pincodeInput"
                                        maxlength="6"
                                        :placeholder="$t('e.g. 302013 or 400053')"
                                        @keyup.enter="handlePincodeSubmit"
                                        class="form-input rounded-xl border border-slate-200 focus:border-primary px-3.5 py-2.5 text-sm w-full outline-none"
                                    />
                                    <button @click="handlePincodeSubmit"
                                        :disabled="locationStore.isResolving"
                                        class="px-4 py-2.5 bg-primary hover:bg-primary-600 disabled:opacity-50 text-white rounded-xl text-sm font-semibold transition shrink-0">
                                        {{ locationStore.isResolving ? $t('Checking...') : $t('Apply') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Stores in Vicinity -->
                            <div v-if="locationStore.nearbyShops.length > 0" class="mt-5 pt-3 border-t border-slate-100">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">
                                    {{ $t('Stores Available in Your Vicinity') }}
                                </span>
                                <div class="max-h-52 overflow-y-auto divide-y divide-slate-100 pr-1">
                                    <div v-for="shop in locationStore.nearbyShops" :key="shop.id"
                                        @click="locationStore.selectShopDirectly(shop)"
                                        class="py-2.5 px-2 flex items-center justify-between hover:bg-slate-50 rounded-xl cursor-pointer transition">
                                        <div class="min-w-0 pr-2">
                                            <p class="text-xs sm:text-sm font-bold text-slate-900 truncate">{{ shop.name }}</p>
                                            <p class="text-[11px] text-slate-500 truncate">{{ shop.address }}</p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-xs font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">{{ shop.estimated_delivery_time || 'Available' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { MapPinIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';
import { useLocationStore } from '../stores/LocationStore';

const locationStore = useLocationStore();
const pincodeInput = ref('');

const handlePincodeSubmit = async () => {
    if (pincodeInput.value) {
        await locationStore.resolveByPincode(pincodeInput.value);
        pincodeInput.value = '';
    }
};
</script>
