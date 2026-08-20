<template>
    <div class="main-container bg-white py-6 sm:py-10 md:py-12">

        <div v-if="!isLoading" class="text-slate-900 text-lg md:text-3xl font-bold leading-9">
            {{ $t('Just For You') }}
        </div>
        <!-- loading -->
        <SkeletonLoader v-else class="w-48 sm:w-60 md:w-72 lg:w-96 h-10 sm:h-12 rounded-lg" />

        <!-- Products -->
        <div v-if="!isLoading && products"
            class="mt-4 sm:mt-6 md:mt-8 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-2.5 sm:gap-4 md:gap-6 items-start">
            <div v-for="product in products" :key="product.id" class="w-full">
                <ProductCard :product="product" />
            </div>
        </div>

        <!-- loading -->
         <div v-if="isLoading" class="mt-4 sm:mt-6 md:mt-8 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-2.5 sm:gap-4 md:gap-6 items-start">
             <div v-for="i in 6" :key="i">
                 <SkeletonLoader class="w-full h-[200px] sm:h-[300px] rounded-2xl" />
            </div>
        </div>

        <!-- Load More Products -->
        <div v-if="!isLoading" class="mt-6 md:mt-10 w-full flex justify-center">
            <button v-if="hasMoreProducts && !loadMore"
                class="w-full sm:w-72 px-6 py-2.5 sm:py-3.5 rounded-xl border-2 border-primary text-primary hover:bg-primary hover:text-white transition duration-300 text-sm sm:text-base font-semibold shadow-xs"
                @click="loadMoreProducts()">
                <span>{{ $t('Load More Products') }}</span>
            </button>

            <button v-if="loadMore"
                class="w-full sm:w-72 px-6 py-2.5 sm:py-3.5 rounded-xl border border-primary-200 bg-primary-50 text-primary flex items-center justify-center cursor-not-allowed text-sm sm:text-base font-semibold"
                disabled>
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                {{ $t('Loading products') }}...
            </button>
        </div>

    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import ProductCard from './ProductCard.vue';
import { useAuth } from '../stores/AuthStore';
import SkeletonLoader from './SkeletonLoader.vue';

const authStore = useAuth();

const props = defineProps({
    justForYou: Object,
    isLoading: Boolean
});

const currentPage = ref(2);
const hasMoreProducts = ref(false);
const totalPages = ref(1);
const loadMore = ref(false);

const products = ref([]);
watch(() => props.justForYou, () => {
    products.value = props.justForYou?.products;
    totalPages.value = Math.ceil(props.justForYou?.total / 12);
    if (totalPages.value > 1) {
        hasMoreProducts.value = true;
    }
});

const loadMoreProducts = () => {
    loadMore.value = true
    axios.get('/home?page=' + currentPage.value + '&per_page=12',{
        headers: {
            Authorization: authStore.token
        }
    }).then((response) => {
        products.value = products.value.concat(response.data.data.just_for_you.products);
        currentPage.value++;
        if (currentPage.value >= totalPages.value) {
            hasMoreProducts.value = false;
        }
        loadMore.value = false
    }).catch((error) => {
        loadMore.value = false
        console.log(error);
    })
}

</script>
