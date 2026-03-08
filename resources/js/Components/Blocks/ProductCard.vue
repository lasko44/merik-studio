<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

interface Props {
    image?: string | null;
    title?: string;
    description?: string;
    price?: string;
    priceId?: string;
    buttonText?: string;
    featured?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Product Title',
    description: 'Product description goes here.',
    price: '$99.00',
    priceId: '',
    buttonText: 'Buy Now',
    featured: false,
});

const isLoading = ref(false);
const error = ref<string | null>(null);

const handleCheckout = async () => {
    if (!props.priceId) {
        error.value = 'Product is not available for purchase.';
        return;
    }

    isLoading.value = true;
    error.value = null;

    try {
        const response = await axios.post('/checkout/create-session', {
            price_id: props.priceId,
            quantity: 1,
        });

        if (response.data.url) {
            window.location.href = response.data.url;
        }
    } catch (err: any) {
        error.value = err.response?.data?.error || 'Unable to process checkout.';
        isLoading.value = false;
    }
};
</script>

<template>
    <div :class="[
        'bg-white rounded-2xl overflow-hidden shadow-sm border transition-all duration-300',
        featured ? 'border-indigo-500 ring-2 ring-indigo-500' : 'border-gray-200 hover:shadow-md'
    ]">
        <!-- Featured Badge -->
        <div v-if="featured" class="bg-indigo-500 text-white text-xs font-semibold px-3 py-1 text-center">
            Featured
        </div>

        <!-- Product Image -->
        <div class="aspect-square bg-gray-100 relative overflow-hidden">
            <img
                v-if="image"
                :src="image"
                :alt="title"
                class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- Product Details -->
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ title }}</h3>
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ description }}</p>

            <div class="flex items-center justify-between">
                <span class="text-2xl font-bold text-gray-900">{{ price }}</span>
                <button
                    @click="handleCheckout"
                    :disabled="isLoading || !priceId"
                    :class="[
                        'inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-colors',
                        priceId
                            ? 'bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                            : 'bg-gray-200 text-gray-500 cursor-not-allowed'
                    ]"
                >
                    <svg v-if="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ isLoading ? 'Processing...' : buttonText }}
                </button>
            </div>

            <!-- Error Message -->
            <p v-if="error" class="mt-3 text-sm text-red-600">{{ error }}</p>
        </div>
    </div>
</template>
