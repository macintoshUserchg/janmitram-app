<template>
    <div class="main-container py-2 flex flex-col gap-2">
        <div class="flex items-center justify-between gap-3 md:gap-8 w-full">
            <div class="flex items-center gap-3 sm:gap-6 grow">
                <router-link to="/" class="w-[120px] sm:w-[150px] md:w-[170px] lg:w-[220px] shrink-0">
                    <img :src="master.logo" alt="" class="h-9 sm:h-11 object-contain">
                </router-link>

                <!-- Delivery Location Pill (Desktop) -->
                <div class="hidden xl:flex items-center gap-2.5 cursor-pointer px-3 py-1.5 rounded-2xl border border-slate-200/80 hover:border-primary/40 bg-slate-50/70 hover:bg-white transition shrink-0 shadow-xs"
                    @click="locationStore.showLocationModal = true">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-primary flex items-center justify-center">
                        <MapPinIcon class="w-4 h-4 text-primary" />
                    </div>
                    <div class="text-left leading-tight">
                        <div class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider flex items-center gap-1">
                            <span>{{ $t('Deliver to') }}</span>
                        </div>
                        <div class="text-xs font-bold text-slate-900 flex items-center gap-1">
                            <span class="max-w-[120px] truncate">{{ locationStore.currentLocationLabel }}</span>
                            <ChevronDownIcon class="w-3 h-3 text-slate-400" />
                        </div>
                    </div>
                </div>

                <!-- Desktop Search -->
                <div class="relative grow max-w-[650px] hidden md:block" ref="searchContainerRef">
                    <div class="relative flex items-center">
                        <input type="text" v-model="search" :placeholder="$t('Search products, categories...')"
                            class="px-4 py-2.5 pr-24 block rounded-2xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 w-full placeholder:text-slate-400 outline-none text-sm transition shadow-xs bg-slate-50/50 focus:bg-white"
                            @keyup.enter="searchProducts()"
                            @focus="onSearchFocus"
                        />
                        <button v-if="search" class="absolute right-14 text-slate-400 hover:text-slate-600 p-1" @click="clearSearch">
                            <XMarkIcon class="w-4 h-4" />
                        </button>
                        <button class="bg-primary hover:bg-primary-600 h-full w-12 border-none absolute right-0 top-0 rounded-r-2xl flex items-center justify-center text-white transition cursor-pointer"
                            @click="searchProducts()">
                            <MagnifyingGlassIcon class="w-5 h-5 text-white" />
                        </button>
                    </div>

                    <!-- Desktop Live Search Dropdown -->
                    <div v-if="showLiveDropdown && (searchResults.length > 0 || isSearching)"
                        class="absolute top-full left-0 right-0 mt-1.5 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50 max-h-[420px] overflow-y-auto">
                        <div v-if="isSearching" class="p-4 text-center text-slate-400 text-xs flex items-center justify-center gap-2">
                            <div class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                            <span>{{ $t('Searching...') }}</span>
                        </div>
                        <div v-else-if="searchResults.length > 0">
                            <div class="px-3 py-1.5 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                                {{ $t('Products') }}
                            </div>
                            <div v-for="item in searchResults" :key="item.id" 
                                @click="goToProduct(item.id)"
                                class="flex items-center gap-3 px-3 py-2 hover:bg-slate-50 cursor-pointer transition">
                                <img :src="item.thumbnail" class="w-10 h-10 object-contain rounded-lg border border-slate-100 bg-slate-50 p-0.5 shrink-0" />
                                <div class="grow min-w-0">
                                    <p class="text-xs font-medium text-slate-900 truncate">{{ item.name }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-xs font-bold text-primary">{{ master.showCurrency(item.discount_price > 0 ? item.discount_price : item.price) }}</span>
                                        <span v-if="item.discount_price > 0" class="text-[10px] text-slate-400 line-through">{{ master.showCurrency(item.price) }}</span>
                                        <span v-if="item.discount_percentage > 0" class="text-[10px] font-bold text-red-500 bg-red-50 px-1 rounded">{{ item.discount_percentage }}% OFF</span>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-slate-100 mt-1 pt-1.5 px-3 text-center">
                                <button @click="searchProducts()" class="text-xs font-semibold text-primary hover:underline">
                                    {{ $t('View all results') }} →
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden md:flex items-center justify-end md:gap-4 lg:gap-8">
                <div class="flex items-center md:gap-1 lg:gap-3">
                    <div class="p-2.5 cursor-pointer hover:scale-105 transition" @click="showWishlist()">
                        <div class="w-6 h-6 relative">
                            <img :src="'/assets/icons/heart.svg'" class="w-6 h-6 text-primary" />
                            <span
                                class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-xs">
                                {{ AuthStore.favoriteProducts }}
                            </span>
                        </div>
                    </div>

                    <button class="p-2.5 hover:scale-105 transition" @click="master.basketCanvas = true">
                        <div class="w-6 h-6 relative">
                            <img :src="'/assets/icons/bag.svg'" class="w-6 h-6 text-primary" />
                            <span
                                class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-xs">
                                {{ basketStore.total }}
                            </span>
                        </div>
                    </button>
                </div>

                <button v-if="!AuthStore.user" class="flex items-center gap-2 lg:p-2.5 text-slate-600 hover:text-primary transition font-medium"
                    @click="showLoginDialog">
                    <span class="text-base font-medium leading-normal">{{ $t('Login') }}</span>
                    <UserIcon class="w-5 h-5" />
                </button>
                <div v-else>
                    <AuthUserDropdown />
                </div>
            </div>

            <!--******=== Mobile View Navbar Top Bar ===********-->
            <div class="md:hidden flex items-center gap-2 relative">
                <!-- Delivery Location Pill (Mobile) -->
                <div class="flex xl:hidden items-center gap-1 cursor-pointer px-2 py-1 rounded-full border border-slate-200 bg-slate-50 hover:bg-white text-slate-700 shadow-xs"
                    @click="locationStore.showLocationModal = true">
                    <MapPinIcon class="w-3.5 h-3.5 text-primary shrink-0" />
                    <span class="max-w-[90px] truncate text-[11px] font-bold text-slate-800">{{ locationStore.currentLocationLabel }}</span>
                    <ChevronDownIcon class="w-3 h-3 text-slate-400 shrink-0" />
                </div>

                <!-- Menu Icon -->
                <button class="w-9 h-9 flex items-center justify-center bg-slate-100 rounded-full hover:bg-slate-200 transition" @click="mobileMenuOpen = true">
                    <Bars3Icon class="w-5 h-5 text-slate-900" />
                </button>
            </div>
        </div>

        <!-- Mobile Integrated Search Bar -->
        <div class="md:hidden w-full relative" ref="searchContainerRef">
            <div class="relative flex items-center">
                <input type="text" v-model="search" :placeholder="$t('Search products, categories...')"
                    class="px-3.5 py-2 pl-9 pr-8 block rounded-xl border border-slate-200 focus:border-primary w-full placeholder:text-slate-400 outline-none text-xs font-normal bg-slate-50/80 focus:bg-white shadow-xs transition"
                    @keyup.enter="searchProducts()"
                    @focus="onSearchFocus"
                />
                <MagnifyingGlassIcon class="w-4 h-4 text-slate-400 absolute left-3 pointer-events-none" />
                <button v-if="search" class="absolute right-2 text-slate-400 hover:text-slate-600 p-1" @click="clearSearch">
                    <XMarkIcon class="w-3.5 h-3.5" />
                </button>
            </div>

            <!-- Mobile Live Search Results -->
            <div v-if="showLiveDropdown && (searchResults.length > 0 || isSearching)"
                class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50 max-h-[300px] overflow-y-auto">
                <div v-if="isSearching" class="p-3 text-center text-slate-400 text-xs flex items-center justify-center gap-2">
                    <div class="w-3.5 h-3.5 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                    <span>{{ $t('Searching...') }}</span>
                </div>
                <div v-else-if="searchResults.length > 0">
                    <div v-for="item in searchResults" :key="item.id" 
                        @click="goToProduct(item.id)"
                        class="flex items-center gap-2.5 px-3 py-2 hover:bg-slate-50 cursor-pointer border-b border-slate-50 last:border-b-0">
                        <img :src="item.thumbnail" class="w-8 h-8 object-contain rounded-md border border-slate-100 bg-slate-50 p-0.5 shrink-0" />
                        <div class="grow min-w-0">
                            <p class="text-xs font-medium text-slate-900 truncate">{{ item.name }}</p>
                            <p class="text-[11px] font-bold text-primary">{{ master.showCurrency(item.discount_price > 0 ? item.discount_price : item.price) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- search modal -->
            <TransitionRoot as="template" :show="showSearch">
                <Dialog class="relative z-50" @close="showSearch = false">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                        enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100"
                        leave-to="opacity-0">
                        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" />
                    </TransitionChild>

                    <div class="fixed inset-0 z-50 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-start justify-center p-4 text-center">
                            <TransitionChild as="template" enter="ease-out duration-300"
                                enter-from="opacity-0 -translate-y-4"
                                enter-to="opacity-100 translate-y-0" leave="ease-in duration-200"
                                leave-from="opacity-100 translate-y-0"
                                leave-to="opacity-0 -translate-y-4">
                                <DialogPanel
                                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all my-4 w-full max-w-lg">
                                    <div class="bg-white px-4 pb-2 pt-4">
                                        <div class="w-full flex items-center justify-between mb-3 border-b border-slate-100 pb-2">
                                            <span class="font-semibold text-slate-900">{{ $t('Search Products') }}</span>
                                            <button type="button"
                                                class="border border-slate-100 rounded-full p-1.5 outline-none hover:bg-slate-100"
                                                @click="showSearch = false">
                                                <XMarkIcon class="w-5 h-5 text-slate-700" />
                                            </button>
                                        </div>
                                        <div class="relative flex items-center">
                                            <input type="text" v-model="search" :placeholder="$t('Search product...')"
                                                class="px-3.5 py-2.5 block rounded-xl border border-slate-200 focus:border-primary w-full placeholder:text-gray-400 outline-none text-sm font-normal"
                                                @keyup.enter="showSearch = false; searchProducts()" />
                                        </div>

                                        <!-- Mobile live results -->
                                        <div v-if="searchResults.length > 0" class="mt-3 max-h-60 overflow-y-auto divide-y divide-slate-100">
                                            <div v-for="item in searchResults" :key="item.id" 
                                                @click="showSearch = false; goToProduct(item.id)"
                                                class="flex items-center gap-3 py-2 cursor-pointer">
                                                <img :src="item.thumbnail" class="w-9 h-9 object-contain rounded-md border border-slate-100 p-0.5 shrink-0" />
                                                <div class="grow min-w-0">
                                                    <p class="text-xs font-medium text-slate-900 truncate">{{ item.name }}</p>
                                                    <p class="text-xs font-bold text-primary">{{ master.showCurrency(item.discount_price > 0 ? item.discount_price : item.price) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-slate-50 px-4 py-3">
                                        <button type="button"
                                            class="inline-flex w-full justify-center rounded-xl bg-primary px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-600 active:scale-98 transition"
                                            @click="showSearch = false; searchProducts()">
                                            {{ $t('Search') }}
                                        </button>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>

            <!-- Mobile Menu Canvas Drawer -->
            <TransitionRoot as="template" :show="mobileMenuOpen">
                <Dialog as="div" class="relative z-10" @close="mobileMenuOpen = false">
                    <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0"
                        enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100"
                        leave-to="opacity-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-30 transition-opacity" />
                    </TransitionChild>

                    <div class="fixed inset-0 overflow-hidden">
                        <div class="absolute inset-0 overflow-hidden">
                            <div class="pointer-events-none fixed inset-y-0  flex max-w-full"
                                :class="master.langDirection == 'rtl' ? 'left-0 sm:pr-10' : 'right-0 sm:pl-10'">
                                <TransitionChild as="template"
                                    enter="transform transition ease-in-out duration-500 sm:duration-700"
                                    :enter-from="master.langDirection == 'rtl' ? '-translate-x-full' : 'translate-x-full'"
                                    enter-to="translate-x-0"
                                    leave="transform transition ease-in-out duration-500 sm:duration-700"
                                    leave-from="translate-x-0"
                                    :leave-to="master.langDirection == 'rtl' ? '-translate-x-full' : 'translate-x-full'">
                                    <DialogPanel class="pointer-events-auto relative w-screen max-w-md">
                                        <TransitionChild as="template" enter="ease-in-out duration-500"
                                            enter-from="opacity-0" enter-to="opacity-100"
                                            leave="ease-in-out duration-500" leave-from="opacity-100"
                                            leave-to="opacity-0">
                                            <div class="absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4">
                                            </div>
                                        </TransitionChild>
                                        <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl p-4">

                                            <div class="flex justify-between items-center">
                                                <div
                                                    class="text-slate-950 text-lg font-bold leading-normal tracking-tight">
                                                    {{ $t('Menu') }}</div>
                                                <button
                                                    class="w-7 h-7 flex justify-center items-center bg-slate-100 rounded-full"
                                                    @click="mobileMenuOpen = false">
                                                    <XMarkIcon class="w-5 h-5 text-slate-700" />
                                                </button>
                                            </div>

                                            <!-- login button -->
                                            <div v-if="!AuthStore.user" class="mt-5 p-2 bg-primary rounded-lg">
                                                <div class="px-3 py-2.5 bg-white rounded-md border border-slate-100 flex justify-between"
                                                    @click="showLoginDialog">
                                                    <div class="flex items-center gap-2">
                                                        <UserIcon class="w-5 h-5 text-slate-600" />
                                                        <div class="text-slate-600 text-sm font-normal leading-tight">
                                                            {{ $t('Login') }}
                                                        </div>
                                                    </div>
                                                    <ChevronRightIcon class="w-5 h-5 text-slate-600" />
                                                </div>
                                            </div>

                                            <div v-else class="bg-primary-100 p-3 rounded-lg mt-5">
                                                <AuthUserDropdown />
                                            </div>

                                            <div
                                                class="p-2 bg-slate-50 rounded-lg border border-slate-100 flex flex-col gap-1 mt-5">

                                                <div class="flex justify-between items-center px-3 py-2.5 bg-white rounded-md border border-slate-100 gap-2"
                                                    @click="showWishlist()">
                                                    <div class="flex items-center gap-2">
                                                        <img :src="'/assets/icons/heart.svg'"
                                                            class="w-5 h-5 text-slate-600" />
                                                        <div class="text-slate-600 text-sm font-normal leading-tight">
                                                            {{ $t('Wishlist') }}
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="w-5 h-5 bg-red-500 rounded-3xl border border-white flex justify-center items-center text-white">
                                                        <span class="text-white text-xs font-bold">
                                                            {{ AuthStore.favoriteProducts }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="flex justify-between items-center px-3 py-2.5 bg-white rounded-md border border-slate-100 gap-2"
                                                    @click="showMyCart()">
                                                    <div class="flex items-center gap-2">
                                                        <img :src="'/assets/icons/bag.svg'"
                                                            class="w-6 h-6 text-slate-600" />
                                                        <div class="text-slate-600 text-sm font-normal leading-tight">
                                                            {{ $t('My Cart') }}
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="w-5 h-5 bg-red-500 rounded-3xl border border-white flex justify-center items-center text-white">
                                                        <span class="text-white text-xs font-bold">
                                                            {{ basketStore.total }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="justify-start inline-flex grow flex-col mt-5 gap-2">

                                                <div v-for="menu in master.menus" :key="menu.id" class="w-full  text-base">
                                                    <router-link v-if="!menu.is_external" :to="menu.url"
                                                        class="py-2 font-normal text-slate-600 border-b-2 border-slate-200 block">
                                                        {{ menu.name }}
                                                    </router-link>
                                                    <a v-else :href="menu.url" :target="menu.target"
                                                        class="py-2 border-b-2 border-slate-200 block font-normal text-slate-600">
                                                        {{ menu.name }}
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </DialogPanel>
                                </TransitionChild>
                            </div>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>

        <!-- Login Dialog Modal -->
        <LoginModal />
        <!-- End Login Dialog Modal -->
    </div>
</template>

<script setup>
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { Bars3Icon, ChevronDownIcon, ChevronRightIcon, MapPinIcon, UserIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/solid'
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import AuthUserDropdown from './AuthUserDropdown.vue'
import LoginModal from './LoginModal.vue'

import { useAuth } from '../stores/AuthStore'
import { useBasketStore } from '../stores/BasketStore'
import { useLocationStore } from '../stores/LocationStore'
import { useMaster } from '../stores/MasterStore'

const route = useRoute();
const router = useRouter();
const basketStore = useBasketStore();
const locationStore = useLocationStore();

const AuthStore = useAuth();
const master = useMaster();

const search = ref('');
const showSearch = ref(false);
const searchContainerRef = ref(null);
const searchResults = ref([]);
const isSearching = ref(false);
const showLiveDropdown = ref(false);
let debounceTimeout = null;

const onSearchFocus = () => {
    if (searchResults.value.length > 0) {
        showLiveDropdown.value = true;
    }
};

const clearSearch = () => {
    search.value = '';
    searchResults.value = [];
    showLiveDropdown.value = false;
};

const performLiveSearch = (query) => {
    if (!query || query.trim().length < 2) {
        searchResults.value = [];
        showLiveDropdown.value = false;
        isSearching.value = false;
        return;
    }

    isSearching.value = true;
    showLiveDropdown.value = true;

    axios.get('/products', {
        params: {
            search: query.trim(),
            per_page: 5
        }
    }).then((res) => {
        isSearching.value = false;
        searchResults.value = res.data?.data?.products || [];
    }).catch(() => {
        isSearching.value = false;
        searchResults.value = [];
    });
};

watch(search, (newVal) => {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        performLiveSearch(newVal);
    }, 250);
});

const handleClickOutside = (e) => {
    if (searchContainerRef.value && !searchContainerRef.value.contains(e.target)) {
        showLiveDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    if (route.path == '/products') {
        search.value = master.search;
    } else {
        search.value = '';
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const toggleSearch = () => {
    showSearch.value = !showSearch.value;
};

const showMyCart = () => {
    mobileMenuOpen.value = false;
    master.basketCanvas = true;
};

const showWishlist = () => {
    mobileMenuOpen.value = false;
    if (!AuthStore.token) {
        return showLoginDialog();
    }
    router.push('/wishlist');
};

watch(() => route.path, () => {
    mobileMenuOpen.value = false;
    showLiveDropdown.value = false;
    if (route.path == '/products') {
        search.value = master.search;
    } else {
        search.value = '';
    }
});

const mobileMenuOpen = ref(false);

const showLoginDialog = () => {
    mobileMenuOpen.value = false;
    AuthStore.showLoginModal();
};

const goToProduct = (id) => {
    showLiveDropdown.value = false;
    search.value = '';
    router.push({ name: 'productDetails', params: { id } });
};

const searchProducts = () => {
    showLiveDropdown.value = false;
    master.search = search.value;
    if (route.path != '/products') {
        search.value = '';
    }
    router.push({ name: 'products' });
};
</script>

<style scoped>
.router-link-active {
    @apply border-primary text-primary
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
