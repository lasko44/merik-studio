<script setup lang="ts">
import { ref, computed } from 'vue';
import draggable from 'vuedraggable';
import BlockWrapper from './BlockWrapper.vue';
import BlockSelector from './BlockSelector.vue';
import type { ContentBlock, PageComponent } from '@/types/admin';

interface ComponentCategory {
    label: string;
    components: PageComponent[];
}

interface AvailablePage {
    id: number;
    title: string;
    path: string;
}

interface Props {
    modelValue: ContentBlock[];
    components: Record<string, ComponentCategory>;
    availablePages?: AvailablePage[];
}

const props = withDefaults(defineProps<Props>(), {
    availablePages: () => [],
});

const emit = defineEmits<{
    'update:modelValue': [value: ContentBlock[]];
}>();

const showSelector = ref(false);
const insertIndex = ref<number | null>(null);
const isDragging = ref(false);

const blocks = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

// Filter to only show sidebar components
const sidebarComponents = computed(() => {
    const result: Record<string, ComponentCategory> = {};

    for (const [key, category] of Object.entries(props.components)) {
        // Only include sidebar category components
        const sidebarOnly = category.components.filter(c => c.category === 'sidebar');
        if (sidebarOnly.length > 0) {
            result[key] = {
                label: category.label,
                components: sidebarOnly,
            };
        }
    }

    return result;
});

const generateId = (): string => {
    return 'block_' + Math.random().toString(36).substring(2, 11);
};

const openSelector = (index: number | null = null) => {
    insertIndex.value = index;
    showSelector.value = true;
};

const closeSelector = () => {
    showSelector.value = false;
    insertIndex.value = null;
};

const addBlock = (component: PageComponent) => {
    const newBlock: ContentBlock = {
        id: generateId(),
        type: component.slug,
        data: { ...component.default_props },
    };

    const newBlocks = [...blocks.value];

    if (insertIndex.value !== null) {
        newBlocks.splice(insertIndex.value + 1, 0, newBlock);
    } else {
        newBlocks.push(newBlock);
    }

    blocks.value = newBlocks;
    closeSelector();
};

const updateBlock = (index: number, data: Record<string, unknown>) => {
    const newBlocks = [...blocks.value];
    newBlocks[index] = { ...newBlocks[index], data };
    blocks.value = newBlocks;
};

const removeBlock = (index: number) => {
    const newBlocks = [...blocks.value];
    newBlocks.splice(index, 1);
    blocks.value = newBlocks;
};

const moveBlock = (index: number, direction: 'up' | 'down') => {
    const newBlocks = [...blocks.value];
    const targetIndex = direction === 'up' ? index - 1 : index + 1;

    if (targetIndex < 0 || targetIndex >= newBlocks.length) {
        return;
    }

    [newBlocks[index], newBlocks[targetIndex]] = [newBlocks[targetIndex], newBlocks[index]];
    blocks.value = newBlocks;
};

const duplicateBlock = (index: number) => {
    const newBlocks = [...blocks.value];
    const blockToDuplicate = newBlocks[index];
    const duplicatedBlock: ContentBlock = {
        id: generateId(),
        type: blockToDuplicate.type,
        data: { ...blockToDuplicate.data },
    };
    newBlocks.splice(index + 1, 0, duplicatedBlock);
    blocks.value = newBlocks;
};

// Get component by slug from ALL components (not just sidebar, for rendering existing blocks)
const getComponentBySlug = (slug: string): PageComponent | undefined => {
    for (const category of Object.values(props.components)) {
        const component = category.components.find((c: PageComponent) => c.slug === slug);
        if (component) {
            return component;
        }
    }
    return undefined;
};

const onDragStart = () => {
    isDragging.value = true;
};

const onDragEnd = () => {
    isDragging.value = false;
};

const dragOptions = computed(() => ({
    animation: 200,
    group: 'sidebar-blocks',
    ghostClass: 'sidebar-ghost',
    chosenClass: 'sidebar-chosen',
    dragClass: 'sidebar-drag',
    handle: '.drag-handle',
    fallbackOnBody: true,
    swapThreshold: 0.65,
}));
</script>

<template>
    <div class="space-y-4">
        <!-- Empty State -->
        <div v-if="blocks.length === 0 && !isDragging" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors hover:border-gray-400">
            <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
            </svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900">No sidebar content</h3>
            <p class="mt-1 text-sm text-gray-500">Add widgets to customize your sidebar.</p>
            <button
                type="button"
                @click="openSelector(null)"
                class="mt-4 inline-flex items-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
            >
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Add Widget
            </button>
        </div>

        <!-- Drop zone when empty and dragging -->
        <div
            v-if="blocks.length === 0 && isDragging"
            class="border-2 border-dashed border-indigo-400 rounded-lg p-8 text-center bg-indigo-50 transition-all"
        >
            <svg class="mx-auto h-10 w-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
            <p class="mt-2 text-sm font-medium text-indigo-600">Drop widget here</p>
        </div>

        <!-- Blocks with Draggable -->
        <draggable
            v-if="blocks.length > 0"
            v-model="blocks"
            v-bind="dragOptions"
            item-key="id"
            class="space-y-3"
            @start="onDragStart"
            @end="onDragEnd"
        >
            <template #item="{ element: block, index }">
                <BlockWrapper
                    :block="block"
                    :component="getComponentBySlug(block.type)"
                    :components="components"
                    :available-pages="availablePages"
                    :index="index"
                    :is-first="index === 0"
                    :is-last="index === blocks.length - 1"
                    :is-dragging="isDragging"
                    @update="(data) => updateBlock(index, data)"
                    @remove="removeBlock(index)"
                    @move-up="moveBlock(index, 'up')"
                    @move-down="moveBlock(index, 'down')"
                    @duplicate="duplicateBlock(index)"
                    @add-below="openSelector(index)"
                />
            </template>
        </draggable>

        <!-- Add Block Button (when blocks exist) -->
        <div v-if="blocks.length > 0" class="flex justify-center">
            <button
                type="button"
                @click="openSelector(null)"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
            >
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Add Widget
            </button>
        </div>

        <!-- Block Selector Modal (only sidebar components) -->
        <BlockSelector
            :show="showSelector"
            :components="sidebarComponents"
            @select="addBlock"
            @close="closeSelector"
        />
    </div>
</template>

<style scoped>
/* Ghost element (placeholder where item will be dropped) */
.sidebar-ghost {
    @apply opacity-40 bg-indigo-100 border-2 border-dashed border-indigo-400 rounded-lg;
}

/* The element being dragged */
.sidebar-chosen {
    @apply ring-2 ring-indigo-500 ring-offset-2 shadow-lg;
}

/* The dragging element (follows cursor) */
.sidebar-drag {
    @apply opacity-90 shadow-2xl rotate-1 scale-[1.02];
}
</style>
