<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
    src?: string | null;
    alt?: string;
    caption?: string;
    rounded?: boolean;
    link?: string;
}

const props = withDefaults(defineProps<Props>(), {
    src: null,
    alt: '',
    caption: '',
    rounded: true,
    link: '',
});

const isExternalLink = (url: string | undefined): boolean => {
    if (!url) return false;
    return url.includes('://');
};

const getLinkComponent = () => {
    if (!props.link) return 'div';
    return isExternalLink(props.link) ? 'a' : Link;
};
</script>

<template>
    <figure v-if="props.src" class="overflow-hidden">
        <component
            :is="getLinkComponent()"
            :href="props.link || undefined"
            :target="props.link && isExternalLink(props.link) ? '_blank' : undefined"
            :rel="props.link && isExternalLink(props.link) ? 'noopener noreferrer' : undefined"
            :class="props.link ? 'block hover:opacity-90 transition-opacity' : ''"
        >
            <img
                :src="props.src"
                :alt="props.alt"
                :class="[
                    'w-full h-auto',
                    props.rounded ? 'rounded-lg' : ''
                ]"
            />
        </component>
        <figcaption v-if="props.caption" class="mt-2 text-xs text-gray-500 text-center">
            {{ props.caption }}
        </figcaption>
    </figure>
</template>
