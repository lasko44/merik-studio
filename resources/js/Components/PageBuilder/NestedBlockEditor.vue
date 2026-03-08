<script setup lang="ts">
import { ref, computed, nextTick } from 'vue';
import draggable from 'vuedraggable';
import ImageUploader from '@/Components/Admin/ImageUploader.vue';
import RichTextEditor from '@/Components/Admin/RichTextEditor.vue';
import type { ContentBlock, PageComponent } from '@/types/admin';

interface ComponentCategory {
    label: string;
    components: PageComponent[];
}

interface Props {
    modelValue: ContentBlock[];
    components: Record<string, ComponentCategory>;
    label?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: () => [],
    label: 'Content',
});

const emit = defineEmits<{
    'update:modelValue': [value: ContentBlock[]];
}>();

const blocks = computed({
    get: () => props.modelValue || [],
    set: (value) => emit('update:modelValue', value),
});

const showSelector = ref(false);
const addButtonRef = ref<HTMLButtonElement | null>(null);
const dropdownStyle = ref<Record<string, string>>({});
const isDragging = ref(false);
const expandedBlocks = ref<Set<string>>(new Set());

const toggleBlockExpanded = (blockId: string) => {
    if (expandedBlocks.value.has(blockId)) {
        expandedBlocks.value.delete(blockId);
    } else {
        expandedBlocks.value.add(blockId);
    }
};

const isBlockExpanded = (blockId: string) => {
    return expandedBlocks.value.has(blockId);
};

const openSelector = async () => {
    showSelector.value = true;
    await nextTick();
    updateDropdownPosition();
};

const closeSelector = () => {
    showSelector.value = false;
};

const updateDropdownPosition = () => {
    if (!addButtonRef.value) return;

    const rect = addButtonRef.value.getBoundingClientRect();
    const spaceBelow = window.innerHeight - rect.bottom;
    const dropdownHeight = 256; // max-h-64 = 16rem = 256px

    // Position above if not enough space below
    if (spaceBelow < dropdownHeight && rect.top > spaceBelow) {
        dropdownStyle.value = {
            position: 'fixed',
            bottom: `${window.innerHeight - rect.top + 4}px`,
            left: `${rect.left}px`,
            width: `${rect.width}px`,
            zIndex: '9999',
        };
    } else {
        dropdownStyle.value = {
            position: 'fixed',
            top: `${rect.bottom + 4}px`,
            left: `${rect.left}px`,
            width: `${rect.width}px`,
            zIndex: '9999',
        };
    }
};

const generateId = (): string => {
    return 'block_' + Math.random().toString(36).substring(2, 11);
};

// Flatten components for easier access
const allComponents = computed(() => {
    const result: PageComponent[] = [];
    for (const category of Object.values(props.components)) {
        // Filter out layout components to prevent infinite nesting
        const nonLayoutComponents = category.components.filter(c => c.category !== 'layout');
        result.push(...nonLayoutComponents);
    }
    return result;
});

const addBlock = (component: PageComponent) => {
    const newBlock: ContentBlock = {
        id: generateId(),
        type: component.slug,
        data: { ...component.default_props },
    };

    blocks.value = [...blocks.value, newBlock];
    showSelector.value = false;
    // Auto-expand newly added block
    expandedBlocks.value.add(newBlock.id);
};

const updateBlockData = (index: number, key: string, value: unknown) => {
    const newBlocks = [...blocks.value];
    newBlocks[index] = {
        ...newBlocks[index],
        data: { ...newBlocks[index].data, [key]: value }
    };
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
    if (targetIndex < 0 || targetIndex >= newBlocks.length) return;
    [newBlocks[index], newBlocks[targetIndex]] = [newBlocks[targetIndex], newBlocks[index]];
    blocks.value = newBlocks;
};

const getComponentBySlug = (slug: string): PageComponent | undefined => {
    return allComponents.value.find(c => c.slug === slug);
};

const getBlockLabel = (type: string): string => {
    const component = getComponentBySlug(type);
    return component?.name || type;
};

const getFieldType = (component: PageComponent | undefined, key: string): string => {
    if (component?.prop_schema && component.prop_schema[key]) {
        return component.prop_schema[key].type;
    }
    return 'text';
};

const getFieldLabel = (component: PageComponent | undefined, key: string): string => {
    if (component?.prop_schema && component.prop_schema[key]) {
        return component.prop_schema[key].label;
    }
    return key.replace(/([A-Z])/g, ' $1').replace(/_/g, ' ').trim();
};

