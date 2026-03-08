<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    options?: {
        container_width?: string;
        show_toc?: boolean;
    };
}

const props = withDefaults(defineProps<Props>(), {
    options: () => ({}),
});

const containerClass = computed(() => {
    const width = props.options?.container_width || 'max-w-3xl';
    return `${width} mx-auto px-4 sm:px-6 lg:px-8 py-12`;
});

const showToc = computed(() => props.options?.show_toc ?? false);
</script>

<template>
    <div :class="containerClass">
        <article class="prose prose-lg prose-indigo max-w-none">
            <slot />
        </article>

        <!-- Table of Contents (if enabled) -->
        <aside v-if="showToc" class="hidden xl:block fixed right-8 top-32 w-64">
            <slot name="toc" />
        </aside>
    </div>
</template>
