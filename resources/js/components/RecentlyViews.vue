<template>
    <div v-if="!isLoading && props?.products?.length > 0" class="main-container py-10 bg-slate-100/80 border-t border-slate-200">
        <div class="flex justify-between items-center gap-4 mb-6">
            <div>
                <div class="text-slate-900 text-lg md:text-2xl font-bold leading-tight">
                    {{ $t('Recently Viewed') }}
                </div>
                <div class="text-slate-500 text-xs md:text-sm mt-1">
                    {{ $t('Products you recently explored') }}
                </div>
            </div>
        </div>

        <div :dir="master.langDirection || 'ltr'">
            <swiper :navigation="true" :modules="modules" :breakpoints="breakpoints" class="recentlyViewedSwiper" :space-between="16" :loop="false">
                <swiper-slide v-for="product in products" :key="product.id" class="h-auto">
                    <ProductCard :product="product" class="h-full" />
                </swiper-slide>
            </swiper>
        </div>
    </div>

    <!-- loading -->
    <div v-if="isLoading" class="main-container py-10 bg-slate-100/80 border-t border-slate-200">
        <div class="flex justify-between items-center gap-4 mb-6">
            <div>
                <SkeletonLoader class="w-48 h-8 rounded-lg" />
                <SkeletonLoader class="w-32 h-4 rounded mt-1.5" />
            </div>
        </div>
        <div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4 md:gap-5">
                <div v-for="i in 6" :key="i">
                    <SkeletonLoader class="w-full h-[260px] sm:h-[300px] rounded-2xl" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, A11y } from 'swiper/modules';

import ProductCard from './ProductCard.vue';
import SkeletonLoader from './SkeletonLoader.vue';
import { useMaster } from '../stores/MasterStore';

const master = useMaster();
// Import Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';

const props = defineProps({
    products: Array,
    isLoading: Boolean
});

const modules = [Navigation, A11y];
const breakpoints = {
    320: {
        slidesPerView: 2,
        spaceBetween: 12
    },
    640: {
        slidesPerView: 3,
        spaceBetween: 16
    },
    768: {
        slidesPerView: 4,
        spaceBetween: 16
    },
    1024: {
        slidesPerView: 5,
        spaceBetween: 20
    },
    1280: {
        slidesPerView: 6,
        spaceBetween: 20
    }
};

</script>

<style>
.recentlyViewedSwiper {
    padding: 6px 2px 14px 2px;
}

.recentlyViewedSwiper .swiper-button-prev,
.recentlyViewedSwiper .swiper-button-next {
    width: 36px;
    height: 36px;
    background-color: #ffffff;
    border-radius: 9999px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    border: 1px solid #e2e8f0;
    color: #334155;
    transition: all 0.2s ease;
}

.recentlyViewedSwiper .swiper-button-prev:hover,
.recentlyViewedSwiper .swiper-button-next:hover {
    background-color: #f8fafc;
    color: #0284c7;
    border-color: #cbd5e1;
    transform: scale(1.05);
}

.recentlyViewedSwiper .swiper-button-prev::after,
.recentlyViewedSwiper .swiper-button-next::after {
    font-size: 14px;
    font-weight: 700;
}

.recentlyViewedSwiper .swiper-button-disabled {
    opacity: 0;
    cursor: auto;
    pointer-events: none;
}
</style>
