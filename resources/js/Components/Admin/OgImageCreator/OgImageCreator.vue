<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';
import Modal from '@/Components/Modal.vue';
import OgCanvas from './OgCanvas.vue';
import OgControls from './OgControls.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import type { OgImageState } from '@/types/og-creator';

interface Props {
    show: boolean;
    initialTitle?: string;
}

const props = withDefaults(defineProps<Props>(), {
    initialTitle: '',
});

const emit = defineEmits<{
    close: [];
    created: [imageUrl: string];
}>();

const canvasRef = ref<InstanceType<typeof OgCanvas> | null>(null);
const isExporting = ref(false);
const exportError = ref<string | null>(null);

const getDefaultState = (): OgImageState => ({
    background: {
        type: 'color',
        color: '#1e40af',
        imageUrl: null,
    },
    title: {
        content: props.initialTitle,
        fontSize: 64,
        fontFamily: 'Arial, sans-serif',
        color: '#ffffff',
        x: 60,
        y: 250,
        maxWidth: 1080,
    },
    logo: null,
});

const state = ref<OgImageState>(getDefaultState());

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
    () => props.initialTitle,
    (newTitle) => {
        if (props.show) {
            state.value.title.content = newTitle;
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

    // Ensure canvas is rendered with latest state
    await canvasRef.value?.render();

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
        formData.append('image', blob, 'og-image.png');

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
        isExporting.value = false;
    }
};
</script>

<template>
    <Modal :show="show" max-width="2xl" @close="close">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">
                    OG Image Creator
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Canvas Preview -->
                <div>
                    <p class="text-sm text-gray-500 mb-2">Preview (1200 x 630)</p>
                    <OgCanvas ref="canvasRef" :state="state" />
                </div>

                <!-- Controls -->
                <div class="max-h-[500px] overflow-y-auto pr-2">
                    <OgControls v-model="state" />
                </div>
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
