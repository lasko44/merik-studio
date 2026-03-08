<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    options?: {
        hero_full_width?: boolean;
        content_width?: string;
    };
}

const props = withDefaults(defineProps<Props>(), {
    options: () => ({}),
});

const heroFullWidth = computed(() => props.options?.hero_full_width ?? true);

const contentClass = computed(() => {
    const width = props.options?.content_width || 'max-w-6xl';
    return `${width} mx-auto px-4 sm:px-6 lg:px-8`;
});
</script>

<template>
    <div>
        <!-- Hero section (full width or contained) -->
        <div :class="{ 'w-full': heroFullWidth, [contentClass]: !heroFullWidth }">
            <slot name="hero" />
        </div>

        <!-- Content sections -->
        <div :class="contentClass">
            <slot name="content" />
        </div>
    </div>
</template>
