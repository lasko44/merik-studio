<script setup lang="ts">
import { ref, computed } from 'vue';

interface ImageWithId {
    id: string;
    src: string;
}

interface Props {
    showThumbnails?: boolean;
    thumbnailPosition?: 'bottom' | 'left' | 'right';
    enableLightbox?: boolean;
    enableZoom?: boolean;
    product?: {
        image?: string | null;
        gallery_images?: string[] | null;
    };
}

const props = withDefaults(defineProps<Props>(), {
    showThumbnails: true,
    thumbnailPosition: 'bottom',
    enableLightbox: true,
    enableZoom: true,
});

// Combine main image with gallery images and add unique IDs
const allImages = computed<ImageWithId[]>(() => {
    const imgs: ImageWithId[] = [];
    const seenSrcs = new Set<string>();

    // Add main product image first
    if (props.product?.image) {
        imgs.push({ id: 'main', src: props.product.image });
        seenSrcs.add(props.product.image);
    }

    // Add gallery images
    if (props.product?.gallery_images && Array.isArray(props.product.gallery_images)) {
        props.product.gallery_images.forEach((img, index) => {
            if (img && !seenSrcs.has(img)) {
                imgs.push({ id: `gallery-${index}`, src: img });
                seenSrcs.add(img);
            }
        });
    }

    return imgs;
});

const activeIndex = ref(0);
const showLightbox = ref(false);
const isZoomed = ref(false);
const zoomPosition = ref({ x: 50, y: 50 });

const activeImage = computed(() => allImages.value[activeIndex.value]?.src ?? '');

const setActiveImage = (index: number) => {
    activeIndex.value = index;
};

const openLightbox = () => {
    if (props.enableLightbox) {
        showLightbox.value = true;
    }
};

const closeLightbox = () => {
    showLightbox.value = false;
};

const nextImage = () => {
    activeIndex.value = (activeIndex.value + 1) % allImages.value.length;
};

const prevImage = () => {
    activeIndex.value = (activeIndex.value - 1 + allImages.value.length) % allImages.value.length;
};

const handleMouseMove = (event: MouseEvent) => {
    if (!props.enableZoom || !isZoomed.value) return;
    const target = event.currentTarget;
    if (!(target instanceof HTMLElement)) return;
    const rect = target.getBoundingClientRect();
    zoomPosition.value = {
        x: ((event.clientX - rect.left) / rect.width) * 100,
        y: ((event.clientY - rect.top) / rect.height) * 100,
    };
};

const toggleZoom = () => {
    if (props.enableZoom) {
        isZoomed.value = !isZoomed.value;
    }
};

const layoutClasses = computed(() => {
    if (!props.showThumbnails) return 'flex-col';
    switch (props.thumbnailPosition) {
        case 'left':
            return 'flex-row-reverse';
        case 'right':
            return 'flex-row';
        default:
            return 'flex-col';
    }
});

const thumbnailContainerClasses = computed(() => {
    if (props.thumbnailPosition === 'bottom') {
        return 'flex gap-2 mt-4 overflow-x-auto pb-2';
    }
    return 'flex flex-col gap-2 w-20 overflow-y-auto max-h-[500px]';
});
</script>

<template>
    <div class="product-image-gallery">
        <div v-if="allImages.length > 0" :class="['flex gap-4', layoutClasses]">
            <!-- Main Image -->
            <div class="flex-1">
                <div
                    class="relative aspect-square overflow-hidden rounded-xl bg-gray-100"
                    :class="{
                        'cursor-zoom-in': enableZoom && !isZoomed,
                        'cursor-zoom-out': enableZoom && isZoomed,
                        'cursor-pointer': enableLightbox && !enableZoom
                    }"
                    @click="enableZoom ? toggleZoom() : openLightbox()"
                    @mousemove="handleMouseMove"
                    @mouseleave="isZoomed = false"
                >
                    <img
                        :src="activeImage"
                        alt="Product image"
                        class="h-full w-full object-cover object-center transition-transform duration-300"
                        :style="isZoomed ? {
                            transform: 'scale(2)',
                            transformOrigin: `${zoomPosition.x}% ${zoomPosition.y}%`
                        } : {}"
                    />
                    <!-- Lightbox Button -->
                    <button
                        v-if="enableLightbox"
                        @click.stop="openLightbox"
                        class="absolute top-4 right-4 p-2 bg-white/80 rounded-full shadow-sm hover:bg-white transition-colors"
                    >
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Thumbnails -->
            <div v-if="showThumbnails && allImages.length > 1" :class="thumbnailContainerClasses">
                <button
                    v-for="(image, index) in allImages"
                    :key="image.id"
                    @click="setActiveImage(index)"
                    :class="[
                        'flex-shrink-0 rounded-lg overflow-hidden border-2 transition-all',
                        thumbnailPosition === 'bottom' ? 'w-16 h-16' : 'w-full aspect-square',
                        activeIndex === index ? 'border-gray-900 ring-2 ring-gray-900 ring-offset-2' : 'border-transparent hover:border-gray-300'
                    ]"
                >
                    <img :src="image.src" alt="Thumbnail" class="w-full h-full object-cover" />
                </button>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="aspect-square bg-gray-100 rounded-xl flex items-center justify-center">
            <svg class="h-24 w-24 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>

        <!-- Lightbox -->
        <Teleport to="body">
            <Transition name="fade">
                <div
                    v-if="showLightbox"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
                    @click="closeLightbox"
                >
                    <button
                        @click="closeLightbox"
                        class="absolute top-4 right-4 p-2 text-white hover:text-gray-300 transition-colors"
                    >
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <button
                        v-if="allImages.length > 1"
                        @click.stop="prevImage"
                        class="absolute left-4 p-3 text-white hover:text-gray-300 transition-colors bg-black/20 rounded-full hover:bg-black/40"
                    >
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <img
                        :src="activeImage"
                        alt="Product image"
                        class="max-h-[90vh] max-w-[90vw] object-contain"
                        @click.stop
                    />

                    <button
                        v-if="allImages.length > 1"
                        @click.stop="nextImage"
                        class="absolute right-4 p-3 text-white hover:text-gray-300 transition-colors bg-black/20 rounded-full hover:bg-black/40"
                    >
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Image counter -->
                    <div v-if="allImages.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-4">
                        <span class="text-white text-sm">{{ activeIndex + 1 }} / {{ allImages.length }}</span>
                        <div class="flex gap-2">
                            <button
                                v-for="(image, index) in allImages"
                                :key="`lightbox-dot-${image.id}`"
                                @click.stop="setActiveImage(index)"
                                :class="[
                                    'w-2.5 h-2.5 rounded-full transition-all',
                                    activeIndex === index ? 'bg-white scale-125' : 'bg-white/50 hover:bg-white/75'
                                ]"
                            />
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
