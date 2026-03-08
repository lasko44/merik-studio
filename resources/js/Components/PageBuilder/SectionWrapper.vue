<script setup lang="ts">
import { ref, computed } from 'vue';
import BlockEditor from './BlockEditor.vue';
import SectionBrandingPanel from './SectionBrandingPanel.vue';
import type { Section, ContentBlock, PageComponent, SectionBranding } from '@/types/admin';

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
    section: Section;
    components: Record<string, ComponentCategory>;
    availablePages?: AvailablePage[];
    isFirst?: boolean;
    isLast?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    availablePages: () => [],
    isFirst: false,
    isLast: false,
});

const emit = defineEmits<{
    'update:section': [section: Section];
    delete: [];
    'move-up': [];
    'move-down': [];
}>();

const isCollapsed = ref(false);
const showBrandingPanel = ref(false);
const showDeleteConfirm = ref(false);

const sectionBlocks = computed({
    get: () => props.section.blocks,
    set: (value: ContentBlock[]) => {
        emit('update:section', { ...props.section, blocks: value });
    },
});

const updateBranding = (branding: SectionBranding) => {
    emit('update:section', { ...props.section, branding });
};

const handleDelete = () => {
    if (props.section.isRequired) {
        showDeleteConfirm.value = true;
    } else {
        emit('delete');
    }
};

const confirmDelete = () => {
    showDeleteConfirm.value = false;
    emit('delete');
};

const cancelDelete = () => {
    showDeleteConfirm.value = false;
};

const hasBrandingCustomization = computed(() => {
    const b = props.section.branding;
    return b.backgroundColor || b.backgroundImage || b.textColor ||
           b.borderTop || b.borderBottom ||
           b.shadow !== 'none' || b.rounded !== 'none';
});

const brandingPreviewStyle = computed(() => {
    const b = props.section.branding;
    const styles: Record<string, string> = {};
    if (b.backgroundColor) {
        styles.backgroundColor = b.backgroundColor;
    }
    return styles;
});
</script>

<template>
    <div class="border border-gray-200 rounded-lg bg-white overflow-hidden">
        <!-- Section Header -->
        <div
            class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200"
            :class="{ 'cursor-pointer hover:bg-gray-100': true }"
        >
            <div class="flex items-center gap-3" @click="isCollapsed = !isCollapsed">
                <!-- Collapse toggle -->
                <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg
                        class="h-5 w-5 transition-transform"
                        :class="{ '-rotate-90': isCollapsed }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Section label -->
                <div class="flex items-center gap-2">
                    <span class="font-medium text-gray-900">{{ section.label }}</span>
                    <span
                        v-if="section.isRequired"
                        class="inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700"
                    >
                        Required
                    </span>
                    <span
                        v-if="section.blocks.length > 0"
                        class="text-xs text-gray-500"
                    >
                        {{ section.blocks.length }} block{{ section.blocks.length !== 1 ? 's' : '' }}
                    </span>
                </div>

                <!-- Branding indicator -->
                <div
                    v-if="hasBrandingCustomization"
                    class="flex items-center gap-1"
                >
                    <div
                        class="w-4 h-4 rounded border border-gray-300"
                        :style="brandingPreviewStyle"
                        title="Custom branding applied"
                    />
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-1">
                <!-- Move up -->
                <button
                    v-if="!isFirst"
                    type="button"
                    @click.stop="emit('move-up')"
                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded"
                    title="Move up"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                </button>

                <!-- Move down -->
                <button
                    v-if="!isLast"
                    type="button"
                    @click.stop="emit('move-down')"
                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded"
                    title="Move down"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Branding -->
                <button
                    type="button"
                    @click.stop="showBrandingPanel = true"
                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded"
                    :class="{ 'text-indigo-600': hasBrandingCustomization }"
                    title="Section branding"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                </button>

                <!-- Delete -->
                <button
                    type="button"
                    @click.stop="handleDelete"
                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded"
                    title="Delete section"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Section Content -->
        <div v-show="!isCollapsed" class="p-4">
            <BlockEditor
                v-model="sectionBlocks"
                :components="components"
                :available-pages="availablePages"
                :group="`section-${section.slug}`"
            />
        </div>

        <!-- Branding Panel -->
        <SectionBrandingPanel
            :show="showBrandingPanel"
            :model-value="section.branding"
            :section-label="section.label"
            @update:model-value="updateBranding"
            @close="showBrandingPanel = false"
        />

        <!-- Delete Confirmation Dialog -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showDeleteConfirm" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="cancelDelete" />

                        <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900">
                                        Delete Required Section
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            The "{{ section.label }}" section is marked as required for this template.
                                            Are you sure you want to delete it? This may affect how the page renders.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                                <button
                                    type="button"
                                    @click="confirmDelete"
                                    class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto"
                                >
                                    Delete Anyway
                                </button>
                                <button
                                    type="button"
                                    @click="cancelDelete"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
