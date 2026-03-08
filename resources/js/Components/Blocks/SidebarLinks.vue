<script setup lang="ts">
import { Link as InertiaLink } from '@inertiajs/vue3';

interface LinkItem {
    text: string;
    url: string;
}

interface Props {
    title?: string;
    links?: LinkItem[];
    style?: 'list' | 'buttons';
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Quick Links',
    links: () => [],
    style: 'list',
});

const isExternalLink = (url: string | undefined): boolean => {
    if (!url) return false;
    return url.includes('://');
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <h3 v-if="props.title" class="text-lg font-semibold text-gray-900 mb-4">
            {{ props.title }}
        </h3>

        <!-- List style -->
        <nav v-if="props.style === 'list'" class="space-y-2" aria-label="Quick links">
            <component
                v-for="(link, index) in props.links"
                :key="index"
                :is="isExternalLink(link.url) ? 'a' : InertiaLink"
                :href="link.url"
                :target="isExternalLink(link.url) ? '_blank' : undefined"
                :rel="isExternalLink(link.url) ? 'noopener noreferrer' : undefined"
                class="block text-sm text-gray-600 hover:text-gray-900 hover:underline"
            >
                {{ link.text }}
            </component>
        </nav>

        <!-- Buttons style -->
        <div v-else class="space-y-2">
            <component
                v-for="(link, index) in props.links"
                :key="index"
                :is="isExternalLink(link.url) ? 'a' : InertiaLink"
                :href="link.url"
                :target="isExternalLink(link.url) ? '_blank' : undefined"
                :rel="isExternalLink(link.url) ? 'noopener noreferrer' : undefined"
                class="block w-full text-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors"
            >
                {{ link.text }}
            </component>
        </div>
    </div>
</template>
