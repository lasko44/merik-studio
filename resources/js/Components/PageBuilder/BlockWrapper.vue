<script setup lang="ts">
import { ref, computed } from 'vue';
import ImageUploader from '@/Components/Admin/ImageUploader.vue';
import RichTextEditor from '@/Components/Admin/RichTextEditor.vue';
import RepeaterEditor from '@/Components/PageBuilder/RepeaterEditor.vue';
import NestedBlockEditor from '@/Components/PageBuilder/NestedBlockEditor.vue';
import HeroImageCreator from '@/Components/Admin/HeroImageCreator/HeroImageCreator.vue';
import HeroPreview from '@/Components/PageBuilder/HeroPreview.vue';
import BlockColorPicker from '@/Components/Admin/BlockColorPicker.vue';
import type { ContentBlock, PageComponent } from '@/types/admin';

interface PropSchemaItem {
    type: string;
    label: string;
    options?: (string | number)[];
    fields?: Record<string, PropSchemaItem>;
    min?: number;
    max?: number;
    step?: number;
    suffix?: string;
}

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
    block: ContentBlock;
    component?: PageComponent;
    components: Record<string, ComponentCategory>;
    availablePages?: AvailablePage[];
    index: number;
    isFirst: boolean;
    isLast: boolean;
    isDragging?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    availablePages: () => [],
    isDragging: false,
});

const emit = defineEmits<{
    update: [data: Record<string, unknown>];
    remove: [];
    moveUp: [];
    moveDown: [];
    duplicate: [];
    addBelow: [];
}>();

const isExpanded = ref(false);
const showHeroCreator = ref(false);
const heroCreatorFieldKey = ref<string | null>(null);

const openHeroCreator = (fieldKey: string) => {
    heroCreatorFieldKey.value = fieldKey;
    showHeroCreator.value = true;
};

const handleHeroCreated = (imageUrl: string) => {
    if (heroCreatorFieldKey.value) {
        updateData(heroCreatorFieldKey.value, imageUrl);
    }
    showHeroCreator.value = false;
    heroCreatorFieldKey.value = null;
};

const blockName = computed(() => {
    return props.component?.name || props.block.type;
});

const blockIcon = computed(() => {
    return props.component?.icon || 'cube';
});

const isHeroBlock = computed(() => {
    return props.block.type === 'hero';
});

const propSchema = computed<Record<string, PropSchemaItem>>(() => {
    if (props.component?.prop_schema && typeof props.component.prop_schema === 'object') {
        return props.component.prop_schema as Record<string, PropSchemaItem>;
    }
    return {};
});

// Merge prop_schema fields with existing data to ensure all fields are shown
const editableFields = computed(() => {
    const schema = propSchema.value;
    const data = props.block.data;

    // If we have a schema, use schema keys (ensures all fields are shown)
    if (Object.keys(schema).length > 0) {
        const fields: Record<string, unknown> = {};
        for (const key of Object.keys(schema)) {
            fields[key] = data[key] ?? null;
        }
        return fields;
    }

    // Fallback to block data if no schema
    return data;
});

const getFieldType = (key: string): string => {
    const schema = propSchema.value[key];
    if (schema?.type) {
        return schema.type;
    }
    // Fallback to auto-detection
    const value = props.block.data[key];
    if (typeof value === 'boolean') return 'boolean';
    if (typeof value === 'number') return 'number';
    if (typeof value === 'string' && value.length > 100) return 'textarea';
    if (Array.isArray(value)) return 'array';
    if (typeof value === 'object' && value !== null) return 'object';
    return 'text';
};

const getFieldLabel = (key: string): string => {
    const schema = propSchema.value[key];
    if (schema?.label) {
        return schema.label;
    }
    return String(key).replace(/([A-Z])/g, ' $1').replace(/_/g, ' ').trim();
};

const getFieldOptions = (key: string): (string | number)[] => {
    const schema = propSchema.value[key];
    return schema?.options || [];
};

const getFieldMin = (key: string): number => {
    const schema = propSchema.value[key];
    return schema?.min ?? 0;
};

const getFieldMax = (key: string): number => {
    const schema = propSchema.value[key];
    return schema?.max ?? 100;
};

const getFieldStep = (key: string): number => {
    const schema = propSchema.value[key];
    return schema?.step ?? 1;
};

const getFieldSuffix = (key: string): string => {
    const schema = propSchema.value[key];
    return schema?.suffix ?? '';
};

