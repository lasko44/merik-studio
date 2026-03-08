<script setup lang="ts">
import { computed } from 'vue';
import { sanitizeHtml } from '@/utils/sanitize';

interface Props {
    title?: string;
    content?: string;
    variant?: 'default' | 'bordered' | 'filled';
}

const props = withDefaults(defineProps<Props>(), {
    title: '',
    content: '',
    variant: 'bordered',
});

const variantClasses = {
    default: 'bg-white',
    bordered: 'bg-white border border-gray-200 shadow-sm',
    filled: 'bg-gray-50 border border-gray-100',
}[props.variant];

const sanitizedContent = computed(() => sanitizeHtml(props.content));
</script>

<template>
    <div :class="[variantClasses, 'rounded-lg p-5']">
        <h3 v-if="props.title" class="text-lg font-semibold text-gray-900 mb-3">
            {{ props.title }}
        </h3>
        <div v-if="props.content" class="text-sm text-gray-600 prose prose-sm max-w-none" v-html="sanitizedContent"></div>
    </div>
</template>
