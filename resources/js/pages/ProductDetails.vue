<template>
    <div class="main-container">
        <div v-show="!isLoading" class="grid grid-cols-1 xl:grid-cols-4">
            <div class="xl:col-span-3 col-span-1 lg:pr-6">
                <div class="flex items-center gap-2 overflow-hidden pt-4">
                    <router-link to="/" class="w-6 h-6">
                        <HomeIcon class="w-5 h-5 text-slate-600" />
                    </router-link>

                    <div class="grow w-full overflow-hidden">
                        <div class="space-x-1 text-slate-600 text-sm font-normal truncate">
                            <router-link to="/">{{ $t("Home") }}</router-link>
                            <span>/</span>
                            <span>{{ product.name }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap lg:flex-nowrap gap-4 mt-6">
                    <div class="lg:w-[480px] w-full">
                        <div class="w-full">
                            <div class="bg-slate-50 rounded-xl border border-slate-100 px-6 pb-2 pt-2">
                                <swiper :spaceBetween="10" :thumbs="{ swiper: thumbsSwiper }"
                                    :pagination="{ clickable: true, dynamicBullets: true }" :modules="modules"
                                    class="product-details-slider">
                                    <swiper-slide v-for="thumbnail in product.thumbnails" :key="thumbnail.id"
                                        class="max-h-[448px] h-auto mb-6">
                                        <div v-if="thumbnail.thumbnail" class="zoom-container h-full"
                                            @mousemove="handleMouseMove" @mouseleave="resetZoom"
                                            @touchstart="handleMouseMove" @touchmove="handleMouseMove"
                                            @touchend="resetZoom">
                                            <img :src="thumbnail.thumbnail" alt="thumbnail"
                                                class="zoom-image h-full w-full object-contain" />
                                        </div>
                                        <div v-else class="h-full w-full bg-slate-200 flex justify-center items-center">
                                            <video v-if="thumbnail.type == 'file'" controls class="w-full">
                                                <source :src="thumbnail.url" type="video/mp4">
                                            </video>
                                            <div v-else v-html="thumbnail.url" class="w-full overflow-hidden"
                                                ref="iframeContainer"></div>
                                        </div>
                                    </swiper-slide>
                                </swiper>
                            </div>
                            <div class="px-1 mt-2">
                                <swiper @swiper="setThumbsSwiper" :spaceBetween="10" :slidesPerView="4" :freeMode="true"
                                    :navigation="true" :watchSlidesProgress="true" :modules="modules"
                                    class="product-details-thumbnail">
                                    <swiper-slide v-for="thumbnail in product.thumbnails" :key="thumbnail.id">
                                        <img v-if="thumbnail.thumbnail" :src="thumbnail.thumbnail" alt=""
                                            class="h-full w-full object-cover" />

                                        <div v-else class="h-full w-full bg-slate-200 flex justify-center items-center">
                                            <video v-if="thumbnail.type == 'file'" class="h-full w-full">
                                                <source :src="thumbnail.url" type="video/mp4">
                                            </video>
                                            <div v-else
                                                class="h-full w-full overflow-hidden flex justify-center items-center">
                                                <img :src="'/assets/icons/video-player.svg'" alt="" width="70"
                                                    height="70">
                                            </div>
                                        </div>
                                    </swiper-slide>
                                </swiper>
                            </div>
                        </div>
                    </div>

                    <div class="w-full sm:w-auto">
                        <!-- Flash Sale  -->
                        <div v-if="flashSale"
                            class="bg-slate-100 mb-3 sm:mb-6 rounded-lg sm:rounded-[44px] flex items-center justify-start gap-2 sm:gap-5 overflow-hidden flex-col sm:flex-row">
                            <div
                                class="px-4 sm:px-8 py-2 bg-gradient-to-l from-primary to-primary-800 w-full sm:w-auto">
                                <div class="text-white text-sm sm:text-base font-bold leading-normal">
                                    {{ $t("Flash Sale") }}
                                </div>
                            </div>

                            <div class="h-full flex justify-center items-center flex-wrap pb-2 sm:pb-0">
                                <div class="text-center text-primary text-sm font-normal leading-tight pr-2">
                                    {{ $t("Ending in") }}
                                </div>

                                <!-- Days, Hours, Minutes, Seconds -->
                                <div class="flex justify-center items-center gap-1 text-white">
                                    <div v-if="endDay > 0" class="p-1 justify-center items-center gap-1 inline-flex">
                                        <div
                                            class="text-center text-primary text-base font-semibold font-['Inter'] leading-none">
                                            {{ endDay }}
                                        </div>
                                        <div
                                            class="text-center text-[#687387] text-[9.14px] font-normal font-['Inter'] leading-none">
                                            {{ $t("Days") }}
                                        </div>
                                    </div>

                                    <span v-if="endDay > 0" class="text-black text-base font-bold">:</span>
                                    <div class="p-1 justify-center items-center gap-1 inline-flex">
                                        <div class="text-center text-primary text-base font-semibold font-['Inter']">
                                            {{ endHour }}
                                        </div>
                                        <div
                                            class="text-center text-[#687387] text-[9.14px] font-normal font-['Inter'] leading-none">
                                            {{ $t("Hours") }}
                                        </div>
                                    </div>

                                    <span class="text-black text-base font-bold">:</span>
                                    <div class="p-1 justify-center items-center gap-1 inline-flex">
                                        <div class="text-center text-primary text-base font-semibold font-['Inter']">
                                            {{ endMinute }}
                                        </div>
                                        <div
                                            class="text-center text-[#687387] text-[9.14px] font-normal font-['Inter'] leading-none">
                                            {{ $t("Minutes") }}
                                        </div>
                                    </div>

                                    <span v-if="endDay <= 0" class="text-black text-base font-bold">:</span>
                                    <div v-if="endDay <= 0" class="p-1 justify-center items-center gap-1 inline-flex">
                                        <div class="text-center text-primary text-base font-semibold font-['Inter']">
                                            {{ endSecond }}
                                        </div>
                                        <div
                                            class="text-center text-[#687387] text-[9.14px] font-normal font-['Inter'] leading-none">
                                            {{ $t("Seconds") }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Flash Sale -->

                        <!-- Brand -->
                        <span class="text-primary text-xs font-normal leading-none px-1.5 py-1 bg-primary-50 rounded">
                            {{ product.brand ?? "Unknown Brand" }}
                        </span>

                        <!-- Title -->
                        <div class="mt-3 text-slate-950 text-2xl font-medium leading-normal">
                            {{ product.name }}
                        </div>

                        <!-- Short Description -->
                        <div class="mt-2 text-slate-700 text-base font-normal leading-normal">
                            {{ product.short_description }}
                        </div>

                        <!-- Rating  review, sold and share -->
                        <div class="py-5 flex flex-wrap justify-start items-center gap-4 border-b border-slate-200">
                            <!-- rating -->
                            <div class="flex items-center gap-2">
                                <div class="flex">
                                    <StarIcon v-for="i in 5" :key="i" class="w-6 h-6 2xl:block hidden" :class="i <= product.rating ? 'text-amber-500' : 'text-gray-300'
                                        " />
                                </div>
                                <div class="text-slate-800 text-base font-bold">
                                    {{ product.rating }}
                                </div>
                                <!-- Review -->
                                <div class="text-slate-500 text-base font-normal">
                                    {{ product.total_reviews }} {{ $t("Review") }}
                                </div>
                            </div>

                            <div class="w-[1px] h-4 bg-slate-200"></div>

                            <!-- Sold -->
                            <div class="text-slate-800 text-base font-normal leading-normal">
                                {{ product.total_sold }} {{ $t("Sold") }}
                            </div>

                            <div class="w-[1px] h-4 bg-slate-200"></div>

                            <!-- Share -->
                            <Menu as="div" class="relative inline-block text-left">
                                <div>
                                    <MenuButton class="flex items-center gap-2 border-none">
                                        <ShareIcon class="w-[18px] text-slate-600" />
                                        <span class="text-slate-800 text-base font-normal leading-normal">
                                            {{ $t("Share") }}
                                        </span>
                                    </MenuButton>
                                </div>

                                <transition enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95">
                                    <MenuItems
                                        class="absolute right-0 tr z-10 mt-2 w-56 origin-top rounded-md bg-white ring-1 shadow-lg ring-black/5 focus:outline-hidden">
                                        <div class="py-1 divide-y divide-gray-100">
                                            <MenuItem v-slot="{ active }" v-for="social in shareOptions"
                                                :key="social.name" class="cursor-pointer" @click="share(social.name)">
                                            <div
                                                class="flex items-center gap-2 justify-between px-4 py-2 hover:bg-slate-100 transition-all duration-200">
                                                <div class="flex items-center gap-1.5">
                                                    <div class="w-7 h-7 p-1.5 flex justify-center items-center text-white rounded-full"
                                                        :class="`bg-[${social.color}]`">
                                                        <FontAwesomeIcon :icon="social.icon" class="w-full h-full" />
                                                    </div>
                                                    <span class="capitalize">{{ social.name }}</span>
                                                </div>
                                                <div
                                                    class="w-5 h-5 p-1 flex justify-center items-center bg-slate-200 rounded-full rotate-45">
                                                    <FontAwesomeIcon :icon="faArrowUp"
                                                        class="w-full h-full text-slate-500" />
                                                </div>
                                            </div>
                                            </MenuItem>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>

                            <div class="w-[1px] h-4 bg-slate-200"></div>

                            <!-- Heart Icon -->
                            <button class="border-none" @click="favoriteAddOrRemove">
                                <HeartIcon v-if="!product.is_favorite" class="w-6 h-6 text-slate-600" />
                                <HeartIconFill v-else class="w-6 h-6 text-red-500" />
                            </button>
                        </div>

                        <!-- Price part -->
                        <div class="flex items-center gap-3 py-4 border-b border-slate-200 flex-wrap">
                            <!-- discount Price -->
                            <div class="text-primary text-3xl font-bold leading-9">
                                {{ masterStore.showCurrency(parseFloat(productPrice).toFixed(2)) }}
                            </div>

                            <!-- Unit Badge -->
                            <div v-if="product.unit?.name"
                                class="px-2.5 py-1 bg-slate-100 border border-slate-200 text-slate-800 text-sm font-bold rounded-lg">
                                {{ product.unit?.name }}
                            </div>

                            <!-- Price -->
                            <div v-if="product.discount_price > 0"
                                class="text-slate-400 text-2xl font-normal line-through leading-loose">
                                {{ masterStore.showCurrency(parseFloat(mainPrice).toFixed(2)) }}
                            </div>

                            <!-- discount -->
                            <div v-if="discountPercentage > 0"
                                class="px-2 py-1 bg-red-500 rounded-2xl text-white text-base font-medium">
                                {{ discountPercentage }}% {{ $t("OFF") }}
                            </div>
                        </div>

                        <!-- Unit Specification -->
                        <div v-if="product.unit?.name && product.is_digital == false" class="flex items-center gap-3 py-4 border-b border-slate-100">
                            <div class="w-[40px] md:w-[88px] text-slate-600 text-base font-normal leading-normal">
                                {{ $t("Unit") }}
                            </div>
                            <div class="px-3 py-1 bg-slate-100 border border-slate-200 rounded-md text-slate-900 text-sm font-bold">
                                {{ product.unit?.name }}
                            </div>
                        </div>

                        <!-- Size -->
                        <div v-if="product.sizes?.length > 0 && product.is_digital == false" class="flex items-center gap-3 py-4">
                            <div class="w-[40px] md:w-[88px] text-slate-600 text-base font-normal leading-normal">
                                {{ $t("Size") }}
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <div v-for="size in product.sizes" :key="size.id" class="relative">
                                    <input type="radio" name="size" :id="'size-' + size.id" class="peer hidden"
                                        :value="size.id" v-model="formData.size" />
                                    <label :for="'size-' + size.id"
                                        class="min-w-11 w-auto h-9 flex justify-center items-center border-2 border-slate-200 rounded-md cursor-pointer peer-checked:border-primary peer-checked:bg-primary-100 px-2">
                                        {{ size.name }}
                                    </label>
                                </div>
                                <div v-if="!product.sizes" class="text-slate-500 text-base font-normal">
                                    {{ $t("N/A") }}
                                </div>
                            </div>
                        </div>

                        <!-- Color -->
                        <div v-if="product.colors?.length > 0 && product.is_digital == false" class="flex items-center gap-3 py-4">
                            <div class="w-[40px] md:w-[88px] text-slate-600 text-base font-normal leading-normal">
                                {{ $t("Color") }}
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <div v-for="color in product.colors" :key="color.id" class="relative">
                                    <input type="radio" name="color" :id="'color-' + color.id" class="peer hidden"
                                        :value="color.id" v-model="formData.color" />
                                    <label :for="'color-' + color.id"
                                        class="px-2 py-1 flex justify-center items-center border-2 border-slate-200 rounded-md cursor-pointer peer-checked:border-primary peer-checked:bg-primary-100">
                                        {{ color.name }}
                                    </label>
                                </div>

                                <div v-if="!product.colors" class="text-slate-500 text-base font-normal">
                                    {{ $t("N/A") }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 w-full flex-wrap sm:flex-nowrap pt-2">
                            <!-- Quantity Increase Or Decrease -->
                            <div v-if="cartProduct"
                                class="p-1.5 rounded-xl border border-slate-200 inline-flex items-center gap-3 bg-slate-50 shrink-0">
                                <button class="bg-white shadow-xs p-1.5 rounded-lg hover:bg-slate-100 transition active:scale-95" @click="decrementQty">
                                    <MinusIcon class="w-5 h-5 text-slate-800" />
                                </button>

                                <div
                                    class="min-w-6 flex items-center justify-center text-center text-slate-950 text-base font-bold">
                                    {{ cartProduct.quantity }}
                                </div>

                                <button class="bg-primary text-white p-1.5 rounded-lg hover:bg-primary-600 transition active:scale-95 shadow-xs" @click="incrementQty">
                                    <PlusIcon class="w-5 h-5 text-white" />
                                </button>
                            </div>

                            <!-- Add to Cart -->
                            <button v-if="!cartProduct && product?.is_digital == false"
                                class="flex-1 min-w-[130px] justify-center items-center text-primary bg-primary-50/50 hover:bg-primary-100/70 flex gap-2 px-4 sm:px-6 py-3 sm:py-3.5 rounded-xl border border-primary/40 font-semibold text-sm sm:text-base transition active:scale-95 shadow-xs"
                                @click="addToCart">
                                <div class="w-5 h-5">
                                    <BagIcon />
                                </div>
                                <span>{{ $t("Add to Cart") }}</span>
                            </button>

                            <!-- Buy Now -->
                            <button
                                class="flex-1 min-w-[130px] text-white bg-primary hover:bg-primary-600 px-4 sm:px-6 py-3 sm:py-3.5 rounded-xl border border-primary font-semibold text-sm sm:text-base transition active:scale-95 shadow-xs flex items-center justify-center"
                                :class="product?.is_digital == true ? 'my-2' : ''"
                                @click="buyNow">
                                <span>{{ $t("Buy Now") }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="block xl:hidden w-full pt-6 border-slate-200">
                    <ProductDetailsRightSide :product="product" :popularProducts="popularProducts" />
                </div>

                <div class="flex items-center gap-8 flex-wrap border-b mt-3 mb-4 xl:my-6">
                    <button class="py-3 transition text-base font-medium leading-normal border-b" :class="aboutProduct
                        ? 'text-primary border-primary'
                        : 'text-slate-600 border-transparent'" @click="aboutProduct = true; review = false;">
                        {{ $t("About Product") }}
                    </button>

                    <button class="py-3 transition text-base font-medium leading-normal border-b" :class="review
                        ? 'text-primary border-primary'
                        : 'text-slate-600 border-transparent'
                        " @click="showReview()">
                        {{ $t("Reviews") }}
                    </button>
                </div>

                <!-- About Product -->
                <div v-if="aboutProduct" class="description">
                    <div class="prose max-w-none w-full m-0" v-html="product.description"></div>
                </div>

                <!-- Reviews -->
                <div v-if="review" class="">
                    <div class="text-slate-950 text-lg lg:text-2xl font-medium leading-loose mb-4">
                        {{ $t("Rating and Review") }}
                    </div>

                    <!-- Review Rating percentage -->
                    <div class="max-w-2xl">
                        <ReviewRatings :reviewRatings="averageRatings.percentages"
                            :averageRating="averageRatings?.rating" :totalReview="averageRatings.total_review" />
                    </div>

                    <!-- Reviews -->
                    <div class="border-t border-slate-200 mt-6">
                        <div class="mt-4 lg:mt-6 text-slate-950 text-lg lg:text-2xl font-medium leading-loose">
                            {{ $t("Reviews") }}
                        </div>

                        <div class="space-y-6 mt-6">
                            <Review v-for="review in reviews" :key="review.id" :review="review" />
                        </div>

                        <!-- pagination's -->
                        <div class="flex justify-between items-center w-full mt-8 gap-4 flex-wrap">
                            <div class="text-slate-800 text-base font-normal leading-normal">
                                {{ $t("Showing") }} {{ perPage * (currentPage - 1) + 1 }}
                                {{ $t("to") }}
                                {{ perPage * (currentPage - 1) + reviews.length }}
                                {{ $t("of") }} {{ totalReviews }} {{ $t("results") }}
                            </div>
                            <div>
                                <vue-awesome-paginate :total-items="totalReviews" :items-per-page="perPage"
                                    type="button" :max-pages-shown="3" v-model="currentPage"
                                    :hide-prev-next-when-ends="true" @click="onClickHandler" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side -->
            <div class="hidden xl:block col-span-1 w-full pt-6 h-full xl:pt-12 border-slate-200 xl:pb-6"
                :class="masterStore.langDirection == 'rtl' ? 'xl:pr-8 xl:border-r' : 'xl:pl-8 xl:border-l'">
                <ProductDetailsRightSide :product="product" :popularProducts="popularProducts" />
            </div>
        </div>

        <!-- Similar Products -->
        <div v-if="relatedProducts.length > 0 && !isLoading">
            <div class="mt-4 xl:mt-6 text-slate-800 text-lg md:text-2xl lg:text-3xl font-bold leading-9">
                {{ $t("Similar Products") }}
            </div>

            <div
                class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 sm:gap-6 items-start my-6">
                <div v-for="product in relatedProducts" :key="product.id">
                    <ProductCard :product="product" />
                </div>
            </div>
        </div>

        <!-- page loader -->
        <div v-if="isLoading" class="grid grid-cols-1 xl:grid-cols-4">
            <div class="xl:col-span-3 col-span-1 lg:pr-6">
                <div class="flex items-center gap-2 overflow-hidden pt-6">
                    <SkeletonLoader class="w-40 h-4" />
                </div>
                <div class="flex flex-wrap lg:flex-nowrap gap-4 mt-6">
                    <div class="lg:w-[480px] w-full lg:shrink-0">
                        <SkeletonLoader class="w-full h-52 md:h-80 rounded-lg" />

                        <div class="flex flex-grow gap-3 mt-4">
                            <SkeletonLoader v-for="i in 4" class="w-20 h-16 grow" />
                        </div>
                        <div class="flex flex-col gap-3 mt-6">
                            <SkeletonLoader class="w-11/12 h-3 rounded-xl" />
                            <SkeletonLoader class="w-10/12 h-3 rounded-xl" />
                            <SkeletonLoader class="w-11/12 h-3 rounded-xl" />
                            <SkeletonLoader class="w-full h-3 rounded-xl" />
                        </div>
                        <div class="flex flex-col gap-4 mt-4">
                            <SkeletonLoader v-for="i in 3" class="w-full h-20 rounded-lg" />
                        </div>
                    </div>

                    <div class="w-full pb-4">
                        <div class="flex flex-col gap-3">
                            <SkeletonLoader class="w-11/12 h-3 rounded-xl" />
                            <SkeletonLoader class="w-10/12 h-3 rounded-xl" />
                            <SkeletonLoader class="w-11/12 h-3 rounded-xl" />
                            <SkeletonLoader class="w-full h-3 rounded-xl" />
                        </div>

                        <div class="flex flex-col gap-4 mt-4">
                            <SkeletonLoader v-for="i in 3" class="w-full h-20 rounded-lg" />
                        </div>

                        <div class="flex flex-col gap-3 mt-4">
                            <SkeletonLoader class="w-11/12 h-3 rounded-xl" />
                            <SkeletonLoader class="w-10/12 h-3 rounded-xl" />
                            <SkeletonLoader class="w-11/12 h-3 rounded-xl" />
                            <SkeletonLoader class="w-full h-3 rounded-xl" />
                        </div>
                        <div class="flex flex-col gap-4 mt-4">
                            <SkeletonLoader v-for="i in 2" class="w-full h-20 md:h-36 rounded-lg" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side -->
            <div
                class="hidden xl:block col-span-1 w-full pt-6 h-full xl:pt-16 xl:pl-8 xl:border-l border-slate-200 xl:pb-6">
                <div class="flex flex-col gap-4 mt-4">
                    <SkeletonLoader v-for="i in 5" class="w-full h-20 md:h-36 rounded-lg" />
                </div>
            </div>
        </div>

        <!-- Sticky Mobile Bottom Action Bar -->
        <div v-if="!isLoading && product?.id"
            class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-4 py-3 sm:hidden shadow-2xl flex items-center justify-between gap-3">
            <div class="flex flex-col">
                <span class="text-[10px] text-slate-500 font-medium leading-tight">{{ $t('Total Price') }}</span>
                <span class="text-primary text-lg font-bold leading-tight">
                    {{ masterStore.showCurrency(parseFloat(product.discount_price > 0 ? product.discount_price : mainPrice).toFixed(2)) }}
                </span>
            </div>

            <div class="flex items-center gap-2 grow justify-end">
                <button v-if="!cartProduct && product?.is_digital == false"
                    class="px-3.5 py-2.5 rounded-xl border border-primary text-primary font-medium text-xs flex items-center gap-1.5 shadow-sm active:scale-95 transition"
                    @click="addToCart">
                    <div class="w-4 h-4"><BagIcon /></div>
                    <span>{{ $t('Add') }}</span>
                </button>

                <div v-else-if="cartProduct && product?.is_digital == false"
                    class="flex items-center bg-emerald-50 border border-emerald-200 rounded-xl p-1">
                    <button class="w-7 h-7 bg-white text-emerald-700 rounded-lg font-bold shadow-sm flex items-center justify-center text-sm" @click="decrementQty">-</button>
                    <span class="px-2 text-xs font-bold text-emerald-800">{{ cartProduct.quantity }}</span>
                    <button class="w-7 h-7 bg-emerald-600 text-white rounded-lg font-bold shadow-sm flex items-center justify-center text-sm" @click="incrementQty">+</button>
                </div>

                <button
                    class="px-4 py-2.5 rounded-xl bg-primary text-white font-semibold text-xs shadow-md active:scale-95 transition flex items-center justify-center"
                    @click="buyNow">
                    {{ $t('Buy Now') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { nextTick, onMounted, onUnmounted, ref, watch } from "vue";
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { useRoute, useRouter } from "vue-router";
import { useMaster } from "../stores/MasterStore";

import { HeartIcon, HomeIcon, MinusIcon, PlusIcon, ShareIcon } from "@heroicons/vue/24/outline";
import { HeartIcon as HeartIconFill, StarIcon } from "@heroicons/vue/24/solid";
import { FreeMode, Navigation, Thumbs, Pagination } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";

import { useToast } from "vue-toastification";
import { useAuth } from "../stores/AuthStore";
import { useBasketStore } from "../stores/BasketStore";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faFacebookF, faLinkedin, faTwitter, faPinterest, faRedditAlien, faWhatsapp, faTelegram } from '@fortawesome/free-brands-svg-icons';
import { faEnvelope, faArrowUp } from "@fortawesome/free-solid-svg-icons";
import { useShareLink } from "vue3-social-sharing";
const { shareLink } = useShareLink();

import ProductDetailsRightSide from "../components/ProductDetailsRightSide.vue";
import ToastSuccessMessage from "../components/ToastSuccessMessage.vue";
import BagIcon from "../icons/Bag.vue";
import SkeletonLoader from "../components/SkeletonLoader.vue";
import ReviewRatings from "../components/ReviewRatings.vue";
import ProductCard from "../components/ProductCard.vue";
import Review from "../components/Review.vue";

// Import Swiper styles
import "swiper/css";
import "swiper/css/free-mode";
import "swiper/css/navigation";
import "swiper/css/thumbs";
import "swiper/css/pagination";

const toast = useToast();
const route = useRoute();
const router = useRouter();
const masterStore = useMaster();
const basketStore = useBasketStore();
const authStore = useAuth();

const thumbsSwiper = ref(null);
const modules = [FreeMode, Navigation, Thumbs, Pagination];

const setThumbsSwiper = (swiper) => {
    thumbsSwiper.value = swiper;
};

const formData = ref({
    product_id: route.params.id,
    shop_id: route.query.shop_id || null,
    size: null,
    color: null,
    unit: null,
});

const product = ref({});
const productPrice = ref(0);
const mainPrice = ref(0);
const discountPercentage = ref(0);

const relatedProducts = ref([]);
const popularProducts = ref([]);

const aboutProduct = ref(true);
const review = ref(false);

const cartProduct = ref(null);
const isLoading = ref(true);

onMounted(() => {
    fetchProductDetails();
    window.scrollTo(0, 0);
    findProductInCart(route.params.id);
});

watch(formData, () => {
    calculateProductPrice();
}, { deep: true });

const shareOptions = [
    { name: "facebook", icon: faFacebookF, color: "#0d68f1" },
    { name: "linkedin", icon: faLinkedin, color: "#1275b1" },
    { name: "twitter", icon: faTwitter, color: "#47acdf" },
    { name: "pinterest", icon: faPinterest, color: "#bb0f23" },
    { name: "reddit", icon: faRedditAlien, color: "#fc471e" },
    { name: "whatsapp", icon: faWhatsapp, color: "#25d366" },
    { name: "email", icon: faEnvelope, color: "#bb0f23" },
    { name: "telegram", icon: faTelegram, color: "#47acdf" },
];
// 1275b1
const share = (network) => {
    let description = product.value.short_description.replace(/<[^>]*>/g, "");
    let currentURL = window.location.href;
    let thumbnail = product.value.thumbnails[0];

    shareLink({
        network: network,
        url: currentURL,
        title: product.value.name,
        description: description,
        media: thumbnail ? thumbnail.url : null,
        quote: product.value.name,
        hashtags: product.value.meta_keywords,
        twitterUser: product.value.shop?.name
    })
}

const calculateProductPrice = () => {
    var colorPrice = 0;
    var sizePrice = 0;

    const color = product.value.colors?.find((color) => color.id == formData.value.color);
    const size = product.value.sizes?.find((size) => size.id == formData.value.size);

    if (color) {
        colorPrice = color.price ?? 0;
    }
    if (size) {
        sizePrice = size.price ?? 0;
    }

    if (product.value.discount_price > 0) {
        productPrice.value = product.value.discount_price + colorPrice + sizePrice;
        mainPrice.value = product.value.price + colorPrice + sizePrice;
    } else {
        productPrice.value = product.value.price + colorPrice + sizePrice;
        mainPrice.value = productPrice.value;
    }

    discountPercentage.value = (((mainPrice.value - productPrice.value) / mainPrice.value) * 100).toFixed(2);
}

const buyNow = () => {
    const shopId = product.value?.shop?.id || formData.value.shop_id || Number(route.query.shop_id) || 1;
    basketStore.buyNowShopId = shopId;
    try {
        localStorage.setItem('buyNowShopId', shopId);
    } catch (e) {}

    basketStore.addToCart({
        product_id: formData.value.product_id || route.params.id,
        shop_id: shopId,
        is_buy_now: true,
        quantity: 1,
        size: formData.value.size,
        color: formData.value.color,
        unit: null
    }, product.value);
};

watch(route, async () => {
    await nextTick();
    window.scrollTo(0, 0);
    fetchProductDetails();
    aboutProduct.value = true;
    review.value = false;
    formData.value.product_id = route.params.id;
    formData.value.shop_id = route.query.shop_id || null;
    findProductInCart(route.params.id);
});

watch(() => basketStore.products, () => {
    findProductInCart(route.params.id);
}, { deep: true });

const findProductInCart = (productId) => {
    let foundProduct = null;
    basketStore.products.forEach((item) => {
        item.products.find((product) => {
            if (product.id == productId) {
                return (foundProduct = product);
            }
        });
    });
    cartProduct.value = foundProduct;
    if (foundProduct) {
        formData.value.size = foundProduct.size?.id;
        formData.value.color = foundProduct.color?.id;
        formData.value.unit = foundProduct.unit;
    }
};

const addToCart = () => {
    basketStore.addToCart(formData.value, product.value);
    setTimeout(() => {
        findProductInCart(route.params.id);
    }, 200);
};

const decrementQty = () => {
    basketStore.decrementQuantity(product.value);
    setTimeout(() => {
        findProductInCart(route.params.id);
    }, 200);
};

const incrementQty = () => {
    basketStore.incrementQuantity(product.value);
    setTimeout(() => {
        findProductInCart(route.params.id);
    }, 200);
};

const favoriteAddOrRemove = () => {
    if (authStore.token === null) {
        return (authStore.loginModal = true);
    }
    axios.post('/favorite-add-or-remove', {
        product_id: product.value.id
    }, {
        headers: {
            Authorization: authStore.token
        }
    }).then(() => {
        product.value.is_favorite = !product.value.is_favorite
        if (product.value.is_favorite === false) {
            const content = {
                component: ToastSuccessMessage,
                props: {
                    title: 'Product removed from favorite',
                    message: 'Product removed from favorite successfully',
                },
            };
            toast(content, {
                type: "default",
                hideProgressBar: true,
                icon: false,
                position: "top-right",
                toastClassName: "vue-toastification-alert",
                timeout: 3000
            });
        } else {
            const content = {
                component: ToastSuccessMessage,
                props: {
                    title: 'Product added to favorite',
                    message: 'Product added to favorite successfully',
                },
            };
            toast(content, {
                type: "default",
                hideProgressBar: true,
                icon: false,
                position: "top-right",
                toastClassName: "vue-toastification-alert",
                timeout: 3000
            });
        }
        authStore.fetchFavoriteProducts();
    }).catch((error) => {
        console.log(error);
    });
};

const showReview = () => {
    aboutProduct.value = false;
    review.value = true;
    fetchReviews();
};

const flashSale = ref({});
const fetchProductDetails = async () => {
    isLoading.value = true;
    const params = { product_id: route.params.id };
    if (route.query.shop_id) {
        params.shop_id = route.query.shop_id;
    }
    axios.get("/product-details", {
        params: params,
        headers: {
            Authorization: authStore.token,
        },
    }).then((response) => {
        product.value = response.data.data.product;
        if (product.value?.shop?.id) {
            formData.value.shop_id = product.value.shop.id;
        }
        relatedProducts.value = response.data.data.related_products;
        popularProducts.value = response.data.data.popular_products;
        flashSale.value = response.data.data.product.flash_sale;

        if (flashSale.value) {
            startCountdown();
        }

        if (product.value.colors.length > 0) {
            formData.value.color = product.value.colors[0].id;
        } else {
            formData.value.color = null;
        }
        if (product.value.sizes.length > 0) {
            formData.value.size = product.value.sizes[0].id;
        } else {
            formData.value.size = null;
        }
        calculateProductPrice();
        findProductInCart(route.params.id);

        setTimeout(() => {
            isLoading.value = false;
        }, 100);
    });
};

const averageRatings = ref({});

const totalReviews = ref(0);
const reviews = ref([]);

const currentPage = ref(1);
const perPage = ref(6);

const onClickHandler = (page) => {
    currentPage.value = page;
    fetchReviews();
};

const fetchReviews = async () => {
    axios.get("/reviews", {
        params: {
            product_id: route.params.id,
            page: currentPage.value,
            per_page: perPage.value,
        },
    }).then((response) => {
        totalReviews.value = response.data.data.total;
        reviews.value = response.data.data.reviews;
        averageRatings.value = response.data.data.average_rating_percentage;
    });
};


const endDay = ref("");
const endHour = ref("");
const endMinute = ref("");
const endSecond = ref("");
let countdownInterval = null;

const startCountdown = () => {
    const endDate = new Date(flashSale.value?.end_date).getTime();

    if (flashSale.value?.end_date) {
        countdownInterval = setInterval(() => {
            const now = new Date().getTime();
            const timeLeft = endDate - now;

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                endDay.value = "00";
                endHour.value = "00";
                endMinute.value = "00";
                endSecond.value = "00";
            } else {
                endDay.value = String(Math.floor(timeLeft / (1000 * 60 * 60 * 24))).padStart(2, "0");
                endHour.value = String(Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, "0");
                endMinute.value = String(Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, "0");
                endSecond.value = String(Math.floor((timeLeft % (1000 * 60)) / 1000)).padStart(2, "0");
            }
        }, 1000);
    }
};

onUnmounted(() => {
    clearInterval(countdownInterval);
});

// Position variables to control zoom position
const mouseX = ref(0);
const mouseY = ref(0);

const handleMouseMove = (event) => {
    const container = event.currentTarget;
    const rect = container.getBoundingClientRect();

    let clientX, clientY;
    if (event.type === "touchmove" || event.type === "touchstart") {
        const touch = event.touches[0];
        clientX = touch.clientX;
        clientY = touch.clientY;
    } else {
        clientX = event.clientX;
        clientY = event.clientY;
    }

    // Calculate mouse position as a percentage of the container dimensions
    mouseX.value = ((clientX - rect.left) / rect.width) * 100;
    mouseY.value = ((clientY - rect.top) / rect.height) * 100;
};

const resetZoom = () => {
    mouseX.value = 50;
    mouseY.value = 50;
};

watch([mouseX, mouseY], ([x, y]) => {
    document.documentElement.style.setProperty('--mouse-x', `${x}%`);
    document.documentElement.style.setProperty('--mouse-y', `${y}%`);
});
</script>

<style scoped>
.zoom-container {
    overflow: hidden;
    position: relative;
    cursor: zoom-in;
    width: 100%;
    height: 100%;
}

.zoom-image {
    transition: transform 0.3s ease, transform-origin 0.3s ease;
    transform: scale(1);
    transform-origin: center center;
}

.zoom-container:hover .zoom-image {
    transform: scale(3.5);
    transform-origin: calc(var(--mouse-x, 50%)) calc(var(--mouse-y, 50%));
}
</style>

<style>
.description img {
    max-width: 95% !important;
}

iframe {
    width: 100%;
    height: 300px !important;
}

@media(max-width:500px) {
    iframe {
        height: 200px !important;
    }
}

@media(max-width:375px) {
    iframe {
        height: 180px !important;
    }
}

@media(max-width:320px) {
    iframe {
        height: 160px !important;
    }
}

.product-details-slider .swiper-slide {
    height: auto !important;
}

.product-details-thumbnail .swiper-slide {
    @apply h-20 md:h-[120px] lg:h-[100px];
}

.product-details-thumbnail .swiper-button-prev,
.product-details-thumbnail .swiper-button-next {
    @apply bg-white w-6 h-6 rounded-full shadow border border-slate-200 text-slate-600 -translate-y-1/2 mt-0;
}

.product-details-thumbnail .swiper-button-prev::after,
.product-details-thumbnail .swiper-button-next::after {
    @apply text-base;
}

.product-details-thumbnail .swiper-button-next {
    right: 0px;
}

.product-details-thumbnail .swiper-button-prev {
    left: 0px;
}

.product-details-thumbnail .swiper-slide {
    @apply border border-slate-100 rounded-lg transition overflow-hidden;
}

.product-details-thumbnail .swiper-slide-thumb-active {
    @apply border border-primary;
}

.product-details-slider .swiper-pagination {
    bottom: 2px !important;
}

.product-details-slider .swiper-pagination-bullet {
    @apply bg-slate-400 opacity-60 w-2.5 h-2.5 transition-all duration-300;
}

.product-details-slider .swiper-pagination-bullet-active {
    @apply bg-primary opacity-100 w-5 rounded-full;
}
</style>
