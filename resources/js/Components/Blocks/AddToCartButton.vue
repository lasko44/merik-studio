<script setup lang="ts">
import { ref, computed } from 'vue';
import axios from 'axios';

interface Product {
    id: number;
    name: string;
    price: number;
    currency: string;
    stripe_price_id?: string;
    track_inventory?: boolean;
    quantity?: number;
    is_active?: boolean;
}

interface Props {
    buttonText?: string;
    buttonStyle?: 'primary' | 'secondary' | 'accent';
    showQuantitySelector?: boolean;
    minQuantity?: number;
    maxQuantity?: number;
    showPrice?: boolean;
    showStock?: boolean;
    product?: Product;
}

const props = withDefaults(defineProps<Props>(), {
    buttonText: 'Add to Cart',
    buttonStyle: 'primary',
    showQuantitySelector: true,
    minQuantity: 1,
    maxQuantity: 10,
    showPrice: true,
    showStock: true,
});

const selectedQuantity = ref(props.minQuantity);
const isLoading = ref(false);
const error = ref<string | null>(null);
const successMessage = ref<string | null>(null);

const isInStock = computed(() => {
    if (!props.product?.track_inventory) return true;
    return (props.product?.quantity ?? 0) > 0;
});

const stockQuantity = computed(() => props.product?.quantity ?? 0);

const effectiveMaxQuantity = computed(() => {
    if (!props.product?.track_inventory) return props.maxQuantity;
    return Math.min(props.maxQuantity, stockQuantity.value);
});

const formattedPrice = computed(() => {
    if (!props.product) return '';
    const symbol = props.product.currency === 'USD' ? '$' : props.product.currency === 'EUR' ? '\u20AC' : props.product.currency === 'GBP' ? '\u00A3' : props.product.currency + ' ';
    const total = Number(props.product.price) * selectedQuantity.value;
    return symbol + total.toFixed(2);
});

const buttonClasses = computed(() => {
    const baseClasses = 'w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg font-semibold text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

    if (!isInStock.value || !props.product?.stripe_price_id) {
        return `${baseClasses} bg-gray-200 text-gray-500 cursor-not-allowed`;
    }

    const styleClasses = {
        primary: 'text-white hover:opacity-90',
        secondary: 'bg-gray-800 text-white hover:bg-gray-900 focus:ring-gray-500',
        accent: 'text-white hover:opacity-90',
    };

    return `${baseClasses} ${styleClasses[props.buttonStyle]}`;
});

const handleAddToCart = async () => {
    if (!props.product?.stripe_price_id || !isInStock.value) {
        error.value = 'Product is not available for purchase.';
        return;
    }

    isLoading.value = true;
    error.value = null;
    successMessage.value = null;

    try {
        const response = await axios.post('/checkout/create-session', {
            price_id: props.product.stripe_price_id,
            quantity: selectedQuantity.value,
        });

        if (response.data.url) {
            window.location.href = response.data.url;
        }
    } catch (err: unknown) {
        const axiosError = err as { response?: { data?: { error?: string } } };
        error.value = axiosError.response?.data?.error || 'Unable to process checkout.';
        isLoading.value = false;
    }
};

const decrementQuantity = () => {
    if (selectedQuantity.value > props.minQuantity) {
        selectedQuantity.value--;
    }
};

const incrementQuantity = () => {
    if (selectedQuantity.value < effectiveMaxQuantity.value) {
        selectedQuantity.value++;
    }
};
</script>

<template>
    <div class="add-to-cart-section space-y-4">
        <!-- Price Display -->
        <div v-if="showPrice && product" class="text-2xl font-bold text-gray-900">
            {{ formattedPrice }}
            <span v-if="selectedQuantity > 1" class="text-sm font-normal text-gray-500">
                ({{ selectedQuantity }} items)
            </span>
        </div>

        <!-- Stock Status -->
        <div v-if="showStock && product?.track_inventory" class="flex items-center gap-2">
            <span
                :class="[
                    'inline-flex items-center gap-1.5 text-sm font-medium',
                    isInStock ? (stockQuantity <= 5 ? 'text-yellow-600' : 'text-green-600') : 'text-red-600'
                ]"
            >
                <span
                    :class="[
                        'w-2 h-2 rounded-full',
                        isInStock ? (stockQuantity <= 5 ? 'bg-yellow-500' : 'bg-green-500') : 'bg-red-500'
                    ]"
                />
                <template v-if="isInStock">
                    {{ stockQuantity <= 5 ? `Only ${stockQuantity} left` : 'In Stock' }}
                </template>
                <template v-else>Out of Stock</template>
            </span>
        </div>

        <!-- Quantity Selector -->
        <div v-if="showQuantitySelector && isInStock" class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-700">Quantity:</span>
            <div class="inline-flex items-center border border-gray-300 rounded-lg">
                <button
                    type="button"
                    @click="decrementQuantity"
                    :disabled="selectedQuantity <= minQuantity"
                    class="px-3 py-2 text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed rounded-l-lg transition-colors"
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
                    :disabled="selectedQuantity >= effectiveMaxQuantity"
                    class="px-3 py-2 text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed rounded-r-lg transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Add to Cart Button -->
        <button
            @click="handleAddToCart"
            :disabled="isLoading || !isInStock || !product?.stripe_price_id"
            :class="buttonClasses"
            :style="buttonStyle === 'primary' ? { backgroundColor: 'var(--primary-color)' } : buttonStyle === 'accent' ? { backgroundColor: 'var(--accent-color)' } : {}"
        >
            <svg v-if="isLoading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            {{ isLoading ? 'Processing...' : (isInStock ? buttonText : 'Out of Stock') }}
        </button>

        <!-- Messages -->
        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        <p v-if="successMessage" class="text-sm text-green-600">{{ successMessage }}</p>
    </div>
</template>
