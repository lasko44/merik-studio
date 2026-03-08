<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

interface Product {
    id: number;
    name: string;
    slug: string;
    price: number;
    currency: string;
    image?: string;
    is_featured?: boolean;
}

interface Props {
    title?: string;
    source?: 'category' | 'featured' | 'manual';
    limit?: number;
    columns?: 2 | 3 | 4;
    showPrice?: boolean;
    relatedProducts?: Product[];
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Related Products',
    source: 'category',
    limit: 4,
    columns: 4,
    showPrice: true,
    relatedProducts: () => [],
});

const displayProducts = computed(() => {
    return (props.relatedProducts || []).slice(0, props.limit);
});

const gridClasses = computed(() => {
    const cols = {
        2: 'grid-cols-1 sm:grid-cols-2',
        3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        4: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    };
    return cols[props.columns];
});

const formatPrice = (price: number, currency: string): string => {
    const symbol = currency === 'USD' ? '$' : currency === 'EUR' ? '\u20AC' : currency === 'GBP' ? '\u00A3' : currency + ' ';
    return symbol + Number(price).toFixed(2);
};
</script>

<template>
    <section class="related-products">
        <h2 v-if="title" class="text-2xl font-bold text-gray-900 mb-6">
            {{ title }}
        </h2>

        <div v-if="displayProducts.length > 0" :class="['grid gap-6', gridClasses]">
            <Link
                v-for="product in displayProducts"
                :key="product.id"
                :href="`/products/${product.slug}`"
                class="group"
            >
                <div class="relative aspect-square overflow-hidden rounded-lg bg-gray-100 mb-3">
                    <img
                        v-if="product.image"
                        :src="product.image"
                        :alt="product.name"
                        class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-300"
                    />
                    <div v-else class="h-full w-full flex items-center justify-center">
                        <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div
                        v-if="product.is_featured"
                        class="absolute top-2 left-2 bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded"
                    >
                        Featured
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-900 group-hover:text-gray-600 transition-colors">
                    {{ product.name }}
                </h3>
                <p v-if="showPrice" class="mt-1 text-sm font-semibold text-gray-900">
                    {{ formatPrice(product.price, product.currency) }}
                </p>
            </Link>
        </div>

        <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">No related products found</p>
        </div>
    </section>
</template>
