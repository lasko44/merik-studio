<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    image?: string;
    title?: string;
    category?: string;
    description?: string;
    link?: string;
    linkText?: string;
    aspectRatio?: 'square' | 'video' | 'portrait' | 'landscape';
    showOverlay?: boolean;
    overlayStyle?: 'gradient' | 'solid' | 'blur';
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Portfolio Item',
    aspectRatio: 'square',
    showOverlay: true,
    overlayStyle: 'gradient',
    linkText: 'View Project',
});

const aspectClass = computed(() => {
    return {
        square: 'aspect-square',
        video: 'aspect-video',
        portrait: 'aspect-[3/4]',
        landscape: 'aspect-[4/3]',
    }[props.aspectRatio] || 'aspect-square';
});

const overlayClass = computed(() => {
    return {
        gradient: 'bg-gradient-to-t from-black/80 via-black/40 to-transparent',
        solid: 'bg-black/60',
        blur: 'bg-black/40 backdrop-blur-sm',
    }[props.overlayStyle] || 'bg-gradient-to-t from-black/80 via-black/40 to-transparent';
});
</script>

<template>
    <div class="group relative overflow-hidden rounded-xl bg-gray-100">
        <!-- Image Container -->
        <div :class="['relative overflow-hidden', aspectClass]">
            <!-- Image -->
            <img
                v-if="image"
                :src="image"
                :alt="title"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
            />
            <!-- Placeholder when no image -->
            <div v-else class="flex h-full w-full items-center justify-center bg-gray-200">
                <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>

            <!-- Overlay -->
            <div
                v-if="showOverlay"
                :class="[
                    'absolute inset-0 transition-opacity duration-300',
                    overlayClass,
                    'opacity-0 group-hover:opacity-100'
                ]"
            />

            <!-- Content Overlay -->
            <div
                v-if="showOverlay"
                class="absolute inset-0 flex flex-col justify-end p-6 opacity-0 transition-all duration-300 group-hover:opacity-100"
            >
                <!-- Category Badge -->
                <span
                    v-if="category"
                    class="mb-2 inline-block w-fit rounded-full bg-white/20 px-3 py-1 text-xs font-medium uppercase tracking-wide text-white backdrop-blur-sm"
                >
                    {{ category }}
                </span>

                <!-- Title -->
                <h3 class="text-xl font-bold text-white">{{ title }}</h3>

                <!-- Description -->
                <p v-if="description" class="mt-2 line-clamp-2 text-sm text-white/80">
                    {{ description }}
                </p>

                <!-- Link -->
                <a
                    v-if="link"
                    :href="link"
                    class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-white hover:underline"
                >
                    {{ linkText }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Static Info (shown when overlay is disabled) -->
        <div v-if="!showOverlay" class="p-4">
            <span
                v-if="category"
                class="mb-2 inline-block text-xs font-medium uppercase tracking-wide text-gray-500"
            >
                {{ category }}
            </span>
            <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
            <p v-if="description" class="mt-1 line-clamp-2 text-sm text-gray-600">
                {{ description }}
            </p>
            <a
                v-if="link"
                :href="link"
                class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500"
            >
                {{ linkText }}
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        <!-- Clickable overlay for linked items -->
        <a
            v-if="link && showOverlay"
            :href="link"
            class="absolute inset-0 z-10"
            :aria-label="title"
        />
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