const getFieldOptions = (component: PageComponent | undefined, key: string): (string | number)[] => {
    if (component?.prop_schema && component.prop_schema[key]) {
        return component.prop_schema[key].options || [];
    }
    return [];
};

// Group components by category for the selector
const groupedComponents = computed(() => {
    const groups: Record<string, PageComponent[]> = {};
    for (const [key, category] of Object.entries(props.components)) {
        // Filter out layout components
        const nonLayout = category.components.filter(c => c.category !== 'layout');
        if (nonLayout.length > 0) {
            groups[category.label] = nonLayout;
        }
    }
    return groups;
});

const onDragStart = () => {
    isDragging.value = true;
};

const onDragEnd = () => {
    isDragging.value = false;
};

// When a block is cloned/added from another list, give it a new unique ID
const onClone = (evt: { item: HTMLElement }) => {
    // The cloned element will get processed, we handle ID in onAdd
};

const onAdd = () => {
    // After a block is added via drag, ensure all blocks have unique IDs
    const seen = new Set<string>();
    const newBlocks = blocks.value.map(block => {
        if (seen.has(block.id)) {
            // Duplicate ID found, generate new one
            return { ...block, id: generateId(), data: { ...block.data } };
        }
        seen.add(block.id);
        return block;
    });

    // Check if any IDs were duplicated
    if (newBlocks.some((b, i) => b !== blocks.value[i])) {
        blocks.value = newBlocks;
    }
};

const dragOptions = computed(() => ({
    animation: 200,
    group: {
        name: 'page-blocks',
        pull: false,
        put: true,
    },
    ghostClass: 'nested-ghost',
    chosenClass: 'nested-chosen',
    dragClass: 'nested-drag',
    handle: '.nested-drag-handle',
    fallbackOnBody: true,
    swapThreshold: 0.65,
}));
</script>

