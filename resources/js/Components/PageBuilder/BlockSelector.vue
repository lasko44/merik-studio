<script setup lang="ts">
import Modal from '@/Components/Modal.vue';
import { ref, computed } from 'vue';
import type { PageComponent } from '@/types/admin';

interface ComponentCategory {
    label: string;
    components: PageComponent[];
}

interface Props {
    show: boolean;
    components: Record<string, ComponentCategory>;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    select: [component: PageComponent];
    close: [];
}>();

const searchQuery = ref('');
const selectedCategory = ref<string | null>(null);

const categories = computed(() => {
    return Object.entries(props.components).map(([key, value]) => ({
        key,
        label: value.label,
        components: value.components,
    }));
});

const filteredComponents = computed(() => {
    let components: PageComponent[] = [];

    if (selectedCategory.value) {
        const category = props.components[selectedCategory.value];
        components = category?.components || [];
    } else {
        for (const category of Object.values(props.components)) {
            components.push(...category.components);
        }
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        components = components.filter(
            (c) =>
                c.name.toLowerCase().includes(query) ||
                c.description?.toLowerCase().includes(query)
        );
    }

    return components;
});

const iconPaths: Record<string, string> = {
    'hero': 'M4 5a2 2 0 012-2h8a2 2 0 012 2v3H4V5zm0 5v5a2 2 0 002 2h8a2 2 0 002-2v-5H4z',
    'text': 'M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1z',
    'features': 'M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
    'cta': 'M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z',
    'contact': 'M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z',
    'pricing': 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    'gallery': 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
    'video': 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
    'testimonial': 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
    'faq': 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    'default': 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
};

const getIconPath = (icon: string | null | undefined): string => {
    return iconPaths[icon || 'default'] || iconPaths['default'];
};

const selectComponent = (component: PageComponent) => {
    emit('select', component);
};

const close = () => {
    searchQuery.value = '';
    selectedCategory.value = null;
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="close" max-width="2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Add Block</h2>
                <button
                    type="button"
                    @click="close"
                    class="text-gray-400 hover:text-gray-500"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Search -->
            <div class="mb-4">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search blocks..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                />
            </div>

            <!-- Categories -->
            <div class="flex gap-2 mb-4 flex-wrap">
                <button
                    type="button"
                    @click="selectedCategory = null"
                    :class="[
                        'px-3 py-1.5 rounded-full text-sm font-medium transition-colors',
                        selectedCategory === null
                            ? 'bg-gray-100 text-gray-800'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                    ]"
                >
                    All
                </button>
                <button
                    v-for="category in categories"
                    :key="category.key"
                    type="button"
                    @click="selectedCategory = category.key"
                    :class="[
                        'px-3 py-1.5 rounded-full text-sm font-medium transition-colors',
                        selectedCategory === category.key
                            ? 'bg-gray-100 text-gray-800'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                    ]"
                >
                    {{ category.label }}
                </button>
            </div>

            <!-- Components Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
                <button
                    v-for="component in filteredComponents"
                    :key="component.id"
                    type="button"
                    @click="selectComponent(component)"
                    class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-gray-700 hover:bg-gray-50 transition-colors text-center"
                >
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(component.icon)" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ component.name }}</span>
                    <span v-if="component.description" class="mt-1 text-xs text-gray-500 line-clamp-2">
                        {{ component.description }}
                    </span>
                </button>
            </div>

            <!-- Empty State -->
            <div v-if="filteredComponents.length === 0" class="py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-2 text-sm text-gray-500">No blocks found</p>
            </div>
        </div>
    </Modal>
</template>
