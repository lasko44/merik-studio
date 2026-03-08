<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import Modal from '@/Components/Modal.vue';
import HeroCanvas from './HeroCanvas.vue';
import HeroControls from './HeroControls.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import type { HeroImageState, ThemeColors } from '@/types/hero-creator';

interface Props {
    show: boolean;
    initialHeading?: string;
    initialSubheading?: string;
}

const props = withDefaults(defineProps<Props>(), {
    initialHeading: '',
    initialSubheading: '',
});

const emit = defineEmits<{
    close: [];
    created: [imageUrl: string];
}>();

const page = usePage();

const themeColors = computed<ThemeColors>(() => {
    const settings = (page.props as Record<string, unknown>).siteSettings as ThemeColors | undefined;
    return {
        primaryColor: settings?.primaryColor || '#3B82F6',
        secondaryColor: settings?.secondaryColor || '#10B981',
        accentColor: settings?.accentColor || '#F59E0B',
        backgroundColor: settings?.backgroundColor || '#FFFFFF',
        textColor: settings?.textColor || '#1F2937',
    };
});

const canvasRef = ref<InstanceType<typeof HeroCanvas> | null>(null);
const isExporting = ref(false);
const exportError = ref<string | null>(null);
const includeText = ref(false);

const getDefaultState = (): HeroImageState => ({
    background: {
        type: 'gradient',
        color: themeColors.value.primaryColor,
        gradient: {
            type: 'linear',
            angle: 135,
            colorStops: [
                { color: themeColors.value.primaryColor, position: 0 },
                { color: themeColors.value.secondaryColor, position: 100 },
            ],
        },
        imageUrl: null,
        overlay: {
            enabled: false,
            color: '#000000',
            opacity: 50,
        },
    },
    text: {
        heading: props.initialHeading,
        subheading: props.initialSubheading,
        color: '#FFFFFF',
        alignment: 'center',
    },
    dimensions: {
        width: 1920,
        height: 800,
    },
});

const state = ref<HeroImageState>(getDefaultState());

watch(
    () => props.show,
    (newValue) => {
        if (newValue) {
            state.value = getDefaultState();
            exportError.value = null;
        }
    }
);

watch(
    () => props.initialHeading,
    (newHeading) => {
        if (props.show) {
            state.value.text.heading = newHeading;
        }
    }
);

watch(
    () => props.initialSubheading,
    (newSubheading) => {
        if (props.show) {
            state.value.text.subheading = newSubheading;
        }
    }
);

const close = () => {
    emit('close');
};

const exportImage = async () => {
    const canvas = canvasRef.value?.getCanvas();
    if (!canvas) {
        exportError.value = 'Canvas not available';
        return;
    }

    // If not including text, temporarily clear it and re-render
    const originalText = { ...state.value.text };
    if (!includeText.value) {
        state.value.text.heading = '';
        state.value.text.subheading = '';
        await canvasRef.value?.render();
    }

    isExporting.value = true;
    exportError.value = null;

    try {
        const blob = await new Promise<Blob | null>((resolve) => {
            canvas.toBlob((b) => resolve(b), 'image/png', 1);
        });

        if (!blob) {
            throw new Error('Failed to create image');
        }

        const formData = new FormData();
        formData.append('image', blob, 'hero-background.png');

        const response = await axios.post(route('admin.media.upload'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        emit('created', response.data.url);
    } catch (error: unknown) {
        if (axios.isAxiosError(error)) {
            exportError.value = error.response?.data?.message || 'Failed to upload image';
        } else {
            exportError.value = 'Failed to create image';
        }
    } finally {
        // Restore text if we cleared it
        if (!includeText.value) {
            state.value.text = originalText;
            await canvasRef.value?.render();
        }
        isExporting.value = false;
    }
};

const dimensionPresets = [
    { label: 'Full Width (1920x800)', width: 1920, height: 800 },
    { label: 'Wide (1600x600)', width: 1600, height: 600 },
    { label: 'Standard (1200x500)', width: 1200, height: 500 },
    { label: 'Compact (1200x400)', width: 1200, height: 400 },
];

const setDimensions = (width: number, height: number) => {
    state.value.dimensions = { width, height };
};
</script>

<template>
    <Modal :show="show" max-width="2xl" @close="close">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">
                    Hero Background Creator
                </h3>
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

            <!-- Dimension Presets -->
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-700 mb-2 block">Image Size</label>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="preset in dimensionPresets"
                        :key="preset.label"
                        type="button"
                        @click="setDimensions(preset.width, preset.height)"
                        :class="[
                            'px-3 py-1.5 text-xs font-medium rounded-md transition-colors',
                            state.dimensions.width === preset.width && state.dimensions.height === preset.height
                                ? 'bg-gray-800 text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        {{ preset.label }}
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Canvas Preview -->
                <div>
                    <p class="text-sm text-gray-500 mb-2">
                        Preview ({{ state.dimensions.width }} x {{ state.dimensions.height }})
                    </p>
                    <HeroCanvas ref="canvasRef" :state="state" />
                </div>

                <!-- Controls -->
                <div class="max-h-[500px] overflow-y-auto pr-2">
                    <HeroControls v-model="state" :theme-colors="themeColors" />
                </div>
            </div>

            <!-- Export Options -->
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        v-model="includeText"
                        class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                    />
                    <span class="ml-2 text-sm text-gray-600">Include text in exported image</span>
                </label>
                <p class="text-xs text-gray-500 mt-1">
                    Usually you'll want this off, as the hero section has its own text fields.
                </p>
            </div>

            <!-- Error Message -->
            <div v-if="exportError" class="mt-4 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="ml-3 text-sm text-red-700">{{ exportError }}</p>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-6 flex justify-end gap-3">
                <SecondaryButton @click="close">
                    Cancel
                </SecondaryButton>
                <PrimaryButton @click="exportImage" :disabled="isExporting">
                    <svg v-if="isExporting" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ isExporting ? 'Creating...' : 'Create & Use Image' }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
