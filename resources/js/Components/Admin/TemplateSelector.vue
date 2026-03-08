<script setup lang="ts">
import { computed, ref } from 'vue';
import TemplatePreview from './TemplatePreview.vue';
import type { TemplateConfig, TemplateDefinition } from '@/types/admin';

interface Props {
    modelValue: string;
    config: TemplateConfig;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const activeCategory = ref<string | 'all'>('all');

const categories = computed(() => {
    const cats = [{ slug: 'all', label: 'All' }];
    for (const [slug, info] of Object.entries(props.config.categories)) {
        cats.push({ slug, label: info.label });
    }
    return cats;
});

const filteredTemplates = computed((): TemplateDefinition[] => {
    const templates = Object.values(props.config.templates);
    if (activeCategory.value === 'all') {
        return templates;
    }
    return templates.filter(t => t.category === activeCategory.value);
});

const selectTemplate = (slug: string) => {
    emit('update:modelValue', slug);
};

const isSelected = (slug: string) => props.modelValue === slug;
</script>

<template>
    <div class="template-selector">
        <!-- Category Tabs -->
        <div class="flex gap-1 mb-4 border-b border-gray-200">
            <button
                v-for="cat in categories"
                :key="cat.slug"
                type="button"
                @click="activeCategory = cat.slug"
                :class="[
                    'px-3 py-2 text-sm font-medium border-b-2 -mb-px transition-colors',
                    activeCategory === cat.slug
                        ? 'border-gray-700 text-gray-700'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
            >
                {{ cat.label }}
            </button>
        </div>

        <!-- Template Grid -->
        <div class="grid grid-cols-2 gap-3">
            <button
                v-for="template in filteredTemplates"
                :key="template.slug"
                type="button"
                @click="selectTemplate(template.slug)"
                :class="[
                    'relative rounded-lg border-2 p-3 text-left transition-all',
                    isSelected(template.slug)
                        ? 'border-gray-700 bg-gray-50 ring-2 ring-gray-700'
                        : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                ]"
            >
                <!-- Preview -->
                <div class="aspect-[4/3] mb-2 bg-gray-100 rounded overflow-hidden flex items-center justify-center">
                    <TemplatePreview :template="template.slug" class="w-full h-full" />
                </div>

                <!-- Template Info -->
                <div>
                    <h4 :class="[
                        'text-sm font-medium',
                        isSelected(template.slug) ? 'text-indigo-900' : 'text-gray-900'
                    ]">
                        {{ template.name }}
                    </h4>
                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">
                        {{ template.description }}
                    </p>
                </div>

                <!-- Selected Indicator -->
                <div
                    v-if="isSelected(template.slug)"
                    class="absolute top-2 right-2 w-5 h-5 rounded-full bg-gray-700 flex items-center justify-center"
                >
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
        </div>
    </div>
</template>
