<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed, type Component } from 'vue';
import type { Product, ProductCategory, ContentBlock } from '@/types/admin';

// Product Block Components
import ProductImageGallery from '@/Components/Blocks/ProductImageGallery.vue';
import AddToCartButton from '@/Components/Blocks/AddToCartButton.vue';
import RelatedProducts from '@/Components/Blocks/RelatedProducts.vue';
import ProductReviews from '@/Components/Blocks/ProductReviews.vue';
import AppointmentPicker from '@/Components/Blocks/AppointmentPicker.vue';
import ProductSpecifications from '@/Components/Blocks/ProductSpecifications.vue';
import StockStatus from '@/Components/Blocks/StockStatus.vue';

// General Block Components (for flexibility)
import TextSection from '@/Components/Blocks/TextSection.vue';
import HeadingSection from '@/Components/Blocks/HeadingSection.vue';
import ImageSection from '@/Components/Blocks/ImageSection.vue';
import SpacerSection from '@/Components/Blocks/SpacerSection.vue';
import FeaturesSection from '@/Components/Blocks/FeaturesSection.vue';
import HeroSection from '@/Components/Blocks/HeroSection.vue';
import ContactSection from '@/Components/Blocks/ContactSection.vue';
import CtaSection from '@/Components/Blocks/CtaSection.vue';

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

interface RelatedProduct {
    id: number;
    name: string;
    slug: string;
    price: number;
    currency: string;
    image?: string;
    is_featured?: boolean;
}

interface Props {
    product: Product & {
        content?: ContentBlock[];
    };
    categories: ProductCategory[];
    relatedProducts?: RelatedProduct[];
    settings: Settings;
    navigation: NavItem[];
    seo: {
        title: string;
        description: string | null;
        ogImage?: string | null;
    };
    canEdit: boolean;
    editUrl: string | null;
}

const props = withDefaults(defineProps<Props>(), {
    relatedProducts: () => [],
});

// Component map for rendering blocks
const componentMap: Record<string, Component> = {
    // Product components
    'product-image-gallery': ProductImageGallery,
    'add-to-cart-button': AddToCartButton,
    'related-products': RelatedProducts,
    'product-reviews': ProductReviews,
    'appointment-picker': AppointmentPicker,
    'product-specifications': ProductSpecifications,
    'stock-status': StockStatus,
    // General components
    'text': TextSection,
    'heading': HeadingSection,
    'image': ImageSection,
    'spacer': SpacerSection,
    'features': FeaturesSection,
    'hero': HeroSection,
    'contact-section': ContactSection,
    'call-to-action': CtaSection,
};

// Block type categories for smart placement
const bottomTypes = ['related-products', 'product-reviews'];
const contentTypes = ['text', 'heading', 'image', 'spacer', 'features', 'hero', 'contact-section', 'call-to-action'];

// Helper to find a block by type
const findBlock = (types: string[]) => {
    return props.product.content?.find(block => types.includes(block.type));
};

// Helper to get all blocks of certain types
const getBlocks = (types: string[]) => {
    return props.product.content?.filter(block => types.includes(block.type)) || [];
};

// Computed blocks by position
const stockBlock = computed(() => findBlock(['stock-status']));
const cartBlock = computed(() => findBlock(['add-to-cart-button']));
const specsBlock = computed(() => findBlock(['product-specifications']));
const appointmentBlock = computed(() => findBlock(['appointment-picker']));
const bottomBlocks = computed(() => getBlocks(bottomTypes));
const contentBlocks = computed(() => getBlocks(contentTypes));

// Check if product has gallery images
const hasGalleryImages = computed(() => {
    return props.product.gallery_images && props.product.gallery_images.length > 0;
});

const formatPrice = (price: number, currency: string): string => {
    const symbol = currency === 'USD' ? '$' : currency === 'EUR' ? '\u20AC' : currency === 'GBP' ? '\u00A3' : currency + ' ';
    return symbol + Number(price).toFixed(2);
};
</script>

