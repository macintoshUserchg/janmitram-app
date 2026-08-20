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
import { useRoute, useRouter } from 'vue-router';
import AddressEditForm from '../components/AddressEditForm.vue';
import AuthPageHeader from '../components/AuthPageHeader.vue';
import LoadingSpin from '../components/LoadingSpin.vue';
import { useAuth } from "../stores/AuthStore";

const route = useRoute();
const router = useRouter();
const authStore = useAuth();
const address = ref({});
const isLoading = ref(true);

const loadAddress = async () => {
    isLoading.value = true;
    if (!authStore.token) {
        authStore.showLoginModal();
        router.push('/');
        return;
    }
    try {
        if (!authStore.addresses || authStore.addresses.length === 0) {
            await authStore.fetchAddresses();
        }
        const found = authStore.getAddressById(route.params.id);
        if (found) {
            address.value = { ...found };
        } else {
            // If address not found in cached list, try fetching fresh
            await authStore.fetchAddresses();
            const freshFound = authStore.getAddressById(route.params.id);
            if (freshFound) {
                address.value = { ...freshFound };
            }
        }
    } catch (e) {
        console.error("Error loading address:", e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    loadAddress();
});

watch(
    () => authStore.addresses,
    () => {
        const found = authStore.getAddressById(route.params.id);
        if (found) {
            address.value = { ...found };
        }
    },
    { deep: true }
);

watch(
    () => route.params.id,
    () => {
        loadAddress();
    }
);
</script>
