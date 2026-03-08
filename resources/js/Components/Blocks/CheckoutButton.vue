<script setup lang="ts">
import { ref, computed } from 'vue';
import axios from 'axios';

interface Props {
    priceId?: string;
    quantity?: number;
    buttonText?: string;
    buttonStyle?: 'primary' | 'secondary' | 'accent';
    showQuantitySelector?: boolean;
    maxQuantity?: number;
}

const props = withDefaults(defineProps<Props>(), {
    priceId: '',
    quantity: 1,
    buttonText: 'Buy Now',
    buttonStyle: 'primary',
    showQuantitySelector: false,
    maxQuantity: 10,
});

const selectedQuantity = ref(props.quantity);
const isLoading = ref(false);
const error = ref<string | null>(null);

const buttonClasses = computed(() => {
    const baseClasses = 'inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg font-semibold text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

    if (!props.priceId) {
        return `${baseClasses} bg-gray-200 text-gray-500 cursor-not-allowed`;
    }

    const styleClasses = {
        primary: 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
        secondary: 'bg-gray-800 text-white hover:bg-gray-900 focus:ring-gray-500',
        accent: 'bg-amber-500 text-white hover:bg-amber-600 focus:ring-amber-500',
    };

    return `${baseClasses} ${styleClasses[props.buttonStyle]}`;
});

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
            quantity: selectedQuantity.value,
        });

        if (response.data.url) {
            window.location.href = response.data.url;
        }
    } catch (err: any) {
        error.value = err.response?.data?.error || 'Unable to process checkout.';
        isLoading.value = false;
    }
};

const decrementQuantity = () => {
    if (selectedQuantity.value > 1) {
        selectedQuantity.value--;
    }
};

const incrementQuantity = () => {
    if (selectedQuantity.value < props.maxQuantity) {
        selectedQuantity.value++;
    }
};
</script>

<template>
    <div class="inline-flex flex-col items-start gap-3">
        <!-- Quantity Selector -->
        <div v-if="showQuantitySelector && priceId" class="inline-flex items-center gap-2">
            <span class="text-sm text-gray-600">Quantity:</span>
            <div class="inline-flex items-center border border-gray-300 rounded-lg">
                <button
                    type="button"
                    @click="decrementQuantity"
                    :disabled="selectedQuantity <= 1"
                    class="px-3 py-2 text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed rounded-l-lg"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </button>
                <span class="px-4 py-2 text-gray-900 font-medium min-w-[3rem] text-center">
                    {{ selectedQuantity }}
                </span>
                <button
                    type="button"
                    @click="incrementQuantity"
                    :disabled="selectedQuantity >= maxQuantity"
                    class="px-3 py-2 text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed rounded-r-lg"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Checkout Button -->
        <button
            @click="handleCheckout"
            :disabled="isLoading || !priceId"
            :class="buttonClasses"
        >
            <svg v-if="isLoading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            {{ isLoading ? 'Processing...' : buttonText }}
        </button>

        <!-- Error Message -->
        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
    </div>
</template>
