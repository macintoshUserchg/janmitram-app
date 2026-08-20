<template>
    <div class="bg-primary-50/50">
        <div class="main-container py-6 sm:py-10 md:py-12">

            <div class="flex justify-between items-center gap-4">
                <div class="text-slate-900 text-lg md:text-3xl font-bold leading-9">{{ $t('Flash Sale') }}</div>

                <router-link :to="'/flash-sale/' + flashSale?.id" class="flex items-center gap-1 text-slate-600 hover:text-primary transition">
                    <div class="text-sm sm:text-base font-normal leading-normal">{{ $t('View All') }}</div>
                    <ArrowRightIcon class="w-4 h-4 sm:w-5 sm:h-5" />
                </router-link>
            </div>

            <div class="mt-4 sm:mt-6 grid grid-cols-1 xl:grid-cols-5 gap-4 lg:gap-8 items-start">

                <!-- Flash Sale Banner -->
                <div class="p-4 sm:p-6 rounded-2xl flex-col sm:flex-row xl:flex-col justify-between xl:justify-center items-center gap-3 md:gap-4 flex shadow-sm" style="background: conic-gradient(from 180deg at 50% 50%, #8B5CF6 0deg, #C622FF 132.50098586082458deg, #5C87F6 271.2626552581787deg, #8B5CF6 360deg);">
                    <div class="w-full xl:w-auto flex xl:flex-col justify-center items-center gap-3 md:gap-4">
                        <div class="w-16 h-16 rounded-xl sm:h-auto sm:w-[120px] xl:w-full max-h-56 overflow-hidden shadow-xs">
                            <img :src="flashSale?.thumbnail" loading="lazy" class="w-full h-full sm:h-auto object-cover sm:max-h-56"/>
                        </div>

                        <div class="text-center text-white text-lg sm:text-xl md:text-2xl font-bold leading-tight">
                            {{ flashSale?.name }}
                        </div>
                    </div>

                    <div class="flex justify-center flex-wrap md:flex-col items-center gap-2 xl:gap-3">
                        <div class="text-center text-white text-sm sm:text-base font-medium tracking-wide">
                            {{ $t('Ending in') }}
                        </div>

                        <div class="flex justify-center items-center gap-1.5 text-white">
                            <div v-if="endDay > 0" class="w-8 h-8 sm:w-10 sm:h-10 p-1 bg-white rounded-lg shadow-inner flex-col justify-center items-center flex">
                                <div class="text-center text-primary text-sm sm:text-base font-bold leading-tight">
                                    {{ endDay }}
                                </div>
                                <div class="text-center text-slate-500 text-[8px] font-normal leading-none">
                                    {{ $t('Days') }}
                                </div>
                            </div>

                            <span v-if="endDay > 0" class="text-white text-lg sm:text-xl font-bold">:</span>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 p-1 bg-white rounded-lg shadow-inner flex-col justify-center items-center flex">
                                <div class="text-center text-primary text-sm sm:text-base font-bold leading-tight">
                                    {{ endHour }}
                                </div>
                                <div class="text-center text-slate-500 text-[8px] font-normal leading-none">
                                    {{ $t('Hours') }}
                                </div>
                            </div>

                            <span class="text-white text-lg sm:text-xl font-bold">:</span>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 p-1 bg-white rounded-lg shadow-inner flex-col justify-center items-center flex">
                                <div class="text-center text-primary text-sm sm:text-base font-bold leading-tight">
                                    {{ endMinute }}
                                </div>
                                <div class="text-center text-slate-500 text-[8px] font-normal leading-none">
                                    {{ $t('Mins') }}
                                </div>
                            </div>

                            <span v-if="endDay <= 0" class="text-white text-lg sm:text-xl font-bold">:</span>
                            <div v-if="endDay <= 0" class="w-8 h-8 sm:w-10 sm:h-10 p-1 bg-white rounded-lg shadow-inner flex-col justify-center items-center flex">
                                <div class="text-center text-primary text-sm sm:text-base font-bold leading-tight">
                                    {{ endSecond }}
                                </div>
                                <div class="text-center text-slate-500 text-[8px] font-normal leading-none">
                                    {{ $t('Secs') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flash Sale Products -->
                <div class="xl:col-span-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
                    <div v-for="product in flashSale.products" :key="product.id" class="w-full">
                        <ProductCardHorizontal :product="product"/>
                    </div>
                </div>

            </div>

        </div>

    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

import { ArrowRightIcon } from '@heroicons/vue/24/outline';
import ProductCardHorizontal from './ProductCardHorizontal.vue';

const props = defineProps({
    flashSale: Object
});

const endDay = ref('');
const endHour = ref('');
const endMinute = ref('');
const endSecond = ref('');
let countdownInterval = null;

const startCountdown = () => {
    const endDate = new Date(props.flashSale.end_date).getTime();

    countdownInterval = setInterval(() => {
        const now = new Date().getTime();
        const timeLeft = endDate - now;

        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            endDay.value = '00';
            endHour.value = '00';
            endMinute.value = '00';
            endSecond.value = '00';
        } else {
            endDay.value = String(Math.floor(timeLeft / (1000 * 60 * 60 * 24))).padStart(2, '0');
            endHour.value = String(Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
            endMinute.value = String(Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
            endSecond.value = String(Math.floor((timeLeft % (1000 * 60)) / 1000)).padStart(2, '0');
        }
    }, 1000);
};

onMounted(startCountdown);

onUnmounted(() => {
    clearInterval(countdownInterval);
});

</script>

