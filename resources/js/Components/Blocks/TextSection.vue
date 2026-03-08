<script setup lang="ts">
import { computed } from 'vue';
import { sanitizeHtml } from '@/utils/sanitize';

interface Props {
    content: string;
}

const props = defineProps<Props>();

const sanitizedContent = computed(() => sanitizeHtml(props.content));
</script>

<template>
    <section class="py-12 lg:py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="prose prose-lg max-w-none prose-headings:font-bold prose-h2:text-3xl prose-h3:text-2xl prose-a:text-indigo-600 prose-a:no-underline hover:prose-a:underline"
                v-html="sanitizedContent"
            />
        </div>
    </section>
</template>
