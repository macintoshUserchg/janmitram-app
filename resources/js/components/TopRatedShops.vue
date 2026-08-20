<template>
    <div class="main-container py-8 md:py-10 bg-slate-100">

        <!-- Header -->
        <div v-if="!isLoading" class="flex justify-between items-center gap-4">
            <div class="text-slate-800 text-lg md:text-2xl font-bold leading-8">{{ $t('Top Rated Shops') }}</div>

            <router-link to="/shops" class="flex items-center gap-1">
                <div class="text-slate-600 text-sm md:text-base font-normal leading-normal">{{ $t('View All') }}</div>
                <ArrowRightIcon class="w-4 h-4 md:w-5 md:h-5 text-slate-600" />
            </router-link>
        </div>
        <!-- loading -->
        <SkeletonLoader v-else class="w-48 sm:w-60 md:w-72 lg:w-96 h-10 rounded-lg" />

        <!-- Shops -->
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4 md:gap-5 items-start">

            <div v-if="!isLoading" v-for="shop in shops" :key="shop.id" class="w-full">
                <ShopCardTop :shop="shop"/>
            </div>

            <!-- loading -->
            <div v-else v-for="i in 6" :key="i">
                <SkeletonLoader class="w-full h-[150px] sm:h-[165px] rounded-xl" />
            </div>
        </div>

    </div>
</template>

<script setup>
import { ArrowRightIcon } from '@heroicons/vue/24/outline';
import ShopCardTop from './ShopCardTop.vue';
import SkeletonLoader from './SkeletonLoader.vue';

const { shops, isLoading } = defineProps(['shops', 'isLoading']);

</script>
