<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    options?: {
        container_width?: string;
    };
}

const props = withDefaults(defineProps<Props>(), {
    options: () => ({}),
});

const containerClass = computed(() => {
    const width = props.options?.container_width || 'max-w-7xl';
    return `${width} mx-auto px-4 sm:px-6 lg:px-8 py-12`;
});
</script>

<template>
    <div :class="containerClass">
        <slot />
    </div>
</template>