const getRepeaterFields = (key: string): Record<string, PropSchemaItem> => {
    const schema = propSchema.value[key];
    return schema?.fields || {};
};

const iconPaths: Record<string, string> = {
    'hero': 'M4 5a2 2 0 012-2h8a2 2 0 012 2v3H4V5zm0 5v5a2 2 0 002 2h8a2 2 0 002-2v-5H4z',
    'text': 'M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1z',
    'features': 'M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
    'cta': 'M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z',
    'contact': 'M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z',
    'pricing': 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    'columns': 'M9 4.5v15m6-15v15M4.5 4.5h15v15h-15z',
    'cube': 'M21 16.811c0 .864-.933 1.405-1.683.977l-7.108-4.062a1.125 1.125 0 00-1.107 0l-7.108 4.062c-.75.428-1.683-.113-1.683-.977V7.19c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 001.107 0l7.108-4.062c.75-.428 1.683.113 1.683.977V16.81z',
};

const getIconPath = (icon: string): string => {
    return iconPaths[icon] || iconPaths['cube'];
};

const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
};

const updateData = (key: string, value: unknown) => {
    emit('update', { ...props.block.data, [key]: value });
};

const updateCheckboxValue = (key: string, option: string, checked: boolean, currentValue: string[]) => {
    const values = Array.isArray(currentValue) ? [...currentValue] : [];
    if (checked) {
        if (!values.includes(option)) {
            values.push(option);
        }
    } else {
        const index = values.indexOf(option);
        if (index > -1) {
            values.splice(index, 1);
        }
    }
    emit('update', { ...props.block.data, [key]: values });
};
</script>

