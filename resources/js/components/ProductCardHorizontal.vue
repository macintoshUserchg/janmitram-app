<template>
    <div class="rounded-2xl border border-slate-100 transition-all duration-300 group bg-white overflow-hidden relative shadow-xs hover:shadow-md hover:-translate-y-0.5 flex flex-col justify-between"
        :class="props.product?.quantity > 0 ? 'hover:border-primary/40' : ''">

        <div class="grid grid-cols-12 gap-0">
            <!-- Product Thumbnail -->
            <div class="col-span-5 relative overflow-hidden bg-slate-50/70 flex items-center justify-center p-2.5 cursor-pointer border-r border-slate-100"
                @click="showProductDetails"
                :class="props.product?.quantity > 0 ? '' : 'opacity-40'">
                <img :src="props.product?.thumbnail" class="w-full h-24 sm:h-28 group-hover:scale-105 transition duration-500 object-contain" loading="lazy"/>

                <!-- Discount Badge -->
                <div v-if="props.product?.discount_percentage > 0"
                    class="px-1.5 py-0.5 bg-gradient-to-r from-red-500 to-rose-600 rounded-full text-white text-[10px] font-bold absolute top-1.5 left-1.5 shadow-xs">
                    {{ props.product?.discount_percentage }}% {{ $t('OFF') }}
                </div>

                <!-- Digital Product Badge -->
                <span v-if="props.product?.is_digital == true"
                    class="absolute bottom-1.5 left-1.5 inline-flex items-center rounded bg-emerald-600/90 px-1.5 py-0.2 text-[9px] font-semibold text-white shadow-xs">
                    {{ $t('Digital') }}
                </span>
            </div>

            <!-- Product Details -->
            <div class="col-span-7 bg-white p-3 flex flex-col justify-between items-start">
                <div class="flex flex-col items-start gap-1 w-full cursor-pointer" @click="showProductDetails">

                    <div class="text-slate-900 text-xs sm:text-sm font-medium leading-snug line-clamp-2 w-full group-hover:text-primary transition-colors"
                        :class="props.product?.quantity > 0 ? '' : 'opacity-40'">
                        {{ props.product?.name }}
                    </div>

                    <div class="flex items-baseline gap-1.5 flex-wrap mt-0.5"
                        :class="props.product?.quantity > 0 ? '' : 'opacity-40'">
                        <div class="text-primary text-sm sm:text-base font-bold">
                            {{ masterStore.showCurrency(props.product?.discount_price > 0 ?
                                props.product?.discount_price : props.product?.price) }}
                        </div>
                        <div v-if="props.product?.unit?.name" class="text-slate-500 text-[10px] font-medium bg-slate-100 px-1 py-0.5 rounded">
                            {{ props.product?.unit?.name }}
                        </div>
                        <div v-if="props.product?.discount_price > 0"
                            class="text-slate-400 text-[11px] line-through">
                            {{ masterStore.showCurrency(props.product?.price) }}
                        </div>
                    </div>

                    <div class="flex justify-between items-center w-full text-[11px] text-slate-500 pt-1 border-t border-slate-100 mt-1">
                        <div class="flex items-center gap-1" :class="props.product?.quantity > 0 ? '' : 'opacity-40'">
                            <StarIcon class="w-3.5 h-3.5 text-amber-400" />
                            <span class="text-slate-800 font-semibold">{{ props.product?.rating?.toFixed(1) }}</span>
                            <span>({{ props.product?.total_reviews }})</span>
                        </div>

                        <div v-if="props.product?.quantity > 0" class="text-right text-slate-500 font-medium">
                            {{ props.product?.total_sold }} {{ $t('Sold') }}
                        </div>
                        <div v-else class="text-right text-red-500 font-semibold">
                            {{ $t('Out of Stock') }}
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="w-full mt-2.5 pt-1">
                    <div v-if="props.product?.quantity > 0">
                        <div v-if="props.product?.is_digital == false && cartQty > 0" 
                            class="flex items-center justify-between w-full bg-emerald-50 border border-emerald-200 rounded-lg p-0.5">
                            <button @click.stop="decrementQuantity" 
                                class="w-6 h-6 flex items-center justify-center bg-white text-emerald-700 hover:bg-emerald-600 hover:text-white rounded font-bold shadow-xs transition active:scale-95 text-xs">
                                -
                            </button>
                            <span class="text-emerald-800 font-bold text-[11px] px-1">{{ cartQty }} in Cart</span>
                            <button @click.stop="incrementQuantity" 
                                class="w-6 h-6 flex items-center justify-center bg-emerald-600 text-white hover:bg-emerald-700 rounded font-bold shadow-xs transition active:scale-95 text-xs">
                                +
                            </button>
                        </div>
                        <div v-else class="flex items-center gap-1.5 w-full">
                            <button v-if="props.product?.is_digital == false"
                                class="cursor-pointer w-7 h-7 bg-slate-50 hover:bg-primary hover:text-white text-slate-700 rounded-lg border border-slate-200 flex items-center justify-center transition shadow-xs shrink-0" 
                                @click.stop="addToBasket(props.product)"
                                :title="$t('Add to Cart')">
                                <div class="w-3.5 h-3.5">
                                    <BagIcon />
                                </div>
                            </button>
                            <button class="flex items-center justify-center gap-1 bg-primary hover:bg-primary-600 text-white font-medium text-xs py-1.5 px-2.5 rounded-lg shadow-xs transition active:scale-95 grow" 
                                @click.stop="buyNow">
                                <span>{{ $t('Buy Now') }}</span>
                                <ArrowRightIcon class="w-3 h-3" />
                            </button>
                        </div>
                    </div>
                    <div v-else class="text-slate-400 text-xs font-medium py-1 bg-slate-50 border border-slate-100 rounded-lg text-center">
                        {{ $t('Out of Stock') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ArrowRightIcon, StarIcon } from '@heroicons/vue/24/solid';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import BagIcon from '../icons/Bag.vue';
import { useAuth } from '../stores/AuthStore';
import { useBasketStore } from '../stores/BasketStore';
import { useMaster } from '../stores/MasterStore';

const router = useRouter();
const authStore = useAuth();
const basketStore = useBasketStore();
const masterStore = useMaster();

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

const buyNow = () => {
    basketStore.addToCart({
        product_id: props.product?.id,
        shop_id: props.product?.shop?.id,
        is_buy_now: true,
        quantity: 1,
        size: null,
        color: null,
        unit: null
    }, props.product);

    basketStore.buyNowShopId = props.product?.shop?.id;
};

const showProductDetails = () => {
    if (props.product.quantity > 0) {
        const query = props.product?.shop?.id ? { shop_id: props.product.shop.id } : {};
        router.push({ name: 'productDetails', params: { id: props.product.id }, query });
    }
};
</script>
