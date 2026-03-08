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
            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
        />
    </svg>
</template>
