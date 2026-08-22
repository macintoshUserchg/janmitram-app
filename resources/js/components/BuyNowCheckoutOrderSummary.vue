<template>
    <div>
        <div class="p-6 bg-white rounded-2xl border border-slate-200">
            <div class="text-slate-950 text-xl font-medium leading-7">
                {{ $t("Order Summary") }}
            </div>

            <!-- Subtotal (MRP) -->
            <div class="my-3 flex justify-between gap-4">
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ $t("Item Total (MRP)") }}
                </div>
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ master.showCurrency(orderData.total_amount) }}
                </div>
            </div>

            <!-- Taxable Base (Without GST) -->
            <div v-if="orderData.taxable_base && orderData.taxable_base > 0" class="my-1 flex justify-between gap-4 text-xs text-slate-500">
                <div>
                    {{ $t("Price Without GST (Taxable Base)") }}
                </div>
                <div>
                    {{ master.showCurrency(orderData.taxable_base) }}
                </div>
            </div>

            <!-- Card Discount -->
            <div
                v-if="authStore.user && orderData.card_discount > 0"
                class="my-3 flex justify-between gap-4"
            >
                <div>
                    <div class="text-red-500 text-base font-normal leading-normal">
                        {{ $t("Card Discount") }}
                    </div>
                    <div v-if="orderData.base_discount > 0" class="text-[11px] text-slate-500">
                        ({{ master.showCurrency(orderData.base_discount) }} base + {{ master.showCurrency(orderData.tax_savings) }} GST saved)
                    </div>
                </div>
                <div class="text-slate-950 text-base font-normal leading-normal">
                    -{{ master.showCurrency(orderData.card_discount) }}
                </div>
            </div>

            <!-- Coupon Discount -->
            <div
                v-if="authStore.user && orderData.coupon_discount > 0"
                class="my-3 flex justify-between gap-4"
            >
                <div class="text-red-500 text-base font-normal leading-normal">
                    {{ $t("Coupon Discount") }}
                </div>
                <div class="text-slate-950 text-base font-normal leading-normal">
                    -{{ master.showCurrency(orderData.coupon_discount) }}
                </div>
            </div>

            <div
                v-if="authStore.user"
                class="w-full h-[0px] border-t border-dashed border-slate-300 my-2"
            ></div>

            <!-- Net Taxable Base -->
            <div v-if="orderData.net_taxable_base && orderData.net_taxable_base > 0 && (orderData.card_discount > 0 || orderData.coupon_discount > 0)" class="my-1 flex justify-between gap-4 text-xs text-slate-600">
                <div>
                    {{ $t("Net Taxable Value (After Discount)") }}
                </div>
                <div class="font-medium text-slate-800">
                    {{ master.showCurrency(orderData.net_taxable_base) }}
                </div>
            </div>

            <!-- Subtotal After Discount -->
            <div v-if="authStore.user && (orderData.card_discount > 0 || orderData.coupon_discount > 0)" class="my-3 flex justify-between gap-4">
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ $t("Subtotal After Discount") }}
                </div>
                <div class="text-slate-950 text-base font-normal leading-normal font-semibold">
                    {{
                        master.showCurrency(
                            (
                                orderData.total_amount -
                                orderData.coupon_discount -
                                orderData.card_discount
                            ).toFixed(2)
                        )
                    }}
                </div>
            </div>

            <!-- Shipping Charge -->
            <div class="my-3 flex justify-between gap-4">
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ $t("Shipping Charge") }}
                </div>
                <div class="text-slate-950 text-base font-normal leading-normal">
                    <span v-if="orderData.delivery_charge == 0" class="text-emerald-600 font-medium">FREE</span>
                    <span v-else>{{ master.showCurrency(orderData.delivery_charge) }}</span>
                </div>
            </div>

            <!-- GST & Taxes Info (Included in Prices) -->
            <div
                v-if="
                    orderData.all_vat_taxes?.length > 0 ||
                    orderData.order_tax_amount > 0
                "
                class="my-3 p-3 bg-slate-50 border border-slate-200 rounded-xl"
            >
                <div class="flex items-center justify-between text-xs text-slate-600 font-medium">
                    <span class="flex items-center gap-1.5">
                        <span class="text-emerald-600">🛡️</span>
                        <span>{{ $t("GST & Taxes (Included in Prices)") }}</span>
                    </span>
                    <span class="text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        {{ master.showCurrency(orderData.order_tax_amount) }}
                    </span>
                </div>

                <div v-if="orderData.all_vat_taxes?.length > 0" class="mt-2 pt-2 border-t border-slate-200/60 space-y-1">
                    <div
                        v-for="vatTax in orderData.all_vat_taxes"
                        :key="vatTax.id"
                        class="flex justify-between text-xs text-slate-500"
                    >
                        <span>{{ vatTax.name }} ({{ vatTax.percentage }}%):</span>
                        <span class="font-medium text-slate-700">{{ master.showCurrency(vatTax.amount) }}</span>
                    </div>
                </div>
            </div>

            <div class="w-full h-[0px] border border-slate-500"></div>

            <!-- Total Payable -->
            <div class="my-4 flex justify-between gap-4">
                <div
                    class="text-slate-950 text-lg font-medium leading-normal tracking-tight"
                >
                    {{ $t("Total Payable") }}
                </div>
                <div
                    class="text-slate-950 text-lg font-medium leading-normal tracking-tight"
                >
                    {{ master.showCurrency(orderData.payable_amount) }}
                </div>
            </div>

            <!-- Have a coupon -->
            <div v-if="authStore.user" class="p-4 mt-6 bg-slate-100 rounded-xl">
                <div class="text-black text-base font-normal leading-normal">
                    {{ $t("Have a coupon") }}?
                </div>

                <!-- Coupon Input -->
                <div class="relative mt-2">
                    <input
                        type="text"
                        v-model="coupon"
                        class="formInputCoupon pr-14 p-3"
                        :placeholder="$t('Enter coupon code')"
                        :class="hasCoupon ? 'text-green-500 pl-10' : ''"
                    />

                    <button
                        v-if="!hasCoupon"
                        class="bg-slate-700 absolute top-1/2 -translate-y-1/2 right-1.5 h-10 w-10 rounded flex justify-center items-center"
                        @click="ApplyCoupon"
                    >
                        <ArrowRightIcon class="w-6 h-6 text-white" />
                    </button>

                    <button
                        v-else
                        class="bg-slate-100 absolute top-1/2 -translate-y-1/2 right-1.5 h-10 w-10 rounded flex justify-center items-center"
                        @click="removeCoupon"
                    >
                        <TrashIcon class="w-6 h-6 text-red-500" />
                    </button>

                    <span class="absolute top-1/2 -translate-y-1/2 left-3">
                        <CheckCircleIcon
                            class="w-6 h-6 text-green-500"
                            v-if="hasCoupon"
                        />
                    </span>
                </div>
            </div>

            <!-- Have a membership card -->
            <div v-if="authStore.user" class="p-4 mt-4 bg-slate-100 rounded-xl">
                <div class="text-black text-base font-normal leading-normal">
                    {{ $t("Have a membership card") }}?
                </div>
                <div class="relative mt-2">
                    <input
                        type="text"
                        v-model="card"
                        class="formInputCoupon pr-14 p-3"
                        :placeholder="$t('Enter card number')"
                    />
                    <button
                        class="bg-slate-700 absolute top-1/2 -translate-y-1/2 right-1.5 h-10 w-10 rounded flex justify-center items-center"
                        @click="ApplyCard"
                    >
                        <ArrowRightIcon class="w-6 h-6 text-white" />
                    </button>
                </div>
                <div v-if="cardError" class="mt-1 text-red-500 text-sm">
                    {{ cardError }}
                </div>
            </div>
        </div>

        <!-- Unfulfillable lines: manual shop picker -->
        <div v-if="Object.keys(unfulfillable).length"
            class="p-4 mt-4 bg-white rounded-2xl border border-slate-200">
            <div class="text-slate-950 text-lg font-medium leading-7 mb-2">
                {{ $t("Choose delivery shop") }}
            </div>
            <div v-for="(cands, pid) in shopCandidates" :key="pid" class="mb-3">
                <p class="mb-1 text-sm text-slate-600">{{ $t("Product") }} #{{ pid }}</p>
                <label v-for="c in cands" :key="c.shop_id"
                    class="flex items-center gap-2 border rounded-lg p-2 mb-1 cursor-pointer has-[:checked]:border-primary">
                    <input type="radio" :name="'shop_' + pid" :value="c.shop_id" v-model="pickedShops[pid]" />
                    <span class="text-sm text-slate-700">{{ c.name }} — {{ c.distance_km }} km · {{ $t("Delivery") }} {{ master.showCurrency(c.delivery_charge) }}</span>
                </label>
            </div>
        </div>

        <!-- Fulfillment Mode Toggle -->
        <div class="p-4 mt-4 bg-slate-50 rounded-xl border border-slate-200">
            <label class="flex items-start gap-3 cursor-pointer">
                <input
                    type="checkbox"
                    v-model="fulfillFromNearest"
                    class="mt-1 w-4 h-4 text-primary rounded border-slate-300 focus:ring-primary"
                />
                <div class="flex-1">
                    <div class="text-sm font-semibold text-slate-900 flex items-center gap-1.5 flex-wrap">
                        <span>{{ $t("Auto-deliver from nearest shop") }}</span>
                        <span v-if="fulfillFromNearest" class="text-xs bg-emerald-100 text-emerald-800 font-medium px-2 py-0.5 rounded-full">
                            {{ $t("Recommended") }}
                        </span>
                        <span v-else class="text-xs bg-amber-100 text-amber-800 font-medium px-2 py-0.5 rounded-full">
                            {{ $t("Strict Mode") }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                        <template v-if="fulfillFromNearest">
                            {{ $t("Items will be dispatched from the closest franchised shop with available stock for fastest delivery.") }}
                        </template>
                        <template v-else>
                            {{ $t("Deliver strictly from the shop selected in your order. No dynamic re-allocation will occur.") }}
                        </template>
                    </p>
                </div>
            </label>
        </div>

        <div
            v-if="authStore.user &&
                !authStore.user?.account_verified &&
                master.orderPlaceAccountVerify
            "
            class="p-4 bg-white rounded-xl border border-slate-200 flex items-center justify-between mt-3"
        >
            <span class="animated-text">{{
                $t("Please verify your account")
            }}</span>
            <button
                class="p-2 border border-primary rounded-md bg-primary-50 text-primary text-sm font-medium"
                @click="showVerifyOtpModal = true"
            >
                {{ $t("Verify Now") }}
            </button>
        </div>

        <template v-if="authStore.user">
            <button
                v-if="!isProcessing"
                class="px-6 py-4 w-full mt-4 bg-primary rounded-[10px] text-white text-base font-medium"
                @click="processOrderConfirm"
            >
                {{ $t("Place Order") }}
            </button>
            <button
                v-else
                class="px-6 py-4 w-full mt-4 bg-primary-200 rounded-[10px] text-primary text-base font-semibold flex items-center justify-center gap-2"
                disabled
            >
                {{ $t("Processing") }}
                <LoadingSpin />
            </button>
        </template>

        <template v-else>
            <button
                v-if="!isProcessing"
                class="px-6 py-4 w-full mt-4 bg-primary rounded-[10px] text-white text-base font-medium"
                @click="processGuestOrderConfirm"
            >
                {{ $t("Place Order") }}
            </button>
            <button
                v-else
                class="px-6 py-4 w-full mt-4 bg-primary-200 rounded-[10px] text-primary text-base font-semibold flex items-center justify-center gap-2"
                disabled
            >
                {{ $t("Processing") }}
                <LoadingSpin />
            </button>
        </template>

        <!-- End Order Confirm Dialog Modal -->
        <OrderConfirmModal />

        <VerifyOtpModal
            :showModal="showVerifyOtpModal"
            @hideModal="showVerifyOtpModal = false"
        />
    </div>
</template>

<script setup>
import { ArrowRightIcon, TrashIcon } from "@heroicons/vue/24/outline";
import { CheckCircleIcon } from "@heroicons/vue/24/solid";
import { onMounted, ref, watch } from "vue";
import OrderConfirmModal from "../components/OrderConfirmModal.vue";
import ToastSuccessMessage from "../components/ToastSuccessMessage.vue";
import LoadingSpin from "../components/LoadingSpin.vue";

import { useToast } from "vue-toastification";
import { useAuth } from "../stores/AuthStore";
import { useBasketStore } from "../stores/BasketStore";
import { useMaster } from "../stores/MasterStore";
import { useGuestAddress } from "../stores/GuestAddressStore";

import { useRouter } from "vue-router";
import VerifyOtpModal from "./VerifyOtpModal.vue";
const router = new useRouter();

const basketStore = useBasketStore();
const master = useMaster();
const authStore = useAuth();
const guestAddressStore = useGuestAddress();

const toast = useToast();

const hasCoupon = ref(false);

const coupon = ref("");
const card = ref("");
const cardError = ref("");

const showVerifyOtpModal = ref(false);

const unfulfillable = ref({});
const shopCandidates = ref({});
const pickedShops = ref({});
const fulfillFromNearest = ref(true);

const buildAllocations = () =>
    Object.entries(pickedShops.value).map(([product_id, shop_id]) => ({
        product_id: Number(product_id),
        shop_id,
    }));

const resolveUnfulfillable = async (data) => {
    unfulfillable.value = data.unfulfillable || {};
    shopCandidates.value = { ...unfulfillable.value };

    // keep picks for lines that still need one; drop stale picks for resolved lines
    const keptPicks = {};
    Object.keys(unfulfillable.value).forEach((pid) => {
        if (pickedShops.value[pid] != null) {
            keptPicks[pid] = pickedShops.value[pid];
        }
    });
    pickedShops.value = keptPicks;

    const qtyByProduct = {};
    (basketStore.buyNowProduct?.products || []).forEach((p) => {
        qtyByProduct[p.id] = (qtyByProduct[p.id] || 0) + p.quantity;
    });

    const lines = Object.keys(unfulfillable.value).map((product_id) => ({
        product_id: Number(product_id),
        quantity: qtyByProduct[product_id] || 1,
    }));

    // guests have no saved address_id to refresh candidates with;
    // the unfulfillable seed already contains the candidate lists
    const addressId = basketStore.address?.id;
    if (!lines.length || !addressId) return;

    try {
        const res = await axios.post(
            "/shop-candidates",
            {
                address_id: addressId,
                products: lines,
            },
            {
                headers: {
                    Authorization: authStore.token,
                },
            }
        );
        shopCandidates.value = res.data.data.shop_candidates;
    } catch (error) {
        // keep the unfulfillable seed so the picker still renders
    }
};

const props = defineProps({
    note: String,
    paymentMethod: String,
    isDigitalProduct: Boolean,
});

const orderData = ref({
    total_amount: 0,
    delivery_charge: 0,
    coupon_discount: 0,
    card_discount: 0,
    payable_amount: 0,
    order_tax_amount: 0,
});

onMounted(() => {
    coupon.value = basketStore.coupon_code;
    fetchBuyNowCartCheckout();
});

watch(
    () => basketStore.isLoadingCart,
    (loading) => {
        if (!loading) {
            fetchBuyNowCartCheckout();
        }
    }
);

watch(
    () => basketStore.address,
    () => {
        fetchBuyNowCartCheckout()
    }
);

const getBuyNowShopIds = () => {
    const sid = basketStore.buyNowShopId || (typeof localStorage !== 'undefined' && localStorage.getItem('buyNowShopId') ? Number(localStorage.getItem('buyNowShopId')) : null);
    return sid ? [sid] : [];
};

const fetchBuyNowCartCheckout = () => {
    axios
        .post(
            "/cart/checkout",
            {
                shop_ids: getBuyNowShopIds(),
                is_buy_now: true,
                coupon_code: coupon.value,
                address_id: basketStore.address ? basketStore.address.id : null ,
            },
            {
                headers: {
                    Authorization: authStore.token,
                    "X-Guest-Token": authStore.access_token,
                },
            }
        )
        .then((response) => {
            orderData.value = response.data.data.checkout;
            basketStore.buyNowProduct = response.data.data.checkout_items[0];

            hasCoupon.value = response.data.data.apply_coupon;

            if (hasCoupon.value && coupon.value.length > 0) {
                toast.success(response.data.message, {
                    position:
                        master.langDirection === "rtl"
                            ? "bottom-right"
                            : "bottom-left",
                });
                basketStore.coupon_code = coupon.value;
            } else if (!hasCoupon.value && coupon.value.length > 0) {
                toast.error(response.data.message, {
                    position:
                        master.langDirection === "rtl"
                            ? "bottom-right"
                            : "bottom-left",
                });
                basketStore.coupon_code = "";
            }
        })
        .catch((error) => {
            toast.error(error.response.data.message, {
                position:
                    master.langDirection === "rtl"
                        ? "bottom-right"
                        : "bottom-left",
            });
        });
};

const ApplyCoupon = () => {
    if (coupon.value.length > 0) {
        fetchBuyNowCartCheckout();
    }
};

const removeCoupon = () => {
    coupon.value = "";
    hasCoupon.value = false;
    basketStore.coupon_code = "";
    fetchBuyNowCartCheckout();
};

const content = {
    component: ToastSuccessMessage,
    props: {
        title: "Order Placed",
        message: "Your order has been placed successfully.",
    },
};

const isProcessing = ref(false);
const processOrderConfirm = () => {
    if (!basketStore.address) {
        toast.error("Please select shipping address");
        return;
    }

    if (!basketStore.address?.latitude || !basketStore.address?.longitude) {
        toast.error(
            "Please set your delivery location on the map before placing the order",
            {
                position:
                    master.langDirection === "rtl"
                        ? "bottom-right"
                        : "bottom-left",
            }
        );
        router.push({ name: "manage-address" });
        return;
    }

    if (props.isDigitalProduct == true && props.paymentMethod == "cash") {
        toast.error("Please select payment method", {
            position:
                master.langDirection === "rtl" ? "bottom-right" : "bottom-left",
        });
        return;
    }

    if (props.paymentMethod == null || props.paymentMethod == "card") {
        toast.error("Please select payment option", {
            position:
                master.langDirection === "rtl" ? "bottom-right" : "bottom-left",
        });
        return;
    }

    if (basketStore.buyNowProduct) {
        isProcessing.value = true;
        axios
            .post(
                "/place-order",
                {
                    shop_ids: getBuyNowShopIds(),
                    address_id: basketStore.address.id,
                    payment_method: props.paymentMethod,
                    coupon_code: coupon.value,
                    card_number: card.value,
                    note: props.note,
                    is_buy_now: true,
                    allocations: buildAllocations(),
                    fulfill_from_nearest_shop: fulfillFromNearest.value,
                },
                {
                    headers: {
                        Authorization: authStore.token,
                        "X-Guest-Token": authStore.access_token,
                    },
                }
            )
            .then((response) => {
                unfulfillable.value = {};
                shopCandidates.value = {};
                pickedShops.value = {};
                isProcessing.value = false;
                toast(content, {
                    type: "default",
                    hideProgressBar: true,
                    icon: false,
                    position:
                        master.langDirection === "rtl"
                            ? "bottom-right"
                            : "bottom-left",
                    toastClassName: "vue-toastification-alert",
                    timeout: 2000,
                });
                orderData.value.total_amount = 0;
                orderData.value.delivery_charge = 0;
                orderData.value.coupon_discount = 0;
                orderData.value.payable_amount = 0;
                basketStore.buyNowProduct = null;
                basketStore.coupon_code = "";
                let paymentUrl = response.data.data.order_payment_url;

                if (paymentUrl != null) {
                    openPaymentPopupWindow(paymentUrl);
                    return;
                } else {
                    basketStore.showOrderConfirmModal = true;
                }
            })
            .catch((error) => {
                if (
                    error.response?.status === 422 &&
                    error.response.data?.data?.unfulfillable
                ) {
                    resolveUnfulfillable(error.response.data.data);
                    isProcessing.value = false;
                    return;
                }

                toast.error(error.response.data.message, {
                    position:
                        master.langDirection === "rtl"
                            ? "bottom-right"
                            : "bottom-left",
                });
                isProcessing.value = false;
            });
    } else {
        toast.error("Please select at least one product", {
            position:
                master.langDirection === "rtl" ? "bottom-right" : "bottom-left",
        });
    }
};

const processGuestOrderConfirm = () => {
    if (props.paymentMethod == null || props.paymentMethod == "card") {
        toast.error("Please select payment method", {
            position:
                master.langDirection === "rtl" ? "bottom-right" : "bottom-left",
        });
        return;
    }

    if (basketStore.buyNowProduct) {
        isProcessing.value = true;
        axios
            .post(
                "/place-order",
                {
                    currency_id: master.selectedCurrency.id,
                    shop_ids: getBuyNowShopIds(),
                    payment_method: props.paymentMethod,
                    coupon_code: coupon.value,
                    card_number: card.value,
                    note: props.note,
                    is_buy_now: true,
                    name: guestAddressStore.name,
                    email: guestAddressStore.email,
                    phone: guestAddressStore.phone,
                    state: guestAddressStore.state,
                    city: guestAddressStore.city,
                    post_code: guestAddressStore.post_code,
                    area_id: guestAddressStore.area_id,
                    address_line: guestAddressStore.address_line,
                    address_type: guestAddressStore.address_type,
                    latitude: guestAddressStore.latitude,
                    longitude: guestAddressStore.longitude,
                    allocations: buildAllocations(),
                    fulfill_from_nearest_shop: fulfillFromNearest.value,
                },
                {
                    headers: {
                        Authorization: authStore.token,
                        "X-Guest-Token": authStore.access_token,
                    },
                }
            )
            .then((response) => {
                unfulfillable.value = {};
                shopCandidates.value = {};
                pickedShops.value = {};
                isProcessing.value = false;
                toast(content, {
                    type: "default",
                    hideProgressBar: true,
                    icon: false,
                    position:
                        master.langDirection === "rtl"
                            ? "bottom-right"
                            : "bottom-left",
                    toastClassName: "vue-toastification-alert",
                    timeout: 2000,
                });
                orderData.value.total_amount = 0;
                orderData.value.delivery_charge = 0;
                orderData.value.coupon_discount = 0;
                orderData.value.payable_amount = 0;
                basketStore.buyNowProduct = null;
                guestAddressStore.clearGuestAddress();
                basketStore.coupon_code = "";
                let paymentUrl = response.data.data.order_payment_url;

                if (paymentUrl != null) {
                    openPaymentPopupWindow(paymentUrl);
                    return;
                } else {
                    basketStore.showOrderConfirmModal = true;
                }
            })
            .catch((error) => {
                if (
                    error.response?.status === 422 &&
                    error.response.data?.data?.unfulfillable
                ) {
                    resolveUnfulfillable(error.response.data.data);
                    isProcessing.value = false;
                    return;
                }

                guestAddressStore.errors = error.response.data.errors;
                toast.error(error.response.data.message, {
                    position:
                        master.langDirection === "rtl"
                            ? "bottom-right"
                            : "bottom-left",
                });
                isProcessing.value = false;
            });
    } else {
        toast.error("Please select at least one product", {
            position:
                master.langDirection === "rtl" ? "bottom-right" : "bottom-left",
        });
    }
};

const openPaymentPopupWindow = (url) => {
    let winWidth = 700;
    let winHeight = 700;
    let left = screen.width / 2 - winWidth / 2;
    let top = screen.height / 2 - winHeight / 2;

    let options =
        "popup,resizable,height=" +
        winHeight +
        ",width=" +
        winWidth +
        ",top=" +
        top +
        ",left=" +
        left;

    let win = window.open(url, "JanmitramPaymentWindow", options);

    if (win) {
        win.focus();
    }

    let isCompleted = false;

    const cleanup = () => {
        clearInterval(intervalID);
        window.removeEventListener("message", handlePaymentMessage);
        window.removeEventListener("storage", handleStorageEvent);
    };

    const handleSuccess = () => {
        if (isCompleted) return;
        isCompleted = true;
        cleanup();
        try {
            if (win && !win.closed) win.close();
        } catch (e) {}
        basketStore.showOrderConfirmModal = true;
    };

    const handleCancel = (msg = "Payment Canceled") => {
        if (isCompleted) return;
        isCompleted = true;
        cleanup();
        try {
            if (win && !win.closed) win.close();
        } catch (e) {}
        basketStore.orderPaymentCancelModal = true;
        toast.error(msg, {
            position:
                master.langDirection === "rtl" ? "bottom-right" : "bottom-left",
        });
        router.push({ name: "home" });
    };

    const handlePaymentMessage = (event) => {
        if (event.data?.type === "PAYMENT_SUCCESS") {
            handleSuccess();
        } else if (event.data?.type === "PAYMENT_FAILED") {
            handleCancel(event.data?.error);
        }
    };

    const handleStorageEvent = (event) => {
        if (event.key === "janmitram_payment_event" && event.newValue) {
            try {
                const data = JSON.parse(event.newValue);
                if (data.status === "success") {
                    handleSuccess();
                } else if (data.status === "failed") {
                    handleCancel(data.error);
                }
            } catch (e) {}
        }
    };

    window.addEventListener("message", handlePaymentMessage);
    window.addEventListener("storage", handleStorageEvent);

    var intervalID = setInterval(trackURLChanges, 800);

    function trackURLChanges() {
        try {
            if (win && win.closed && !isCompleted) {
                handleCancel();
                return;
            }

            if (win && win.location) {
                const pathname = win.location.pathname || "";
                if (
                    pathname.includes("/payment/success") ||
                    pathname.includes("/order/payment/success")
                ) {
                    handleSuccess();
                    return;
                }
                if (
                    pathname.includes("/payment/cancel") ||
                    pathname.includes("/order/payment/cancel")
                ) {
                    handleCancel();
                    return;
                }
            }
        } catch (error) {}
    }

    // Timeout fallback after 4 minutes
    setTimeout(() => {
        if (!isCompleted) {
            cleanup();
            try {
                if (win && !win.closed) win.close();
            } catch (e) {}
        }
    }, 240000);
};

const ApplyCard = () => {
    if (card.value.length > 0) {
        fetchCardApply();
    }
};

const fetchCardApply = () => {
    axios
        .post(
            "/cart/checkout",
            {
                shop_ids: getBuyNowShopIds(),
                is_buy_now: true,
                card_number: card.value,
                address_id: basketStore.address ? basketStore.address.id : null,
            },
            {
                headers: {
                    Authorization: authStore.token,
                    "X-Guest-Token": authStore.access_token,
                },
            }
        )
        .then((response) => {
            orderData.value = response.data.data.checkout;
            cardError.value = response.data.data.checkout.card_error || "";

            if (cardError.value) {
                toast.error(cardError.value, {
                    position:
                        master.langDirection === "rtl"
                            ? "bottom-right"
                            : "bottom-left",
                });
            } else {
                hasCoupon.value = false;
                coupon.value = "";
                basketStore.coupon_code = "";
                toast.success(response.data.message, {
                    position:
                        master.langDirection === "rtl"
                            ? "bottom-right"
                            : "bottom-left",
                });
            }
        })
        .catch((error) => {
            toast.error(error.response.data.message, {
                position:
                    master.langDirection === "rtl"
                        ? "bottom-right"
                        : "bottom-left",
            });
        });
};
</script>

<style scoped>
.formInputCoupon {
    @apply rounded-lg border border-slate-200 focus:border-primary w-full outline-none text-base font-normal leading-normal placeholder:text-slate-400;
}

.animated-text {
    display: inline-block;
    background: linear-gradient(
        90deg,
        red,
        orange,
        indigo,
        yellow,
        green,
        blue,
        violet
    );
    background-size: 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: colorChange 3s linear infinite;
}

@keyframes colorChange {
    0% {
        background-position: 100%;
    }

    100% {
        background-position: 0%;
    }
}
</style>
