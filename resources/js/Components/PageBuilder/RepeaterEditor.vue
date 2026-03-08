<script setup lang="ts">
import { computed } from 'vue';
import ImageUploader from '@/Components/Admin/ImageUploader.vue';
import RichTextEditor from '@/Components/Admin/RichTextEditor.vue';
import IconPicker from '@/Components/Admin/IconPicker.vue';

interface FieldSchema {
    type: string;
    label: string;
    options?: (string | number)[];
}

interface Props {
    modelValue: Record<string, unknown>[];
    fields: Record<string, FieldSchema>;
    label?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: () => [],
    label: 'Items',
});

const emit = defineEmits<{
    'update:modelValue': [value: Record<string, unknown>[]];
}>();

const items = computed({
    get: () => props.modelValue || [],
    set: (value) => emit('update:modelValue', value),
});

const addItem = () => {
    const newItem: Record<string, unknown> = {};
    for (const key of Object.keys(props.fields)) {
        newItem[key] = '';
    }
    items.value = [...items.value, newItem];
};

const removeItem = (index: number) => {
    const newItems = [...items.value];
    newItems.splice(index, 1);
    items.value = newItems;
};

const updateItem = (index: number, key: string, value: unknown) => {
    const newItems = [...items.value];
    newItems[index] = { ...newItems[index], [key]: value };
    items.value = newItems;
};

const moveItem = (index: number, direction: 'up' | 'down') => {
    const newItems = [...items.value];
    const targetIndex = direction === 'up' ? index - 1 : index + 1;
    if (targetIndex < 0 || targetIndex >= newItems.length) return;
    [newItems[index], newItems[targetIndex]] = [newItems[targetIndex], newItems[index]];
    items.value = newItems;
};

const getFieldLabel = (key: string): string => {
    return props.fields[key]?.label || key;
};

// String list helpers (for arrays of strings within repeater items)
const addStringListItem = (itemIndex: number, fieldKey: string) => {
    const newItems = [...items.value];
    const currentList = (newItems[itemIndex][fieldKey] as string[]) || [];
    newItems[itemIndex] = {
        ...newItems[itemIndex],
        [fieldKey]: [...currentList, ''],
    };
    items.value = newItems;
};

const removeStringListItem = (itemIndex: number, fieldKey: string, strIndex: number) => {
    const newItems = [...items.value];
    const currentList = [...((newItems[itemIndex][fieldKey] as string[]) || [])];
    currentList.splice(strIndex, 1);
    newItems[itemIndex] = {
        ...newItems[itemIndex],
        [fieldKey]: currentList,
    };
    items.value = newItems;
};

const updateStringListItem = (itemIndex: number, fieldKey: string, strIndex: number, value: string) => {
    const newItems = [...items.value];
    const currentList = [...((newItems[itemIndex][fieldKey] as string[]) || [])];
    currentList[strIndex] = value;
    newItems[itemIndex] = {
        ...newItems[itemIndex],
        [fieldKey]: currentList,
    };
    items.value = newItems;
};
</script>

