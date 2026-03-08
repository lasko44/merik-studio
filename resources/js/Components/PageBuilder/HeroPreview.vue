<script setup lang="ts">
interface Props {
    heading?: string;
    subheading?: string;
    backgroundImage?: string | null;
    overlayOpacity?: number;
    alignment?: 'left' | 'center' | 'right';
}

const props = withDefaults(defineProps<Props>(), {
    heading: 'Welcome to Our Site',
    subheading: '',
    backgroundImage: null,
    overlayOpacity: 50,
    alignment: 'center',
});

const alignmentClasses = {
    left: 'text-left',
    center: 'text-center',
    right: 'text-right',
};
</script>

<template>
    <div class="rounded-lg overflow-hidden border border-gray-200 shadow-sm">
        <div class="text-xs text-gray-500 bg-gray-50 px-3 py-1.5 border-b border-gray-200">
            Live Preview
        </div>
        <div
            class="relative py-8 px-4 overflow-hidden"
            :class="backgroundImage ? 'text-white' : 'bg-gradient-to-br from-indigo-600 to-purple-700 text-white'"
            style="min-height: 140px;"
        >
            <div
                v-if="backgroundImage"
                class="absolute inset-0 bg-cover bg-center"
                :style="{ backgroundImage: `url(${backgroundImage})` }"
            >
                <div
                    class="absolute inset-0 bg-black"
                    :style="{ opacity: overlayOpacity / 100 }"
                ></div>
            </div>

            <div class="relative" :class="alignmentClasses[alignment]">
                <h3 class="text-lg font-bold mb-1 leading-tight line-clamp-2">
                    {{ heading || 'Heading Preview' }}
                </h3>
                <p v-if="subheading" class="text-sm text-white/90 line-clamp-2">
                    {{ subheading }}
                </p>
            </div>
        </div>
    </div>
</template>
