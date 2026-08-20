<template>
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-[0_-4px_16px_rgba(0,0,0,0.06)] md:hidden pb-[env(safe-area-inset-bottom)]">
        <div class="grid grid-cols-5 items-center h-14 px-1">
            <!-- Home -->
            <router-link to="/" class="flex flex-col items-center justify-center py-1 transition group"
                :class="route.path === '/' ? 'text-primary' : 'text-slate-500 hover:text-slate-900'">
                <HomeIcon class="w-5 h-5 transition-transform group-active:scale-90" />
                <span class="text-[10px] font-medium leading-tight mt-0.5">{{ $t('Home') }}</span>
            </router-link>

            <!-- Categories -->
            <router-link to="/categories" class="flex flex-col items-center justify-center py-1 transition group"
                :class="route.path.startsWith('/categories') ? 'text-primary' : 'text-slate-500 hover:text-slate-900'">
                <Squares2X2Icon class="w-5 h-5 transition-transform group-active:scale-90" />
                <span class="text-[10px] font-medium leading-tight mt-0.5">{{ $t('Categories') }}</span>
            </router-link>

            <!-- Cart (Drawer Trigger) -->
            <button @click="openCart" class="flex flex-col items-center justify-center py-1 text-slate-500 hover:text-slate-900 transition group relative">
                <div class="relative">
                    <ShoppingBagIcon class="w-5 h-5 transition-transform group-active:scale-90" :class="basketStore.total > 0 ? 'text-primary' : 'text-slate-500'" />
                    <span v-if="basketStore.total > 0"
                        class="absolute -top-1.5 -right-2 min-w-[16px] h-4 bg-red-500 text-white rounded-full text-[9px] font-bold flex items-center justify-center px-1 shadow-xs animate-pulse">
                        {{ basketStore.total > 99 ? '99+' : basketStore.total }}
                    </span>
                </div>
                <span class="text-[10px] font-medium leading-tight mt-0.5" :class="basketStore.total > 0 ? 'text-primary font-bold' : ''">{{ $t('Cart') }}</span>
            </button>

            <!-- Wishlist -->
            <button @click="openWishlist" class="flex flex-col items-center justify-center py-1 transition group relative"
                :class="route.path === '/wishlist' ? 'text-primary' : 'text-slate-500 hover:text-slate-900'">
                <div class="relative">
                    <HeartIcon class="w-5 h-5 transition-transform group-active:scale-90" />
                    <span v-if="authStore.favoriteProducts > 0"
                        class="absolute -top-1.5 -right-2 min-w-[16px] h-4 bg-red-500 text-white rounded-full text-[9px] font-bold flex items-center justify-center px-1 shadow-xs">
                        {{ authStore.favoriteProducts > 99 ? '99+' : authStore.favoriteProducts }}
                    </span>
                </div>
                <span class="text-[10px] font-medium leading-tight mt-0.5">{{ $t('Wishlist') }}</span>
            </button>

            <!-- Account / Profile -->
            <button @click="openAccount" class="flex flex-col items-center justify-center py-1 transition group"
                :class="route.path.startsWith('/dashboard') || route.path.startsWith('/profile') ? 'text-primary' : 'text-slate-500 hover:text-slate-900'">
                <UserIcon class="w-5 h-5 transition-transform group-active:scale-90" />
                <span class="text-[10px] font-medium leading-tight mt-0.5">{{ authStore.user ? $t('Account') : $t('Login') }}</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { HomeIcon, Squares2X2Icon, ShoppingBagIcon, HeartIcon, UserIcon } from '@heroicons/vue/24/outline';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '../stores/AuthStore';
import { useBasketStore } from '../stores/BasketStore';
import { useMaster } from '../stores/MasterStore';

const route = useRoute();
const router = useRouter();
const authStore = useAuth();
const basketStore = useBasketStore();
const master = useMaster();

const openCart = () => {
    master.basketCanvas = true;
};

const openWishlist = () => {
    if (!authStore.token) {
        authStore.showLoginModal();
        return;
    }
    router.push('/wishlist');
};

const openAccount = () => {
    if (!authStore.token) {
        authStore.showLoginModal();
        return;
    }
    router.push('/dashboard');
};
</script>
