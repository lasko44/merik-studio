<script setup lang="ts">
import { computed, ref } from 'vue';
import SectionWrapper from './SectionWrapper.vue';
import draggable from 'vuedraggable';
import type { Section, PageComponent, SectionDefinition, SectionBranding, ContentBlock } from '@/types/admin';

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
    modelValue: Section[];
    components: Record<string, ComponentCategory>;
    sectionDefinitions: Record<string, SectionDefinition>;
    availablePages?: AvailablePage[];
}

const props = withDefaults(defineProps<Props>(), {
    availablePages: () => [],
});

const emit = defineEmits<{
    'update:modelValue': [value: Section[]];
}>();

const isDragging = ref(false);
const showAddSectionMenu = ref(false);

const sections = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const generateSectionId = (): string => {
    return 'section_' + Math.random().toString(36).substring(2, 11);
};

const getDefaultBranding = (): SectionBranding => ({
    backgroundColor: null,
    backgroundImage: null,
    backgroundOverlay: 0,
    textColor: null,
    paddingTop: 'md',
    paddingBottom: 'md',
    borderTop: false,
    borderBottom: false,
    shadow: 'none',
    rounded: 'none',
    maxWidth: 'max-w-7xl',
});

const availableSectionsToAdd = computed(() => {
    const existingSlugs = new Set(sections.value.map(s => s.slug));
    const available: Array<{ slug: string; definition: SectionDefinition }> = [];

    for (const [slug, definition] of Object.entries(props.sectionDefinitions)) {
        if (!existingSlugs.has(slug)) {
            available.push({ slug, definition });
        }
    }

    return available;
});

// Generate unique block ID
const generateBlockId = (): string => {
    return 'block_' + Math.random().toString(36).substring(2, 11);
};

const addSection = (slug: string, definition: SectionDefinition) => {
    const defaultBranding = getDefaultBranding();
    const customBranding = definition.defaultBranding || {};

    // Initialize blocks from defaultBlocks if present
    const blocks: ContentBlock[] = [];
    const defaultBlocks = definition.defaultBlocks || [];
    for (const block of defaultBlocks) {
        blocks.push({
            id: generateBlockId(),
            type: block.type,
            data: { ...block.data },
        });
    }

    const newSection: Section = {
        id: generateSectionId(),
        slug,
        label: definition.label,
        blocks,
        branding: { ...defaultBranding, ...customBranding } as SectionBranding,
        isRequired: definition.required,
    };

    // Find the correct position based on section_definitions order
    const definitionOrder = Object.keys(props.sectionDefinitions);
    const newIndex = definitionOrder.indexOf(slug);

    let insertIndex = sections.value.length;
    for (let i = 0; i < sections.value.length; i++) {
        const existingIndex = definitionOrder.indexOf(sections.value[i].slug);
        if (existingIndex > newIndex) {
            insertIndex = i;
            break;
        }
    }

    const newSections = [...sections.value];
    newSections.splice(insertIndex, 0, newSection);
    sections.value = newSections;

    showAddSectionMenu.value = false;
};

const updateSection = (index: number, section: Section) => {
    const newSections = [...sections.value];
    newSections[index] = section;
    sections.value = newSections;
};

const deleteSection = (index: number) => {
    const newSections = [...sections.value];
    newSections.splice(index, 1);
    sections.value = newSections;
};

const moveSection = (index: number, direction: 'up' | 'down') => {
    const newSections = [...sections.value];
    const targetIndex = direction === 'up' ? index - 1 : index + 1;

    if (targetIndex < 0 || targetIndex >= newSections.length) {
        return;
    }

    [newSections[index], newSections[targetIndex]] = [newSections[targetIndex], newSections[index]];
    sections.value = newSections;
};

const onDragStart = () => {
    isDragging.value = true;
};

const onDragEnd = () => {
    isDragging.value = false;
};

const dragOptions = computed(() => ({
    animation: 200,
    group: 'sections',
    ghostClass: 'section-ghost',
    chosenClass: 'section-chosen',
    dragClass: 'section-drag',
    handle: '.section-drag-handle',
}));
</script>

<template>
    <div class="space-y-4">
        <!-- Empty state when no sections -->
        <div
            v-if="sections.length === 0"
            class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center"
        >
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
            </svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900">No sections</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding sections to your page.</p>

            <div v-if="availableSectionsToAdd.length > 0" class="mt-4 flex flex-wrap justify-center gap-2">
                <button
                    v-for="{ slug, definition } in availableSectionsToAdd"
                    :key="slug"
                    type="button"
                    @click="addSection(slug, definition)"
                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                >
                    <svg class="h-4 w-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ definition.label }}
                    <span v-if="definition.required" class="ml-1.5 text-xs text-indigo-600">(Required)</span>
                </button>
            </div>
        </div>

        <!-- Sections list -->
        <draggable
            v-if="sections.length > 0"
            v-model="sections"
            v-bind="dragOptions"
            item-key="id"
            class="space-y-4"
            @start="onDragStart"
            @end="onDragEnd"
        >
            <template #item="{ element: section, index }">
                <div class="section-drag-handle cursor-move">
                    <SectionWrapper
                        :section="section"
                        :components="components"
                        :available-pages="availablePages"
                        :is-first="index === 0"
                        :is-last="index === sections.length - 1"
                        @update:section="updateSection(index, $event)"
                        @delete="deleteSection(index)"
                        @move-up="moveSection(index, 'up')"
                        @move-down="moveSection(index, 'down')"
                    />
                </div>
            </template>
        </draggable>

        <!-- Add section button -->
        <div v-if="sections.length > 0 && availableSectionsToAdd.length > 0" class="relative">
            <div class="flex justify-center">
                <button
                    type="button"
                    @click="showAddSectionMenu = !showAddSectionMenu"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                >
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Add Section
                </button>
            </div>

            <!-- Add section menu -->
            <Transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <div
                    v-if="showAddSectionMenu"
                    class="absolute left-1/2 -translate-x-1/2 mt-2 w-72 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 z-10"
                >
                    <div class="py-1">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Available Sections
                        </div>
                        <button
                            v-for="{ slug, definition } in availableSectionsToAdd"
                            :key="slug"
                            type="button"
                            @click="addSection(slug, definition)"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-between"
                        >
                            <span>{{ definition.label }}</span>
                            <span v-if="definition.required" class="text-xs text-indigo-600">Required</span>
                        </button>
                    </div>
                </div>
            </Transition>
        </div>

        <!-- Click outside to close menu -->
        <div
            v-if="showAddSectionMenu"
            class="fixed inset-0 z-0"
            @click="showAddSectionMenu = false"
        />
    </div>
</template>

<style scoped>
.section-ghost {
    @apply opacity-40 bg-indigo-100 border-2 border-dashed border-indigo-400 rounded-lg;
}

.section-chosen {
    @apply ring-2 ring-indigo-500 ring-offset-2;
}

.section-drag {
    @apply opacity-90 shadow-xl;
}
</style>
