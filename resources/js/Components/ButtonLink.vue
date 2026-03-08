<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { isExternalLink, getExternalLinkAttrs } from '@/utils/url';
import ArrowRightIcon from '@/Components/Icons/ArrowRightIcon.vue';

interface Props {
    href: string;
    variant?: 'primary' | 'secondary' | 'outline' | 'ghost';
    size?: 'sm' | 'md' | 'lg';
    showArrow?: boolean;
    disabled?: boolean;
    fullWidth?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'primary',
    size: 'md',
    showArrow: false,
    disabled: false,
    fullWidth: false,
});

const isExternal = computed(() => isExternalLink(props.href));
const externalAttrs = computed(() => isExternal.value ? getExternalLinkAttrs() : {});

const baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

const variantClasses: Record<string, string> = {
    primary: 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 shadow-sm',
    secondary: 'bg-white text-gray-900 border border-gray-300 hover:bg-gray-50 focus:ring-gray-500',
    outline: 'bg-transparent text-primary-600 border-2 border-primary-600 hover:bg-primary-50 focus:ring-primary-500',
    ghost: 'bg-transparent text-gray-700 hover:bg-gray-100 focus:ring-gray-500',
};

const sizeClasses: Record<string, string> = {
    sm: 'px-3 py-1.5 text-sm gap-1.5',
    md: 'px-5 py-2.5 text-base gap-2',
    lg: 'px-7 py-3.5 text-lg gap-2.5',
};

const buttonClasses = computed(() => {
    const classes = [
        baseClasses,
        variantClasses[props.variant],
        sizeClasses[props.size],
    ];

    if (props.fullWidth) {
        classes.push('w-full');
    }

    if (props.disabled) {
        classes.push('opacity-50 cursor-not-allowed pointer-events-none');
    }

    return classes.join(' ');
});

const arrowSize = computed(() => {
    return props.size === 'sm' ? 'sm' : props.size === 'lg' ? 'lg' : 'md';
});
</script>

<template>
    <component
        :is="isExternal ? 'a' : Link"
        :href="href"
        :class="buttonClasses"
        v-bind="externalAttrs"
    >
        <slot />
        <ArrowRightIcon v-if="showArrow" :size="arrowSize" class="ml-1" />
    </component>
</template>
