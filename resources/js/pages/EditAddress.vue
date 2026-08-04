<template>
    <div>
        <div class="bg-white px-3 text-slate-600 flex items-center gap-1 pt-2 leading-normal">
            <HomeIcon class="w-5 h-5 md:w-6 md:h-6" />
            <router-link to="/manage-address" class="hover:text-primary">
                {{ $t('Manage Address') }}
            </router-link>
            <span>/ {{ $t('Edit Address') }}</span>
        </div>
        <!-- Header -->
        <AuthPageHeader :title="$t('Edit Address')" />

        <div class="p-3 md:p-4 xl:p-6">
            <div class="max-w-5xl mx-auto">
                <AddressEditForm :address="address"/>
            </div>
        </div>
    </div>
</template>

<script setup>
import { HomeIcon } from '@heroicons/vue/24/solid';
import { onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import AddressEditForm from '../components/AddressEditForm.vue';
import AuthPageHeader from '../components/AuthPageHeader.vue';
import { useAuth } from "../stores/AuthStore";

const route = useRoute();
const authStore = useAuth();
const address = ref({});

const loadAddress = async () => {
    if (!authStore.addresses || authStore.addresses.length === 0) {
        await authStore.fetchAddresses();
    }
    const found = authStore.getAddressById(route.params.id);
    if (found) {
        address.value = { ...found };
    }
};

onMounted(() => {
    loadAddress();
});

watch(
    () => route.params.id,
    () => {
        loadAddress();
    }
);
</script>