<template>
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">{{ label }}</span>
            <span class="text-xs text-gray-500">Drag blocks here or add new</span>
        </div>

        <!-- Blocks list with drag and drop -->
        <draggable
            v-model="blocks"
            v-bind="dragOptions"
            item-key="id"
            class="space-y-2 min-h-[60px] rounded-lg transition-colors"
            :class="[
                blocks.length === 0 ? 'border-2 border-dashed p-4' : '',
                isDragging ? 'border-indigo-400 bg-indigo-50' : 'border-gray-300 bg-gray-50'
            ]"
            @start="onDragStart"
            @end="onDragEnd"
            @add="onAdd"
        >
            <template #item="{ element: block, index }">
                <div class="border border-gray-200 rounded-lg bg-white overflow-hidden shadow-sm">
                    <!-- Block header -->
                    <div class="flex items-center justify-between px-3 py-2 bg-gray-50 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <!-- Drag handle -->
                            <div class="nested-drag-handle cursor-grab active:cursor-grabbing text-gray-400 hover:text-gray-600 p-0.5">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"/>
                                </svg>
                            </div>

                            <!-- Expand/collapse -->
                            <button
                                type="button"
                                @click="toggleBlockExpanded(block.id)"
                                class="p-0.5 text-gray-500 hover:text-gray-700"
                            >
                                <svg
                                    class="w-3.5 h-3.5 transition-transform duration-200"
                                    :class="{ 'rotate-90': isBlockExpanded(block.id) }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <span class="text-sm font-medium text-gray-700">{{ getBlockLabel(block.type) }}</span>
                        </div>
                        <div class="flex items-center gap-0.5">
                            <!-- Move buttons -->
                            <div class="flex items-center border border-gray-200 rounded bg-white">
                                <button
                                    type="button"
                                    @click="moveBlock(index, 'up')"
                                    :disabled="index === 0"
                                    :class="['p-1 text-gray-400 border-r border-gray-200', index === 0 ? 'opacity-40' : 'hover:text-gray-600']"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    @click="moveBlock(index, 'down')"
                                    :disabled="index === blocks.length - 1"
                                    :class="['p-1 text-gray-400', index === blocks.length - 1 ? 'opacity-40' : 'hover:text-gray-600']"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                            <button
                                type="button"
                                @click="removeBlock(index)"
                                class="p-1 rounded text-gray-400 hover:text-red-600 hover:bg-red-50"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Block fields (collapsible) -->
                    <div v-show="isBlockExpanded(block.id)" class="p-3 space-y-3">
                        <template v-for="(value, key) in block.data" :key="key">
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-gray-600">
                                    {{ getFieldLabel(getComponentBySlug(block.type), String(key)) }}
                                </label>

                                <!-- Image field -->
                                <template v-if="getFieldType(getComponentBySlug(block.type), String(key)) === 'image'">
                                    <ImageUploader
                                        :model-value="(value as string | null)"
                                        @update:model-value="updateBlockData(index, String(key), $event)"
                                    />
                                </template>

                                <!-- Select field -->
                                <template v-else-if="getFieldType(getComponentBySlug(block.type), String(key)) === 'select'">
                                    <select
                                        :value="value"
                                        @change="updateBlockData(index, String(key), ($event.target as HTMLSelectElement).value)"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 text-sm"
                                    >
                                        <option
                                            v-for="opt in getFieldOptions(getComponentBySlug(block.type), String(key))"
                                            :key="opt"
                                            :value="opt"
                                        >
                                            {{ opt }}
                                        </option>
                                    </select>
                                </template>

                                <!-- Boolean field -->
                                <template v-else-if="getFieldType(getComponentBySlug(block.type), String(key)) === 'boolean'">
                                    <input
                                        type="checkbox"
                                        :checked="(value as boolean)"
                                        @change="updateBlockData(index, String(key), ($event.target as HTMLInputElement).checked)"
                                        class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                                    />
                                </template>

                                <!-- Rich text field -->
                                <template v-else-if="getFieldType(getComponentBySlug(block.type), String(key)) === 'richtext'">
                                    <RichTextEditor
                                        :model-value="(value as string) || ''"
                                        :placeholder="`Enter ${getFieldLabel(getComponentBySlug(block.type), String(key)).toLowerCase()}...`"
                                        @update:model-value="updateBlockData(index, String(key), $event)"
                                    />
                                </template>

                                <!-- Textarea field -->
                                <template v-else-if="getFieldType(getComponentBySlug(block.type), String(key)) === 'textarea'">
                                    <textarea
                                        :value="(value as string) || ''"
                                        @input="updateBlockData(index, String(key), ($event.target as HTMLTextAreaElement).value)"
                                        rows="3"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 text-sm"
                                    ></textarea>
                                </template>

                                <!-- Skip complex types like repeater/blocks/array in nested editor -->
                                <template v-else-if="['repeater', 'blocks', 'array'].includes(getFieldType(getComponentBySlug(block.type), String(key)))">
                                    <div class="text-xs text-gray-500 bg-gray-50 rounded p-2">
                                        Complex field - edit in main editor
                                    </div>
                                </template>

                                <!-- Default text field -->
                                <template v-else>
                                    <input
                                        type="text"
                                        :value="(value as string) || ''"
                                        @input="updateBlockData(index, String(key), ($event.target as HTMLInputElement).value)"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 text-sm"
                                    />
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </draggable>

        <!-- Empty state text -->
        <div v-if="blocks.length === 0 && !isDragging" class="text-center text-sm text-gray-500">
            Drop blocks here or click Add Block
        </div>

        <!-- Add block button -->
        <div>
            <button
                ref="addButtonRef"
                type="button"
                @click="openSelector"
                class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Block
            </button>
        </div>

        <!-- Block selector dropdown (teleported to body to avoid clipping) -->
        <Teleport to="body">
            <div v-if="showSelector">
                <!-- Backdrop -->
                <div
                    class="fixed inset-0"
                    style="z-index: 9998;"
                    @click="closeSelector"
                ></div>

                <!-- Dropdown -->
                <div
                    :style="dropdownStyle"
                    class="bg-white border border-gray-200 rounded-lg shadow-lg max-h-64 overflow-y-auto"
                >
                    <div v-for="(comps, category) in groupedComponents" :key="category" class="p-2">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 py-1">
                            {{ category }}
                        </div>
                        <button
                            v-for="comp in comps"
                            :key="comp.slug"
                            type="button"
                            @click="addBlock(comp); closeSelector()"
                            class="w-full text-left px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-800 rounded"
                        >
                            {{ comp.name }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
/* Ghost element (placeholder where item will be dropped) */
.nested-ghost {
    @apply opacity-40 bg-indigo-100 border-2 border-dashed border-indigo-400 rounded-lg;
}

/* The element being dragged */
.nested-chosen {
    @apply ring-2 ring-indigo-500 ring-offset-1 shadow-lg;
}

/* The dragging element (follows cursor) */
.nested-drag {
    @apply opacity-90 shadow-xl;
}
</style>
