<template>
    <div class="main-container mt-6 sm:mt-10">
        <div v-if="!isLoading" class="p-3.5 sm:p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6 lg:gap-8">
            <div class="flex items-start gap-2.5 sm:gap-3.5">
                <img :src="'/assets/icons/secure-payment.svg'" class="w-7 h-7 sm:w-10 sm:h-10 shrink-0" loading="lazy" alt="icon"/>
                <div class="text-slate-600">
                    <div class="text-slate-950 text-xs sm:text-base font-bold leading-tight">
                        {{ $t('Secure Payment Gateways') }}
                    </div>
                    <div class="text-slate-600 text-[11px] sm:text-sm font-normal mt-0.5 leading-snug hidden xs:block">
                        {{ $t('48+ gateways to ensure your security') }}.
                    </div>
                </div>
            </div>

            <div class="flex items-start gap-2.5 sm:gap-3.5">
                <img :src="'/assets/icons/customer-review.svg'" class="w-7 h-7 sm:w-10 sm:h-10 shrink-0" loading="lazy" alt="icon"/>
                <div class="text-slate-600">
                    <div class="text-slate-950 text-xs sm:text-base font-bold leading-tight">
                        {{ $t('Genuine Customer Reviews') }}
                    </div>
                    <div class="text-slate-600 text-[11px] sm:text-sm font-normal mt-0.5 leading-snug hidden xs:block">
                        {{ $t('Find verified reviews showcased on our platforms') }}.
                    </div>
                </div>
            </div>

            <div class="flex items-start gap-2.5 sm:gap-3.5">
                <img :src="'/assets/icons/support.svg'" class="w-7 h-7 sm:w-10 sm:h-10 shrink-0" loading="lazy" alt="icon"/>
                <div class="text-slate-600">
                    <div class="text-slate-950 text-xs sm:text-base font-bold leading-tight">
                       {{ $t('24/7 Customer Support') }}
                    </div>
                    <div class="text-slate-600 text-[11px] sm:text-sm font-normal mt-0.5 leading-snug hidden xs:block">
                        {{ $t('Always our support team is available for you') }}.
                    </div>
                </div>
            </div>

            <div class="flex items-start gap-2.5 sm:gap-3.5">
                <img :src="'/assets/icons/easy-return.svg'" class="w-7 h-7 sm:w-10 sm:h-10 shrink-0" loading="lazy" alt="icon"/>
                <div class="text-slate-600">
                    <div class="text-slate-950 text-xs sm:text-base font-bold leading-tight">
                        {{ $t('Easy Return Policy') }}
                    </div>
                    <div class="text-slate-600 text-[11px] sm:text-sm font-normal mt-0.5 leading-snug hidden xs:block">
                        {{ $t("If you're not satisfied, return it hassle-free") }}.
                    </div>
                </div>
            </div>
        </div>
        <!-- loader -->
        <div v-else class="p-3.5 sm:p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6">
            <SkeletonLoader v-for="i in 4" :key="i" class="w-full h-16 sm:h-24 rounded-xl" />
        </div>
    </div>
</template>

<script setup>
import SkeletonLoader from './SkeletonLoader.vue';
const props = defineProps({
    isLoading: {
        type: Boolean,
        default: true
    }
})
</script>
