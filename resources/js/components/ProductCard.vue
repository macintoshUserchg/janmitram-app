<template>
    <div class="rounded-2xl border border-slate-100 transition-all duration-300 group bg-white overflow-hidden relative flex flex-col justify-between shadow-xs hover:shadow-lg hover:-translate-y-1"
        :class="props.product?.quantity > 0 ? 'hover:border-primary/40' : ''">

        <div class="flex flex-col">
            <div class="bg-white">
                <div class="w-full h-32 xs:h-36 sm:h-52 overflow-hidden relative bg-slate-50/50 flex items-center justify-center p-2"
                    :class="props.product?.quantity > 0 ? '' : 'opacity-40'">
                    <div class="cursor-pointer w-full h-full flex items-center justify-center" @click="showProductDetails">
                        <!-- thumbnail -->
                        <img :src="props.product?.thumbnail" class="w-full h-full group-hover:scale-105 transition duration-500 object-contain" loading="lazy" />
                    </div>

                    <!--discount badge--->
                    <div v-if="props.product?.discount_percentage > 0"
                        class="px-1.5 py-0.5 bg-gradient-to-r from-red-500 to-rose-600 rounded-full text-white text-[10px] sm:text-[11px] font-bold tracking-wide absolute top-2 left-2 shadow-xs">
                        {{ props.product?.discount_percentage }}% {{ $t('OFF') }}
                    </div>

                    <!--favorite-->
                    <button v-if="props.product?.is_favorite"
                        class="absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full justify-center items-center flex cursor-pointer bg-white/90 backdrop-blur-sm shadow-xs transition hover:scale-110"
                        @click.stop="favoriteAddOrRemove">
                        <HeartIcon class="w-4 h-4 sm:w-5 sm:h-5 text-red-500" />
                    </button>

                    <!--unfavorite-->
                    <button v-else
                        class="absolute flex sm:hidden group-hover:flex top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full justify-center items-center cursor-pointer bg-white/90 backdrop-blur-sm shadow-xs transition-all duration-200 hover:scale-110"
                        @click.stop="favoriteAddOrRemove">
                        <HeartIconOutline class="w-4 h-4 sm:w-5 sm:h-5 text-slate-500 hover:text-red-500" />
                    </button>

                    <!-- Digital Product Badge -->
                    <span v-if="props.product?.is_digital == true"
                        class="absolute bottom-2 right-2 inline-flex gap-1 items-center rounded-lg bg-emerald-600/90 backdrop-blur-sm px-1.5 py-0.5 text-[10px] font-semibold text-white shadow-xs"
                    >
                    <ArrowDownTrayIcon class="w-3 h-3" />
                        {{ $t('Digital') }}
                    </span>
                </div>

                <div class="cursor-pointer" @click="showProductDetails">
                    <div class="bg-white p-2.5 sm:p-3 flex flex-col items-start gap-1">

                        <div class="text-slate-900 text-xs sm:text-base font-medium leading-snug line-clamp-2 w-full group-hover:text-primary transition-colors min-h-[32px] sm:min-h-[44px]"
                            :class="props.product?.quantity > 0 ? '' : 'opacity-40'">
                            {{ props.product?.name }}
                        </div>

                        <div class="flex items-baseline gap-1 sm:gap-1.5 flex-wrap mt-0.5" :class="props.product?.quantity > 0 ? '' : 'opacity-40'">
                            <!-- price -->
                            <div class="text-primary text-sm sm:text-lg font-bold">
                                {{ masterStore.showCurrency(props.product?.discount_price > 0 ?
                                    props.product?.discount_price : props.product?.price) }}
                            </div>
                            <!-- unit -->
                            <div v-if="props.product?.unit?.name" class="text-slate-500 text-[10px] font-medium bg-slate-100 px-1 py-0.5 rounded">
                                {{ props.product?.unit?.name }}
                            </div>
                            <!-- discount price -->
                            <div v-if="props.product?.discount_price > 0"
                                class="text-slate-400 text-[10px] sm:text-xs font-normal line-through">
                                {{ masterStore.showCurrency(props.product?.price) }}
                            </div>
                        </div>

                        <div class="flex justify-between items-center w-full pt-1 border-t border-slate-100 text-[11px] sm:text-xs text-slate-500 mt-0.5">
                            <div class="flex items-center gap-1"
                                :class="props.product?.quantity > 0 ? '' : 'opacity-40'">
                                <StarIcon class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-amber-400" />
                                <span class="text-slate-800 font-semibold">{{ props.product?.rating }}</span>
                                <span class="hidden xs:inline">({{ props.product?.total_reviews }})</span>
                            </div>

                            <div v-if="props.product?.quantity > 0" class="text-right text-slate-500 font-medium">
                                {{ props.product?.total_sold }} {{ $t('Sold') }}
                            </div>
                            <div v-else class="text-right text-red-500 font-semibold">
                                {{ $t('Out of Stock') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full p-2 sm:p-2.5 pt-0">
            <div v-if="props.product?.quantity > 0" class="w-full">
                <!-- In-Card Quantity Stepper when already in cart -->
                <div v-if="props.product?.is_digital == false && cartQty > 0" 
                    class="flex items-center justify-between w-full bg-emerald-50 border border-emerald-200 rounded-xl p-0.5 sm:p-1 shadow-xs">
                    <button @click.stop="decrementQuantity" 
                        class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center bg-white text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-lg font-bold shadow-xs transition active:scale-95 text-sm">
                        -
                    </button>
                    <span class="text-emerald-800 font-bold text-[11px] sm:text-sm px-0.5">{{ cartQty }} {{ $t('in Cart') }}</span>
                    <button @click.stop="incrementQuantity" 
                        class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center bg-emerald-600 text-white hover:bg-emerald-700 rounded-lg font-bold shadow-xs transition active:scale-95 text-sm">
                        +
                    </button>
                </div>

                <!-- Standard Actions when not yet in cart -->
                <div v-else class="flex items-center gap-1.5 sm:gap-2 w-full">
                    <button v-if="props.product?.is_digital == false"
                        class="cursor-pointer w-8 h-8 sm:w-10 sm:h-10 bg-slate-50 hover:bg-primary hover:text-white text-slate-700 rounded-xl border border-slate-200 hover:border-primary justify-center items-center flex transition active:scale-95 shadow-xs shrink-0"
                        @click.stop="addToBasket(props.product)"
                        :title="$t('Add to Cart')">
                        <div class="w-4 h-4 sm:w-5 sm:h-5">
                            <BagIcon />
                        </div>
                    </button>

                    <button
                        class="justify-center items-center flex bg-primary hover:bg-primary-600 text-white font-medium grow py-2 sm:py-2.5 rounded-xl shadow-xs hover:shadow transition active:scale-95 text-xs sm:text-sm"
                        @click.stop="buyNow">
                        {{ $t('Buy Now') }}
                    </button>
                </div>
            </div>
            <button v-else
                class="justify-center items-center flex border border-slate-200 bg-slate-50 py-2 sm:py-2.5 rounded-xl w-full cursor-not-allowed"
                disabled>
                <span class="text-slate-400 text-xs font-medium">{{ $t('Out of Stock') }}</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { HeartIcon as HeartIconOutline } from '@heroicons/vue/24/outline';
import { HeartIcon, StarIcon } from '@heroicons/vue/24/solid';
import { ArrowDownTrayIcon } from '@heroicons/vue/20/solid';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import BagIcon from '../icons/Bag.vue';
import { useAuth } from '../stores/AuthStore';
import { useBasketStore } from '../stores/BasketStore';
import { useMaster } from '../stores/MasterStore';

const router = useRouter();

const masterStore = useMaster();

const basketStore = useBasketStore();
const authStore = useAuth();

const toast = useToast();

const props = defineProps({
    product: Object
});

const cartQty = computed(() => basketStore.getProductCartQuantity(props.product?.id));

const orderData = {
    is_buy_now: false,
    product_id: props.product?.id,
    quantity: 1,
    size: null,
    color: null,
    unit: null
};

const addToBasket = (product) => {
    basketStore.addToCart(orderData, product);
};

const incrementQuantity = () => {
    const item = basketStore.getCartProduct(props.product?.id);
    if (item) {
        basketStore.incrementQuantity(item);
    } else {
        addToBasket(props.product);
    }
};

const decrementQuantity = () => {
    const item = basketStore.getCartProduct(props.product?.id);
    if (item) {
        basketStore.decrementQuantity(item);
    }
};

const buyNow = async () => {
    await basketStore.addToCart({
        product_id: props.product?.id,
        is_buy_now: true,
        quantity: 1,
        size: null,
        color: null,
        unit: null
    }, props.product);

    basketStore.buyNowShopId = props.product?.shop?.id;
};

const isFavorite = ref(props.product?.is_favorite);

const favoriteAddOrRemove = () => {
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }
    axios.post('/favorite-add-or-remove', {
        product_id: props.product.id
    }, {
        headers: {
            Authorization: authStore.token
        }
    }).then((response) => {
        props.product.is_favorite = !props.product.is_favorite
        isFavorite.value = response.data.data.product.is_favorite
        if (isFavorite.value === false) {
            toast.warning('Product removed from favorite', {
               position: masterStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
            });
        } else {
            toast.success('Product added to favorite', {
               position: masterStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
            });
        }
        authStore.favoriteRemove = true
        authStore.fetchFavoriteProducts();
    });
}

const showProductDetails = () => {
    if (props.product.quantity > 0) {
        router.push({ name: 'productDetails', params: { id: props.product.id } })
    }
}
</script>
