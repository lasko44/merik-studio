<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    options?: {
        container_width?: string;
        vertical_center?: boolean;
    };
}

const props = withDefaults(defineProps<Props>(), {
    options: () => ({}),
});

const containerClass = computed(() => {
    const width = props.options?.container_width || 'max-w-2xl';
    const verticalCenter = props.options?.vertical_center ?? false;
    const baseClasses = `${width} mx-auto px-4 sm:px-6 lg:px-8 py-12`;

    if (verticalCenter) {
        return `${baseClasses} min-h-[calc(100vh-200px)] flex items-center`;
    }
    return baseClasses;
});
</script>

<template>
    <div :class="containerClass">
        <div class="w-full">
            <slot />
        </div>
    </div>
</template>
