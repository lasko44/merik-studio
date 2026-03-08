<script setup lang="ts">
interface Props {
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
    class?: string;
}

withDefaults(defineProps<Props>(), {
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
</script>

<template>
    <svg
        :class="[sizeClasses[size], $props.class]"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        aria-hidden="true"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M17 8l4 4m0 0l-4 4m4-4H3"
        />
    </svg>
</template>
