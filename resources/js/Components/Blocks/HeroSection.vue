<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { isExternalLink, getExternalLinkAttrs } from '@/utils/url';
import ArrowRightIcon from '@/Components/Icons/ArrowRightIcon.vue';

interface Props {
    heading?: string;
    subheading?: string;
    primaryButtonText?: string;
    primaryButtonLink?: string;
    secondaryButtonText?: string;
    secondaryButtonLink?: string;
    backgroundImage?: string | null;
    overlayOpacity?: number;
    alignment?: 'left' | 'center' | 'right';
    settings?: {
        primaryColor: string;
    };
}

const props = withDefaults(defineProps<Props>(), {
    heading: 'Welcome to Our Site',
    subheading: '',
    primaryButtonText: '',
    primaryButtonLink: '',
    secondaryButtonText: '',
    secondaryButtonLink: '',
    backgroundImage: null,
    overlayOpacity: 50,
    alignment: 'center',
});

const alignmentClasses = {
    left: 'text-left',
    center: 'text-center',
    right: 'text-right',
};
</script>

<template>
    <section
        class="relative py-20 lg:py-32 overflow-hidden"
        :class="backgroundImage ? 'text-white' : 'bg-gradient-to-br from-indigo-600 to-purple-700 text-white'"
    >
        <div
            v-if="backgroundImage"
            class="absolute inset-0 bg-cover bg-center"
            :style="{ backgroundImage: `url(${backgroundImage})` }"
        >
            <div
                class="absolute inset-0 bg-black"
                :style="{ opacity: overlayOpacity / 100 }"
            ></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" :class="alignmentClasses[alignment]">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                {{ heading }}
            </h1>
            <p v-if="subheading" class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl" :class="{ 'mx-auto': alignment === 'center' }">
                {{ subheading }}
            </p>
            <div class="flex flex-wrap gap-4" :class="{ 'justify-center': alignment === 'center', 'justify-end': alignment === 'right' }">
                <component
                    :is="isExternalLink(primaryButtonLink) ? 'a' : Link"
                    v-if="primaryButtonText && primaryButtonLink"
                    :href="primaryButtonLink"
                    v-bind="isExternalLink(primaryButtonLink) ? getExternalLinkAttrs() : {}"
                    class="inline-flex items-center px-8 py-4 text-lg font-semibold bg-white text-gray-900 rounded-lg hover:bg-gray-100 transition-colors shadow-lg"
                >
                    {{ primaryButtonText }}
                    <ArrowRightIcon size="md" class="ml-2" />
                    <span v-if="isExternalLink(primaryButtonLink)" class="sr-only">(opens in new window)</span>
                </component>
                <component
                    :is="isExternalLink(secondaryButtonLink) ? 'a' : Link"
                    v-if="secondaryButtonText && secondaryButtonLink"
                    :href="secondaryButtonLink"
                    v-bind="isExternalLink(secondaryButtonLink) ? getExternalLinkAttrs() : {}"
                    class="inline-flex items-center px-8 py-4 text-lg font-semibold bg-transparent border-2 border-white text-white rounded-lg hover:bg-white/10 transition-colors"
                >
                    {{ secondaryButtonText }}
                </component>
            </div>
        </div>
    </section>
</template>