<template>
    <PublicLayout :settings="settings" :navigation="navigation">
        <SeoHead
            :seo="{
                meta: {
                    title: seo.title,
                    description: seo.description,
                    keywords: null,
                },
            }"
            :title="seo.title"
        />

        <div class="bg-white">
            <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
                <!-- Edit Button (for authorized users) -->
                <div v-if="canEdit && editUrl" class="mb-6">
                    <a
                        :href="editUrl"
                        class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Product
                    </a>
                </div>

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
                        <li v-if="product.category">
                            <Link :href="route('products.category', product.category.slug)" class="hover:text-gray-700">
                                {{ product.category.name }}
                            </Link>
                        </li>
                        <li v-if="product.category">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </li>
                        <li class="text-gray-900 font-medium">{{ product.name }}</li>
                    </ol>
                </nav>

                <!-- Main Product Section -->
                <div class="lg:grid lg:grid-cols-2 lg:gap-x-12">
                    <!-- Left Column: Product Image Gallery -->
                    <div>
                        <!-- Use gallery component when there are images -->
                        <ProductImageGallery
                            :product="product"
                            :show-thumbnails="true"
                            thumbnail-position="bottom"
                            :enable-lightbox="true"
                            :enable-zoom="true"
                        />
                    </div>

                    <!-- Right Column: Product Info -->
                    <div class="mt-10 lg:mt-0 flex flex-col">
                        <!-- Category Link -->
                        <Link
                            v-if="product.category"
                            :href="route('products.category', product.category.slug)"
                            class="text-sm font-medium uppercase tracking-wide hover:underline"
                            :style="{ color: settings.primaryColor }"
                        >
                            {{ product.category.name }}
                        </Link>

                        <!-- Title -->
                        <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                            {{ product.name }}
                        </h1>

                        <!-- Featured Badge -->
                        <div v-if="product.is_featured" class="mt-3">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium" :style="{ backgroundColor: settings.accentColor + '20', color: settings.accentColor }">
                                Featured
                            </span>
                        </div>

                        <!-- Price & Stock Row -->
                        <div class="mt-6 flex items-center justify-between gap-4">
                            <div>
                                <p class="text-3xl font-bold tracking-tight text-gray-900">
                                    {{ formatPrice(product.price, product.currency) }}
                                    <span v-if="product.type === 'recurring' && product.recurring_interval" class="text-lg font-normal text-gray-500">
                                        / {{ product.recurring_interval }}
                                    </span>
                                </p>
                                <p v-if="product.type === 'recurring'" class="mt-1 text-sm text-gray-500">
                                    Recurring subscription
                                </p>
                            </div>
                            <!-- Stock Status Block -->
                            <component
                                v-if="stockBlock"
                                :is="componentMap[stockBlock.type]"
                                v-bind="stockBlock.data"
                                :product="product"
                            />
                        </div>

                        <!-- Description -->
                        <div v-if="product.description" class="mt-6">
                            <div class="prose prose-gray max-w-none">
                                <p class="text-gray-600 leading-relaxed">{{ product.description }}</p>
                            </div>
                        </div>

                        <!-- Purchase Section -->
                        <div class="mt-8">
                            <!-- Custom Add to Cart Block or Default Button -->
                            <component
                                v-if="cartBlock"
                                :is="componentMap[cartBlock.type]"
                                v-bind="cartBlock.data"
                                :product="product"
                            />
                            <button
                                v-else
                                type="button"
                                class="w-full rounded-lg px-6 py-4 text-base font-semibold text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-opacity"
                                :style="{ backgroundColor: settings.primaryColor }"
                            >
                                {{ product.type === 'recurring' ? 'Subscribe Now' : 'Add to Cart' }}
                            </button>
                        </div>

                        <!-- Appointment Picker (if service product) -->
                        <div v-if="appointmentBlock" class="mt-8">
                            <component
                                :is="componentMap[appointmentBlock.type]"
                                v-bind="appointmentBlock.data"
                                :product="product"
                            />
                        </div>

                        <!-- Product Specifications -->
                        <div v-if="specsBlock" class="mt-8 border-t border-gray-200 pt-8">
                            <component
                                :is="componentMap[specsBlock.type]"
                                v-bind="specsBlock.data"
                                :product="product"
                            />
                        </div>

                        <!-- Trust Badges / Additional Info -->
                        <div class="mt-auto pt-8">
                            <div class="flex items-center gap-6 text-sm text-gray-500">
                                <div class="flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <span>Secure checkout</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <span>Safe payment</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Content Blocks -->
                <div v-if="contentBlocks.length > 0" class="mt-16 space-y-8">
                    <template v-for="block in contentBlocks" :key="block.id">
                        <component
                            :is="componentMap[block.type]"
                            v-bind="block.data"
                            :product="product"
                        />
                    </template>
                </div>

                <!-- Reviews Section -->
                <template v-for="block in bottomBlocks" :key="block.id">
                    <div class="mt-16 border-t border-gray-200 pt-16">
                        <component
                            :is="componentMap[block.type]"
                            v-bind="block.data"
                            :product="product"
                            :related-products="relatedProducts"
                        />
                    </div>
                </template>

                <!-- Back to Products -->
                <div class="mt-16 border-t border-gray-200 pt-8">
                    <Link
                        :href="route('products.index')"
                        class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Products
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
