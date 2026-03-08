<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    showIcon?: boolean;
    showLabel?: boolean;
    lowStockThreshold?: number;
    inStockText?: string;
    lowStockText?: string;
    outOfStockText?: string;
    product?: {
        track_inventory?: boolean;
        quantity?: number;
        is_active?: boolean;
    };
}

const props = withDefaults(defineProps<Props>(), {
    showIcon: true,
    showLabel: true,
    lowStockThreshold: 5,
    inStockText: 'In Stock',
    lowStockText: 'Low Stock',
    outOfStockText: 'Out of Stock',
});

const stockQuantity = computed(() => props.product?.quantity ?? 0);
const tracksInventory = computed(() => props.product?.track_inventory ?? false);

type StockLevel = 'in_stock' | 'low_stock' | 'out_of_stock' | 'unlimited';

const stockStatus = computed((): StockLevel => {
    if (!tracksInventory.value) {
        return 'unlimited';
    }
    if (stockQuantity.value <= 0) {
        return 'out_of_stock';
    }
    if (stockQuantity.value <= props.lowStockThreshold) {
        return 'low_stock';
    }
    return 'in_stock';
});

const statusText = computed(() => {
    switch (stockStatus.value) {
        case 'unlimited':
            return props.inStockText;
        case 'in_stock':
            return props.inStockText;
        case 'low_stock':
            return `${props.lowStockText} - Only ${stockQuantity.value} left`;
        case 'out_of_stock':
            return props.outOfStockText;
        default:
            return '';
    }
});

const statusClasses = computed(() => {
    switch (stockStatus.value) {
        case 'unlimited':
        case 'in_stock':
            return {
                container: 'bg-green-50',
                icon: 'text-green-500',
                text: 'text-green-700',
                dot: 'bg-green-500',
            };
        case 'low_stock':
            return {
                container: 'bg-yellow-50',
                icon: 'text-yellow-500',
                text: 'text-yellow-700',
                dot: 'bg-yellow-500',
            };
        case 'out_of_stock':
            return {
                container: 'bg-red-50',
                icon: 'text-red-500',
                text: 'text-red-700',
                dot: 'bg-red-500',
            };
        default:
            return {
                container: 'bg-gray-50',
                icon: 'text-gray-500',
                text: 'text-gray-700',
                dot: 'bg-gray-500',
            };
    }
});
</script>

<template>
    <div
        :class="[
            'inline-flex items-center gap-2 px-3 py-2 rounded-lg',
            statusClasses.container
        ]"
    >
        <!-- Icon -->
        <template v-if="showIcon">
            <svg
                v-if="stockStatus === 'in_stock' || stockStatus === 'unlimited'"
                :class="['w-5 h-5', statusClasses.icon]"
                fill="currentColor"
                viewBox="0 0 20 20"
            >
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <svg
                v-else-if="stockStatus === 'low_stock'"
                :class="['w-5 h-5', statusClasses.icon]"
                fill="currentColor"
                viewBox="0 0 20 20"
            >
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <svg
                v-else
                :class="['w-5 h-5', statusClasses.icon]"
                fill="currentColor"
                viewBox="0 0 20 20"
            >
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
        </template>

        <!-- Animated Dot (alternative to icon) -->
        <span
            v-if="!showIcon"
            :class="[
                'w-2.5 h-2.5 rounded-full',
                statusClasses.dot,
                stockStatus === 'in_stock' || stockStatus === 'unlimited' ? 'animate-pulse' : ''
            ]"
        />

        <!-- Label -->
        <span
            v-if="showLabel"
            :class="['text-sm font-medium', statusClasses.text]"
        >
            {{ statusText }}
        </span>
    </div>
</template>
