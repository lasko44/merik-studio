<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    options?: {
        container_width?: string;
        grid_columns?: number;
    };
}

const props = withDefaults(defineProps<Props>(), {
    options: () => ({}),
});

const containerClass = computed(() => {
    const width = props.options?.container_width || 'max-w-6xl';
    return `${width} mx-auto px-4 sm:px-6 lg:px-8 py-12`;
});

const gridClass = computed(() => {
    const cols = props.options?.grid_columns || 3;
    return {
        2: 'grid-cols-1 sm:grid-cols-2',
        3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        4: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    }[cols] || 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
});
</script>

<template>
    <div :class="containerClass">
        <!-- Header slot for title/intro -->
        <slot name="header" />

        <!-- Grid for portfolio items -->
        <div :class="['grid gap-6', gridClass]">
            <slot />
        </div>

        <!-- Footer slot for pagination/load more -->
        <slot name="footer" />
    </div>
</template>
