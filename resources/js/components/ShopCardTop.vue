<template>
    <div class="w-full rounded-xl overflow-hidden shadow-sm hover:shadow-md transition duration-300">
        <router-link :to="`/shops/${props.shop?.id}`" class="block group">
            <div class="h-16 sm:h-20 w-full relative overflow-hidden bg-slate-200">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" :src="props.shop?.banner" alt="banner" loading="lazy" />

                <!--==== Status ====-->
                <div class="px-1.5 py-0.5 rounded-[8px] absolute top-1.5 right-1.5" :class="props.shop?.shop_status === 'Online' ? 'bg-lime-600' : 'bg-slate-500'">
                    <div class="text-white text-[10px] font-medium leading-none">
                        {{ props.shop?.shop_status }}
                    </div>
                </div>
            </div>
            <div class="w-full px-3 pt-7 pb-3 relative bg-primary hover:bg-primary-600 transition duration-300">

                <!--==== Shop Logo ====-->
                <div class="w-11 h-11 sm:w-12 sm:h-12 absolute bg-slate-300 rounded-full -top-5 sm:-top-6 left-3 border-2 border-white shadow-sm overflow-hidden">
                    <img :src="props.shop?.logo" alt="logo" loading="lazy"
                        class="w-full h-full object-cover rounded-full" />
                </div>

                <!--==== Shop Name ====-->
                <div class="text-white text-xs sm:text-sm font-bold leading-normal truncate overflow-hidden">
                    {{ props.shop?.name }}
                </div>

                <div class="flex justify-start items-center gap-2 sm:gap-3 w-full mt-2">
                    <!-- products -->
                    <div class="text-primary-200 text-[11px] sm:text-xs font-normal leading-tight truncate">
                        {{ props.shop?.total_products }} + {{ $t('Items') }}
                    </div>

                    <div class="h-2.5 w-[0px] border border-primary-400"></div>

                    <!-- Rating -->
                    <div class="flex items-center gap-1 shrink-0">
                        <StarIcon class="w-3.5 h-3.5 text-yellow-400" />
                        <div class="text-white text-[11px] sm:text-xs font-bold">
                            {{ props.shop?.rating?.toFixed(1) }}
                        </div>
                    </div>
                </div>

                <div class="mt-2 flex justify-start items-center gap-1">
                    <div class="text-white text-[11px] sm:text-xs font-normal leading-tight">{{ $t('Visit Store') }}</div>
                    <ArrowRightIcon class="w-3.5 h-3.5 text-white group-hover:translate-x-1 transition-transform duration-200" />
                </div>

            </div>
        </router-link>
    </div>
</template>

<script setup>
import { StarIcon, ArrowRightIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    shop: Object
});

</script>