<template>
    <div class="space-y-3">
        <!-- Items List -->
        <div v-for="(item, index) in items" :key="index" class="border border-gray-200 rounded-lg bg-gray-50 overflow-hidden">
            <!-- Item Header -->
            <div class="flex items-center justify-between px-3 py-2 bg-gray-100 border-b border-gray-200">
                <span class="text-sm font-medium text-gray-700">{{ label }} #{{ index + 1 }}</span>
                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        @click="moveItem(index, 'up')"
                        :disabled="index === 0"
                        :class="[
                            'p-1 rounded text-gray-400',
                            index === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:text-gray-600 hover:bg-gray-200'
                        ]"
                        title="Move up"
                        aria-label="Move item up"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="moveItem(index, 'down')"
                        :disabled="index === items.length - 1"
                        :class="[
                            'p-1 rounded text-gray-400',
                            index === items.length - 1 ? 'opacity-50 cursor-not-allowed' : 'hover:text-gray-600 hover:bg-gray-200'
                        ]"
                        title="Move down"
                        aria-label="Move item down"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="removeItem(index)"
                        class="p-1 rounded text-gray-400 hover:text-red-600 hover:bg-red-50"
                        title="Remove"
                        aria-label="Remove item"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Item Fields -->
            <div class="p-3 space-y-3">
                <div v-for="(fieldSchema, fieldKey) in fields" :key="fieldKey">
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        {{ getFieldLabel(String(fieldKey)) }}
                    </label>

                    <!-- Image field -->
                    <template v-if="fieldSchema.type === 'image'">
                        <ImageUploader
                            :model-value="(item[fieldKey] as string | null)"
                            @update:model-value="updateItem(index, String(fieldKey), $event)"
                        />
                    </template>

                    <!-- Select field -->
                    <template v-else-if="fieldSchema.type === 'select' && fieldSchema.options">
                        <select
                            :value="item[fieldKey]"
                            @change="updateItem(index, String(fieldKey), ($event.target as HTMLSelectElement).value)"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        >
                            <option v-for="option in fieldSchema.options" :key="option" :value="option">
                                {{ option }}
                            </option>
                        </select>
                    </template>

                    <!-- Rich Text field -->
                    <template v-else-if="fieldSchema.type === 'richtext'">
                        <RichTextEditor
                            :model-value="(item[fieldKey] as string) || ''"
                            :placeholder="`Enter ${getFieldLabel(String(fieldKey)).toLowerCase()}...`"
                            @update:model-value="updateItem(index, String(fieldKey), $event)"
                        />
                    </template>

                    <!-- Textarea field -->
                    <template v-else-if="fieldSchema.type === 'textarea'">
                        <textarea
                            :value="(item[fieldKey] as string) || ''"
                            @input="updateItem(index, String(fieldKey), ($event.target as HTMLTextAreaElement).value)"
                            rows="2"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        ></textarea>
                    </template>

                    <!-- Icon field with searchable picker -->
                    <template v-else-if="fieldSchema.type === 'icon'">
                        <IconPicker
                            :model-value="(item[fieldKey] as string) || ''"
                            @update:model-value="updateItem(index, String(fieldKey), $event)"
                        />
                    </template>

                    <!-- String list field (array of strings) -->
                    <template v-else-if="fieldSchema.type === 'stringlist'">
                        <div class="space-y-2">
                            <div
                                v-for="(str, strIndex) in ((item[fieldKey] as string[]) || [])"
                                :key="strIndex"
                                class="flex items-center gap-2"
                            >
                                <input
                                    type="text"
                                    :value="str"
                                    @input="updateStringListItem(index, String(fieldKey), strIndex, ($event.target as HTMLInputElement).value)"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                />
                                <button
                                    type="button"
                                    @click="removeStringListItem(index, String(fieldKey), strIndex)"
                                    class="p-1 text-gray-400 hover:text-red-600"
                                    title="Remove"
                                    aria-label="Remove list item"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <button
                                type="button"
                                @click="addStringListItem(index, String(fieldKey))"
                                class="text-sm text-gray-700 hover:text-gray-800 flex items-center gap-1"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add item
                            </button>
                        </div>
                    </template>

                    <!-- Boolean field -->
                    <template v-else-if="fieldSchema.type === 'boolean'">
                        <input
                            type="checkbox"
                            :checked="(item[fieldKey] as boolean) || false"
                            @change="updateItem(index, String(fieldKey), ($event.target as HTMLInputElement).checked)"
                            class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                        />
                    </template>

                    <!-- Default text field -->
                    <template v-else>
                        <input
                            type="text"
                            :value="(item[fieldKey] as string) || ''"
                            @input="updateItem(index, String(fieldKey), ($event.target as HTMLInputElement).value)"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        />
                    </template>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="items.length === 0" class="text-center py-4 text-sm text-gray-500 border border-dashed border-gray-300 rounded-lg">
            No items yet. Click "Add" to create one.
        </div>

        <!-- Add Button -->
        <button
            type="button"
            @click="addItem"
            class="w-full flex items-center justify-center gap-2 px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add {{ label }}
        </button>
    </div>
</template>
