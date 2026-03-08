<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    size: 'md',
    class: '',
});

const sizeClasses: Record<string, string> = {
    xs: 'w-3 h-3',
    sm: 'w-4 h-4',
    md: 'w-5 h-5',
    lg: 'w-6 h-6',
    xl: 'w-8 h-8',
};

const iconClass = computed(() => {
    return `${sizeClasses[props.size]} ${props.class}`.trim();
});
</script>

<template>
    <svg
        :class="iconClass"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        aria-hidden="true"
    >
        <slot />
    </svg>
</template>
