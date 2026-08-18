<template>
    <div class="flex gap-4">
        <!-- profile avatar -->
        <div class="w-11 h-11 shrink-0 rounded-full overflow-hidden border border-slate-200">
            <img :src="props.review?.customer_profile" class="w-full h-full object-cover">
        </div>

        <div class="grow flex flex-col gap-3 border-b border-slate-100 pb-5">
            <!-- name and rating -->
            <div class="flex justify-between items-start gap-3">
                <div class="flex flex-col gap-1">
                    <!-- name -->
                    <div class="flex items-center gap-2">
                        <span class="text-slate-950 text-sm font-bold leading-tight">
                            {{ props.review?.customer_name }}
                        </span>
                        <span v-if="props.review?.shop_name" class="text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            {{ props.review?.shop_name }}
                        </span>
                    </div>

                    <!-- rating stars -->
                    <div class="flex items-center gap-1">
                        <div class="flex">
                            <StarIcon v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= Math.round(props.review?.rating || 0) ? 'text-amber-400' : 'text-slate-200'" />
                        </div>
                        <span class="text-slate-900 text-xs font-bold ml-1">
                            {{ Number(props.review?.rating || 0).toFixed(1) }}
                        </span>
                    </div>
                </div>

                <!-- review date -->
                <div class="text-right text-slate-400 text-xs font-medium">
                    {{ props.review?.created_at }}
                </div>
            </div>

            <!-- review message -->
            <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                {{ props.review?.description }}
            </div>

            <!-- Review Photo Gallery -->
            <div v-if="props.review?.photos && props.review.photos.length > 0" class="flex gap-2 flex-wrap mt-1">
                <div
                    v-for="(photo, index) in props.review.photos"
                    :key="index"
                    @click="activePhoto = photo"
                    class="cursor-pointer overflow-hidden rounded-lg border border-slate-200 hover:opacity-90 transition shadow-xs"
                >
                    <img :src="photo" class="w-16 h-16 object-cover" />
                </div>
            </div>

            <!-- Official Store / Admin Reply -->
            <div v-if="props.review?.reply" class="mt-2 p-3 bg-slate-50 border-l-4 border-primary rounded-r-lg">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <span class="text-xs font-bold text-primary flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        {{ $t("Official Response") }}
                    </span>
                    <span v-if="props.review?.replied_at" class="text-xs text-slate-400">
                        {{ props.review?.replied_at }}
                    </span>
                </div>
                <div class="text-xs text-slate-600 leading-normal">
                    {{ props.review?.reply }}
                </div>
            </div>
        </div>

        <!-- Lightbox Modal for Photo Preview -->
        <div
            v-if="activePhoto"
            @click="activePhoto = null"
            class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4 cursor-pointer"
        >
            <div class="relative max-w-2xl max-h-[85vh] bg-white rounded-xl overflow-hidden shadow-2xl" @click.stop>
                <img :src="activePhoto" class="max-w-full max-h-[80vh] object-contain mx-auto" />
                <button
                    @click="activePhoto = null"
                    class="absolute top-3 right-3 bg-black/60 hover:bg-black text-white rounded-full w-8 h-8 flex items-center justify-center text-lg shadow"
                >
                    &times;
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { StarIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    review: Object
});

const activePhoto = ref(null);
</script>
