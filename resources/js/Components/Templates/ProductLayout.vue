<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    options?: {
        show_gallery?: boolean;
        gallery_position?: 'left' | 'right';
        show_related?: boolean;
    };
}

const props = withDefaults(defineProps<Props>(), {
    options: () => ({}),
});

const showGallery = computed(() => props.options?.show_gallery ?? true);
const galleryPosition = computed(() => props.options?.gallery_position || 'left');
const showRelated = computed(() => props.options?.show_related ?? false);
const isGalleryLeft = computed(() => galleryPosition.value === 'left');
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Product Section -->
        <div :class="[
            'grid grid-cols-1 gap-8 lg:gap-12',
            showGallery ? 'lg:grid-cols-2' : ''
        ]">
            <!-- Gallery Section -->
            <div v-if="showGallery" :class="{ 'lg:order-2': !isGalleryLeft }">
                <slot name="gallery">
                    <div class="aspect-square bg-gray-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </slot>
            </div>

            <!-- Product Details Section -->
            <div :class="{ 'lg:order-1': !isGalleryLeft }">
                <slot name="details">
                    <div class="space-y-6">
                        <slot name="content" />
                    </div>
                </slot>
            </div>
        </div>

        <!-- Features Section -->
        <div class="mt-16 pt-16 border-t border-gray-200">
            <slot name="features" />
        </div>

        <!-- CTA Section -->
        <div class="mt-16">
            <slot name="cta" />
        </div>

        <!-- Related Products Section -->
        <div v-if="showRelated" class="mt-16 pt-16 border-t border-gray-200">
            <slot name="related">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Products</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <slot name="related-items" />
                </div>
            </slot>
        </div>
    </div>
</template>
