<script setup lang="ts">
import { computed, watch } from 'vue';
import type { TemplateDefinition, TemplateOptions } from '@/types/admin';

interface Props {
    template: TemplateDefinition | null;
    modelValue: TemplateOptions;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: TemplateOptions): void;
}>();

const options = computed(() => props.template?.options || {});
const hasOptions = computed(() => Object.keys(options.value).length > 0);

const updateOption = (key: keyof TemplateOptions, value: TemplateOptions[keyof TemplateOptions]) => {
    emit('update:modelValue', {
        ...props.modelValue,
        [key]: value,
    });
};

const getOptionValue = <K extends keyof TemplateOptions>(key: K, defaultValue: TemplateOptions[K]) => {
    return props.modelValue[key] ?? defaultValue;
};

// Reset options when template changes
watch(() => props.template?.slug, () => {
    if (props.template) {
        emit('update:modelValue', { ...props.template.options });
    }
});

const containerWidthOptions = [
    { value: 'max-w-2xl', label: 'Small (max-w-2xl)' },
    { value: 'max-w-3xl', label: 'Narrow (max-w-3xl)' },
    { value: 'max-w-4xl', label: 'Medium (max-w-4xl)' },
    { value: 'max-w-5xl', label: 'Large (max-w-5xl)' },
    { value: 'max-w-6xl', label: 'Extra Large (max-w-6xl)' },
    { value: 'max-w-7xl', label: 'Full (max-w-7xl)' },
];

const sidebarWidthOptions = [
    { value: '1/4', label: 'Narrow (1/4)' },
    { value: '1/3', label: 'Standard (1/3)' },
    { value: '2/5', label: 'Wide (2/5)' },
];

const gridColumnOptions = [
    { value: 2, label: '2 Columns' },
    { value: 3, label: '3 Columns' },
    { value: 4, label: '4 Columns' },
];
</script>

<template>
    <div v-if="hasOptions" class="space-y-4">
        <!-- Container Width -->
        <div v-if="'container_width' in options">
            <label class="block text-sm font-medium text-gray-700 mb-1">Container Width</label>
            <select
                :value="getOptionValue('container_width', options.container_width)"
                @change="updateOption('container_width', ($event.target as HTMLSelectElement).value)"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
            >
                <option v-for="opt in containerWidthOptions" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>
        </div>

        <!-- Content Width (for landing template) -->
        <div v-if="'content_width' in options">
            <label class="block text-sm font-medium text-gray-700 mb-1">Content Section Width</label>
            <select
                :value="getOptionValue('content_width', options.content_width)"
                @change="updateOption('content_width', ($event.target as HTMLSelectElement).value)"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
            >
                <option v-for="opt in containerWidthOptions" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>
        </div>

        <!-- Sidebar Width -->
        <div v-if="'sidebar_width' in options">
            <label class="block text-sm font-medium text-gray-700 mb-1">Sidebar Width</label>
            <select
                :value="getOptionValue('sidebar_width', options.sidebar_width)"
                @change="updateOption('sidebar_width', ($event.target as HTMLSelectElement).value)"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
            >
                <option v-for="opt in sidebarWidthOptions" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>
        </div>

        <!-- Sidebar Sticky -->
        <div v-if="'sidebar_sticky' in options">
            <label class="flex items-center">
                <input
                    type="checkbox"
                    :checked="getOptionValue('sidebar_sticky', options.sidebar_sticky) as boolean"
                    @change="updateOption('sidebar_sticky', ($event.target as HTMLInputElement).checked)"
                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                />
                <span class="ml-2 text-sm text-gray-600">Sticky sidebar</span>
            </label>
        </div>

        <!-- Hero Full Width -->
        <div v-if="'hero_full_width' in options">
            <label class="flex items-center">
                <input
                    type="checkbox"
                    :checked="getOptionValue('hero_full_width', options.hero_full_width) as boolean"
                    @change="updateOption('hero_full_width', ($event.target as HTMLInputElement).checked)"
                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                />
                <span class="ml-2 text-sm text-gray-600">Full-width hero section</span>
            </label>
        </div>

        <!-- Show TOC -->
        <div v-if="'show_toc' in options">
            <label class="flex items-center">
                <input
                    type="checkbox"
                    :checked="getOptionValue('show_toc', options.show_toc) as boolean"
                    @change="updateOption('show_toc', ($event.target as HTMLInputElement).checked)"
                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                />
                <span class="ml-2 text-sm text-gray-600">Show table of contents</span>
            </label>
        </div>

        <!-- Show Nav -->
        <div v-if="'show_nav' in options">
            <label class="flex items-center">
                <input
                    type="checkbox"
                    :checked="getOptionValue('show_nav', options.show_nav) as boolean"
                    @change="updateOption('show_nav', ($event.target as HTMLInputElement).checked)"
                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                />
                <span class="ml-2 text-sm text-gray-600">Show navigation sidebar</span>
            </label>
        </div>

        <!-- Nav Sticky -->
        <div v-if="'nav_sticky' in options">
            <label class="flex items-center">
                <input
                    type="checkbox"
                    :checked="getOptionValue('nav_sticky', options.nav_sticky) as boolean"
                    @change="updateOption('nav_sticky', ($event.target as HTMLInputElement).checked)"
                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                />
                <span class="ml-2 text-sm text-gray-600">Sticky navigation</span>
            </label>
        </div>

        <!-- Grid Columns -->
        <div v-if="'grid_columns' in options">
            <label class="block text-sm font-medium text-gray-700 mb-1">Grid Columns</label>
            <select
                :value="getOptionValue('grid_columns', options.grid_columns)"
                @change="updateOption('grid_columns', parseInt(($event.target as HTMLSelectElement).value))"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
            >
                <option v-for="opt in gridColumnOptions" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>
        </div>

        <!-- Vertical Center -->
        <div v-if="'vertical_center' in options">
            <label class="flex items-center">
                <input
                    type="checkbox"
                    :checked="getOptionValue('vertical_center', options.vertical_center) as boolean"
                    @change="updateOption('vertical_center', ($event.target as HTMLInputElement).checked)"
                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                />
                <span class="ml-2 text-sm text-gray-600">Vertically center content</span>
            </label>
        </div>
    </div>

    <p v-else class="text-sm text-gray-500 italic">
        This template has no configurable options.
    </p>
</template>
