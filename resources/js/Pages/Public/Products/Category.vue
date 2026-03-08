<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
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

interface PaginatedProducts {
    data: Product[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
}

interface Props {
    category: ProductCategory;
    products: PaginatedProducts;
    categories: ProductCategory[];
    settings: Settings;
    navigation: NavItem[];
    seo: {
        title: string;
        description: string | null;
    };
}

const props = defineProps<Props>();

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
                <!-- Breadcrumb -->
                <nav class="mb-8">
                    <ol class="flex items-center gap-2 text-sm text-gray-500">
                        <li>
                            <Link :href="route('products.index')" class="hover:text-gray-700">
                                Products
                            </Link>
                        </li>
                        <li>
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </li>
                        <li class="text-gray-900 font-medium">{{ category.name }}</li>
                    </ol>
                </nav>

                <!-- Header -->
                <div class="mb-12">
                    <div class="flex items-start gap-6">
                        <!-- Category Image -->
                        <div v-if="category.image" class="hidden sm:block h-24 w-24 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100">
                            <img
                                :src="category.image"
                                :alt="category.name"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                                {{ category.name }}
                            </h1>
                            <p v-if="category.description" class="mt-4 text-lg text-gray-600">
                                {{ category.description }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="products.data.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No products in this category</h3>
                    <p class="mt-1 text-sm text-gray-500">Check back later or browse other categories.</p>
                    <div class="mt-6">
                        <Link
                            :href="route('products.index')"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900"
                        >
                            View all products
                        </Link>
                    </div>
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

                <!-- Other Categories -->
                <div v-if="categories.length > 1" class="mt-16 border-t border-gray-200 pt-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Other Categories</h2>
                    <div class="flex flex-wrap gap-2">
                        <Link
                            v-for="cat in categories.filter(c => c.id !== category.id)"
                            :key="cat.id"
                            :href="route('products.category', cat.slug)"
                            class="rounded-full bg-gray-100 px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 transition-colors"
                        >
                            {{ cat.name }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
.products-pagination-active {
    background-color: var(--primary-color);
}
</style>