<template>
    <div
        :class="[
            'border rounded-lg overflow-hidden bg-white transition-all duration-200',
            isDragging ? 'border-gray-300' : 'border-gray-200',
            isExpanded ? 'ring-1 ring-gray-200' : ''
        ]"
    >
        <!-- Header -->
        <div
            :class="[
                'flex items-center justify-between px-4 py-3 border-b transition-colors',
                isExpanded ? 'bg-gray-100 border-gray-200' : 'bg-gray-50 border-gray-200'
            ]"
        >
            <div class="flex items-center gap-3">
                <!-- Drag Handle -->
                <div
                    class="drag-handle flex flex-col gap-0.5 cursor-grab active:cursor-grabbing text-gray-400 hover:text-gray-600 p-1 -m-1 rounded transition-colors"
                    aria-label="Drag to reorder"
                    role="img"
                >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"/>
                    </svg>
                </div>

                <!-- Expand/Collapse Toggle -->
                <button
                    type="button"
                    @click="toggleExpand"
                    class="p-1 rounded text-gray-500 hover:text-gray-700 hover:bg-gray-200 transition-all"
                    :title="isExpanded ? 'Collapse' : 'Expand'"
                    :aria-label="isExpanded ? 'Collapse block content' : 'Expand block content'"
                    :aria-expanded="isExpanded"
                >
                    <svg
                        class="h-4 w-4 transition-transform duration-200"
                        :class="{ 'rotate-90': isExpanded }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Icon -->
                <div class="flex-shrink-0 w-8 h-8 bg-gray-200 rounded flex items-center justify-center" aria-hidden="true">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(blockIcon)" />
                    </svg>
                </div>

                <!-- Name -->
                <span class="font-medium text-gray-900">{{ blockName }}</span>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-0.5">
                <!-- Move Up/Down Group -->
                <div class="flex items-center border border-gray-200 rounded-md bg-white mr-1">
                    <button
                        type="button"
                        @click="$emit('moveUp')"
                        :disabled="isFirst"
                        :class="[
                            'p-1.5 text-gray-500 border-r border-gray-200',
                            isFirst ? 'opacity-40 cursor-not-allowed' : 'hover:text-gray-700 hover:bg-gray-50',
                        ]"
                        title="Move up"
                        aria-label="Move block up"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="$emit('moveDown')"
                        :disabled="isLast"
                        :class="[
                            'p-1.5 text-gray-500',
                            isLast ? 'opacity-40 cursor-not-allowed' : 'hover:text-gray-700 hover:bg-gray-50',
                        ]"
                        title="Move down"
                        aria-label="Move block down"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <!-- Other Actions -->
                <button
                    type="button"
                    @click="$emit('duplicate')"
                    class="p-1.5 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
                    title="Duplicate"
                    aria-label="Duplicate block"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="$emit('addBelow')"
                    class="p-1.5 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
                    title="Add block below"
                    aria-label="Add block below"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="$emit('remove')"
                    class="p-1.5 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                    title="Remove"
                    aria-label="Remove block"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div
            v-show="isExpanded"
            class="p-4 animate-in slide-in-from-top-2 duration-200"
        >
            <!-- Hero Preview -->
            <div v-if="isHeroBlock" class="mb-4">
                <HeroPreview
                    :heading="(block.data.heading as string)"
                    :subheading="(block.data.subheading as string)"
                    :background-image="(block.data.backgroundImage as string | null)"
                    :overlay-opacity="(block.data.overlayOpacity as number) ?? 50"
                    :alignment="(block.data.alignment as 'left' | 'center' | 'right')"
                />
            </div>

            <!-- Dynamic block editor based on type -->
            <div class="space-y-4">
                <div v-for="(value, key) in editableFields" :key="key" class="space-y-1">
                    <label :for="`block-${block.id}-${key}`" class="block text-sm font-medium text-gray-700">
                        {{ getFieldLabel(String(key)) }}
                    </label>

                    <!-- Boolean / Checkbox -->
                    <template v-if="getFieldType(String(key)) === 'boolean'">
                        <input
                            type="checkbox"
                            :id="`block-${block.id}-${key}`"
                            :checked="value as boolean"
                            @change="updateData(String(key), ($event.target as HTMLInputElement).checked)"
                            class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                        />
                    </template>

                    <!-- Select -->
                    <template v-else-if="getFieldType(String(key)) === 'select'">
                        <select
                            :id="`block-${block.id}-${key}`"
                            :value="value"
                            @change="updateData(String(key), ($event.target as HTMLSelectElement).value)"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        >
                            <option v-for="option in getFieldOptions(String(key))" :key="option" :value="option">
                                {{ option }}
                            </option>
                        </select>
                    </template>

                    <!-- Checkboxes (multi-select) -->
                    <template v-else-if="getFieldType(String(key)) === 'checkboxes'">
                        <div class="space-y-2">
                            <label
                                v-for="option in getFieldOptions(String(key))"
                                :key="option"
                                class="flex items-center gap-2"
                            >
                                <input
                                    type="checkbox"
                                    :checked="Array.isArray(value) && (value as string[]).includes(String(option))"
                                    @change="updateCheckboxValue(String(key), String(option), ($event.target as HTMLInputElement).checked, value as string[])"
                                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                                />
                                <span class="text-sm text-gray-700 capitalize">{{ option }}</span>
                            </label>
                        </div>
                    </template>

                    <!-- Image -->
                    <template v-else-if="getFieldType(String(key)) === 'image'">
                        <ImageUploader
                            :model-value="(value as string | null)"
                            :label="getFieldLabel(String(key))"
                            @update:model-value="updateData(String(key), $event)"
                        />
                    </template>

                    <!-- Hero Image (with creator) -->
                    <template v-else-if="getFieldType(String(key)) === 'hero_image'">
                        <div class="space-y-2">
                            <div class="flex items-center justify-end">
                                <button
                                    type="button"
                                    @click="openHeroCreator(String(key))"
                                    class="text-sm text-gray-700 hover:text-gray-800"
                                >
                                    Create with Editor
                                </button>
                            </div>
                            <ImageUploader
                                :model-value="(value as string | null)"
                                :label="getFieldLabel(String(key))"
                                @update:model-value="updateData(String(key), $event)"
                            />
                        </div>
                    </template>

                    <!-- Rich Text Editor -->
                    <template v-else-if="getFieldType(String(key)) === 'richtext'">
                        <RichTextEditor
                            :model-value="(value as string) || ''"
                            :placeholder="`Enter ${getFieldLabel(String(key)).toLowerCase()}...`"
                            @update:model-value="updateData(String(key), $event)"
                        />
                    </template>

                    <!-- Textarea -->
                    <template v-else-if="getFieldType(String(key)) === 'textarea'">
                        <textarea
                            :id="`block-${block.id}-${key}`"
                            :value="value as string"
                            @input="updateData(String(key), ($event.target as HTMLTextAreaElement).value)"
                            rows="3"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        ></textarea>
                    </template>

                    <!-- Number -->
                    <template v-else-if="getFieldType(String(key)) === 'number'">
                        <input
                            type="number"
                            :id="`block-${block.id}-${key}`"
                            :value="value as number"
                            @input="updateData(String(key), parseFloat(($event.target as HTMLInputElement).value))"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        />
                    </template>

                    <!-- Range Slider -->
                    <template v-else-if="getFieldType(String(key)) === 'range'">
                        <div class="flex items-center gap-3">
                            <input
                                type="range"
                                :id="`block-${block.id}-${key}`"
                                :value="value as number"
                                :min="getFieldMin(String(key))"
                                :max="getFieldMax(String(key))"
                                :step="getFieldStep(String(key))"
                                @input="updateData(String(key), parseFloat(($event.target as HTMLInputElement).value))"
                                class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                            />
                            <span class="text-sm font-medium text-gray-700 min-w-[3rem] text-right">
                                {{ value }}{{ getFieldSuffix(String(key)) }}
                            </span>
                        </div>
                    </template>

                    <!-- Color Picker -->
                    <template v-else-if="getFieldType(String(key)) === 'color'">
                        <BlockColorPicker
                            :model-value="(value as string)"
                            @update:model-value="updateData(String(key), $event)"
                        />
                    </template>

                    <!-- Page Link -->
                    <template v-else-if="getFieldType(String(key)) === 'page_link'">
                        <div class="space-y-2">
                            <select
                                :id="`block-${block.id}-${key}`"
                                :value="(value as string)?.startsWith('/') || (value as string) === '#' || !(value as string)?.includes('://') ? (value as string) : '__external__'"
                                @change="($event.target as HTMLSelectElement).value === '__external__'
                                    ? updateData(String(key), 'https://')
                                    : updateData(String(key), ($event.target as HTMLSelectElement).value)"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                            >
                                <option value="">Select a page...</option>
                                <option value="#"># (Same page anchor)</option>
                                <option
                                    v-for="page in availablePages"
                                    :key="page.id"
                                    :value="page.path"
                                >
                                    {{ page.title }} ({{ page.path }})
                                </option>
                                <option value="__external__">External URL...</option>
                            </select>
                            <div v-if="(value as string)?.includes('://')" class="flex items-center gap-2">
                                <input
                                    type="url"
                                    :value="value as string"
                                    @input="updateData(String(key), ($event.target as HTMLInputElement).value)"
                                    placeholder="https://example.com"
                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                />
                                <span class="text-xs text-gray-500 whitespace-nowrap">Opens in new tab</span>
                            </div>
                        </div>
                    </template>

                    <!-- Array (repeater) -->
                    <template v-else-if="getFieldType(String(key)) === 'array' || getFieldType(String(key)) === 'repeater'">
                        <RepeaterEditor
                            :model-value="(value as Record<string, unknown>[] || [])"
                            :fields="getRepeaterFields(String(key))"
                            :label="getFieldLabel(String(key)).replace(/s$/, '')"
                            @update:model-value="updateData(String(key), $event)"
                        />
                    </template>

                    <!-- Nested blocks (for layout components) -->
                    <template v-else-if="getFieldType(String(key)) === 'blocks'">
                        <NestedBlockEditor
                            :model-value="(value as ContentBlock[] || [])"
                            :components="components"
                            :label="getFieldLabel(String(key))"
                            @update:model-value="updateData(String(key), $event)"
                        />
                    </template>

                    <!-- Object -->
                    <template v-else-if="getFieldType(String(key)) === 'object'">
                        <div class="text-sm text-gray-500 bg-gray-50 rounded-md p-3 border border-gray-200">
                            <span>Complex object</span>
                        </div>
                    </template>

                    <!-- Default Text -->
                    <template v-else>
                        <input
                            type="text"
                            :id="`block-${block.id}-${key}`"
                            :value="value || ''"
                            @input="updateData(String(key), ($event.target as HTMLInputElement).value)"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        />
                    </template>
                </div>

                <div v-if="Object.keys(editableFields).length === 0" class="text-sm text-gray-500 italic">
                    No editable properties
                </div>
            </div>
        </div>

        <!-- Hero Image Creator Modal -->
        <HeroImageCreator
            :show="showHeroCreator"
            :initial-heading="(block.data.heading as string) || ''"
            :initial-subheading="(block.data.subheading as string) || ''"
            @close="showHeroCreator = false"
            @created="handleHeroCreated"
        />
    </div>
</template>

<style scoped>
.animate-in {
    animation: animateIn 0.2s ease-out;
}

@keyframes animateIn {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
