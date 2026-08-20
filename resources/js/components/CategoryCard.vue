<template>
    <router-link :to="routeUrl" class="w-full block group">
        <div class="p-2 sm:p-3 bg-white rounded-2xl border border-slate-100 group-hover:border-primary shadow-xs transition duration-300 flex flex-col items-center">
            <div class="w-full overflow-hidden h-16 sm:h-20 md:h-24 rounded-xl bg-slate-50 flex items-center justify-center p-1.5">
                <img :src="props.category?.thumbnail"
                    class="w-full h-full object-contain transition duration-500 group-hover:scale-110" loading="lazy" />
            </div>
            <div class="text-center text-slate-700 group-hover:text-primary transition-colors text-xs sm:text-sm font-semibold leading-tight truncate mt-1.5 w-full">
                {{ props.category?.name }}
            </div>
        </div>
    </router-link>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { ref, onMounted } from 'vue';

const route = useRoute();

const props = defineProps({
    category: Object
});

const routeUrl = ref(`/categories/${props.category?.id}`);

onMounted(() => {
    if (route.name === 'shop-detail') {
        routeUrl.value = `/shops/${route.params.id}/categories/${props.category?.id}`
    }
});

</script>
