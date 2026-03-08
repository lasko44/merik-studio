<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    title?: string;
    description?: string;
    buttonText?: string;
    buttonLink?: string;
    variant?: 'primary' | 'secondary' | 'accent';
}

const props = withDefaults(defineProps<Props>(), {
    title: '',
    description: '',
    buttonText: 'Learn More',
    buttonLink: '#',
    variant: 'primary',
});

const buttonClasses = computed(() => {
    return {
        primary: 'sidebar-cta-btn-primary text-white hover:opacity-90',
        secondary: 'bg-gray-800 hover:bg-gray-900 text-white',
        accent: 'sidebar-cta-btn-accent text-white hover:opacity-90',
    }[props.variant];
});

const isExternalLink = (url: string | undefined): boolean => {
    if (!url) return false;
    return url.includes('://');
};
</script>

<template>
    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg border border-gray-200 p-5 text-center">
        <h3 v-if="props.title" class="text-lg font-semibold text-gray-900 mb-2">
            {{ props.title }}
        </h3>
        <p v-if="props.description" class="text-sm text-gray-600 mb-4">
            {{ props.description }}
        </p>
        <component
            :is="isExternalLink(props.buttonLink) ? 'a' : Link"
            :href="props.buttonLink"
            :target="isExternalLink(props.buttonLink) ? '_blank' : undefined"
            :rel="isExternalLink(props.buttonLink) ? 'noopener noreferrer' : undefined"
            :class="[buttonClasses, 'inline-block w-full px-4 py-2.5 text-sm font-semibold rounded-md transition-colors']"
        >
            {{ props.buttonText }}
        </component>
    </div>
</template>

<style scoped>
.sidebar-cta-btn-primary {
    background-color: var(--primary-color);
}

.sidebar-cta-btn-accent {
    background-color: var(--accent-color);
}
</style>
