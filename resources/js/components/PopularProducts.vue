<template>
    <div class="main-container py-6 sm:py-10 md:py-12">

        <!-- Header -->
        <div v-if="!isLoading" class="flex justify-between items-center gap-4">
            <div class="text-slate-900 text-lg md:text-3xl font-bold leading-9">{{ $t('Popular Products') }}</div>

            <router-link to="/most-popular" class="flex items-center gap-1 text-slate-600 hover:text-primary transition">
                <div class="text-sm sm:text-base font-normal leading-normal">{{ $t('View All') }}</div>
                <ArrowRightIcon class="w-4 h-4 sm:w-5 sm:h-5" />
            </router-link>
        </div>
        <!-- loading -->
        <SkeletonLoader v-else class="w-48 sm:w-60 md:w-72 lg:w-96 h-10 sm:h-12 rounded-lg" />

        <!-- Products -->
        <div class="mt-4 sm:mt-6 md:mt-8 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-2.5 sm:gap-4 md:gap-6 items-start">

            <div v-if="!isLoading" v-for="product in products" :key="product.id" class="w-full">
                <ProductCard :product="product"/>
            </div>

            <!-- loading -->
            <div v-else v-for="i in 6" :key="i">
                <SkeletonLoader class="w-full h-[200px] sm:h-[300px] rounded-2xl" />
            </div>
        </div>

    </div>
</template>

<script setup>
import { ArrowRightIcon } from '@heroicons/vue/24/outline';
import ProductCard from './ProductCard.vue';
import SkeletonLoader from './SkeletonLoader.vue';

const props = defineProps({
    products: Array,
    isLoading: Boolean
})

</script>
