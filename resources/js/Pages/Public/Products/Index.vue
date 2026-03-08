<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Product, ProductCategory } from '@/types/admin';

interface NavItem {
    title: string;
    path: string;
}

interface Settings {
    siteName: string;
    tagline: string | null;
    logoPath: string | null;
    showSiteNameInNav: boolean;
    navLogoHeight: number;
    primaryColor: string;
    secondaryColor: string;
    accentColor: string;
    backgroundColor: string;
    textColor: string;
    successColor: string;
    warningColor: string;
    errorColor: string;
    infoColor: string;
    surfaceColor: string;
    borderColor: string;
    mutedColor: string;
    contactEmail: string | null;
    contactPhone: string | null;
    contactAddress: string | null;
    socialLinks: Array<{ platform: string; url: string }> | null;
}

interface ProductSettings {
    enableSearch: boolean;
    enableCategoryFilter: boolean;
}

interface Filters {
    search: string | null;
    category: string | null;
}

interface PaginatedProducts {
    data: Product[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
}

interface Props {
    products: PaginatedProducts;
    categories: ProductCategory[];
    settings: Settings;
    productSettings: ProductSettings;
    filters: Filters;
    navigation: NavItem[];
    seo: {
        title: string;
        description: string | null;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const applyFilters = () => {
    const params: Record<string, string> = {};

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }
    if (selectedCategory.value) {
        params.category = selectedCategory.value;
    }

    router.get(route('products.index'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
};

const handleCategoryChange = () => {
    applyFilters();
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    router.get(route('products.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const hasActiveFilters = () => {
    return searchQuery.value || selectedCategory.value;
};

const formatPrice = (price: number, currency: string): string => {
    const symbol = currency === 'USD' ? '$' : currency === 'EUR' ? '\u20AC' : currency === 'GBP' ? '\u00A3' : currency + ' ';
    return symbol + Number(price).toFixed(2);
};
</script>

<template>
    <PublicLayout :settings="settings" :navigation="navigation">
        <SeoHead :seo="{ meta: { title: seo.title, description: seo.description, keywords: null } }" :title="seo.title" />

        <div class="bg-white py-12 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">Products</h1>
                    <p class="mt-4 text-lg text-gray-600">Browse our collection of products.</p>
                </div>

                <!-- Search and Filter Bar -->
                <div v-if="productSettings.enableSearch || productSettings.enableCategoryFilter" class="mb-8">
                    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                        <!-- Search Input -->
                        <div v-if="productSettings.enableSearch" class="relative flex-1 w-full sm:max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search products..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                @input="handleSearch"
                            />
                        </div>

                        <!-- Category Filter -->
                        <div v-if="productSettings.enableCategoryFilter && categories.length > 0" class="w-full sm:w-auto">
                            <select
                                v-model="selectedCategory"
                                class="block w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                @change="handleCategoryChange"
                            >
                                <option value="">All Categories</option>
                                <option v-for="category in categories" :key="category.id" :value="category.slug">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        <button
                            v-if="hasActiveFilters()"
                            @click="clearFilters"
                            class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear filters
                        </button>
                    </div>

                    <!-- Active Filters Display -->
                    <div v-if="hasActiveFilters()" class="mt-3 flex flex-wrap gap-2">
                        <span
                            v-if="searchQuery"
                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-sm text-gray-700"
                        >
                            Search: "{{ searchQuery }}"
                        </span>
                        <span
                            v-if="selectedCategory"
                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-sm text-gray-700"
                        >
                            Category: {{ categories.find(c => c.slug === selectedCategory)?.name }}
                        </span>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="products.data.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">
                        {{ hasActiveFilters() ? 'No matching products' : 'No products yet' }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ hasActiveFilters() ? 'Try adjusting your search or filter.' : 'Check back later for new products.' }}
                    </p>
                    <button
                        v-if="hasActiveFilters()"
                        @click="clearFilters"
                        class="mt-4 text-sm text-gray-600 hover:text-gray-900 underline"
                    >
                        Clear all filters
                    </button>
                </div>

                <!-- Products Grid -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <Link
                        v-for="product in products.data"
                        :key="product.id"
                        :href="route('products.show', product.slug)"
                        class="group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white hover:shadow-lg transition-shadow"
                    >
                        <!-- Product Image -->
                        <div class="aspect-square bg-gray-100 overflow-hidden">
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
                        </div>

                        <!-- Featured Badge -->
                        <div v-if="product.is_featured" class="absolute top-2 right-2">
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">
                                Featured
                            </span>
                        </div>

                        <!-- Product Info -->
                        <div class="flex flex-1 flex-col p-4">
                            <h3 class="text-sm font-medium text-gray-900 group-hover:text-gray-700">
                                {{ product.name }}
                            </h3>
                            <p v-if="product.category" class="mt-1 text-xs text-gray-500">
                                {{ product.category.name }}
                            </p>
                            <p v-if="product.description" class="mt-2 text-sm text-gray-500 line-clamp-2">
                                {{ product.description }}
                            </p>
                            <div class="mt-auto pt-4">
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ formatPrice(product.price, product.currency) }}
                                    <span v-if="product.type === 'recurring' && product.recurring_interval" class="text-sm font-normal text-gray-500">
                                        / {{ product.recurring_interval }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Pagination -->
                <nav v-if="products.last_page > 1" class="mt-12 flex items-center justify-center gap-2">
                    <template v-for="link in products.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'px-4 py-2 text-sm font-medium rounded-lg',
                                link.active
                                    ? 'products-pagination-active text-white'
                                    : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
                            ]"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="px-4 py-2 text-sm font-medium text-gray-400"
                            v-html="link.label"
                        />
                    </template>
                </nav>
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
.products-pagination-active {
    background-color: var(--primary-color);
}
</style>
