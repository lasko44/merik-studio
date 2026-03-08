<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { isExternalLink, getExternalLinkAttrs } from '@/utils/url';
import ArrowRightIcon from '@/Components/Icons/ArrowRightIcon.vue';

interface Props {
    headline: string;
    description?: string;
    button_text: string;
    button_link: string;
}

withDefaults(defineProps<Props>(), {
    description: '',
});
</script>

<template>
    <section class="cta-section py-16 lg:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                {{ headline }}
            </h2>
            <p v-if="description" class="text-xl text-white/90 mb-8">
                {{ description }}
            </p>
            <component
                :is="isExternalLink(button_link) ? 'a' : Link"
                :href="button_link"
                v-bind="isExternalLink(button_link) ? getExternalLinkAttrs() : {}"
                class="inline-flex items-center px-8 py-4 text-lg font-semibold bg-white text-gray-900 rounded-lg hover:bg-gray-100 transition-colors shadow-lg"
            >
                {{ button_text }}
                <ArrowRightIcon size="md" class="ml-2" />
                <span v-if="isExternalLink(button_link)" class="sr-only">(opens in new window)</span>
            </component>
        </div>
    </section>
</template>

<style scoped>
.cta-section {
    background-color: var(--primary-color);
}
</style>
