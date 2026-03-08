<script setup lang="ts">
import { ref, computed } from 'vue';
import axios from 'axios';

interface Props {
    modelValue: string[];
    label?: string;
    accept?: string;
    maxImages?: number;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: () => [],
    label: 'Images',
    accept: 'image/*',
    maxImages: 10,
});

const emit = defineEmits<{
    'update:modelValue': [value: string[]];
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const isUploading = ref(false);
const uploadError = ref<string | null>(null);

const images = computed({
    get: () => props.modelValue || [],
    set: (value) => emit('update:modelValue', value),
});

const canAddMore = computed(() => images.value.length < props.maxImages);

const openFilePicker = () => {
    if (canAddMore.value) {
        fileInput.value?.click();
    }
};

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = target.files;
    if (files) {
        uploadFiles(Array.from(files));
    }
};

const handleDrop = (event: DragEvent) => {
    event.preventDefault();
    isDragging.value = false;

    const files = event.dataTransfer?.files;
    if (files) {
        const imageFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
        if (imageFiles.length > 0) {
            uploadFiles(imageFiles);
        }
    }
};

const handleDragOver = (event: DragEvent) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const getCsrfToken = (): string => {
    const tokenMeta = document.head.querySelector('meta[name="csrf-token"]');
    return tokenMeta ? (tokenMeta as HTMLMetaElement).content : '';
};

const uploadFiles = async (files: File[]) => {
    const remainingSlots = props.maxImages - images.value.length;
    const filesToUpload = files.slice(0, remainingSlots);

    if (filesToUpload.length === 0) {
        uploadError.value = `Maximum ${props.maxImages} images allowed`;
        return;
    }

    uploadError.value = null;
    isUploading.value = true;

    const uploadedUrls: string[] = [];

    for (const file of filesToUpload) {
        if (!file.type.startsWith('image/')) {
            continue;
        }

        if (file.size > 5 * 1024 * 1024) {
            uploadError.value = 'Each image must be less than 5MB';
            continue;
        }

        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await axios.post(route('admin.media.upload'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });

            uploadedUrls.push(response.data.url);
        } catch (error: unknown) {
            if (axios.isAxiosError(error)) {
                uploadError.value = error.response?.data?.message || 'Upload failed';
            } else {
                uploadError.value = 'Upload failed';
            }
        }
    }

    if (uploadedUrls.length > 0) {
        images.value = [...images.value, ...uploadedUrls];
    }

    isUploading.value = false;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const removeImage = (index: number) => {
    const newImages = [...images.value];
    newImages.splice(index, 1);
    images.value = newImages;
};

const moveImage = (index: number, direction: 'up' | 'down') => {
    const newImages = [...images.value];
    const newIndex = direction === 'up' ? index - 1 : index + 1;

    if (newIndex < 0 || newIndex >= newImages.length) return;

    [newImages[index], newImages[newIndex]] = [newImages[newIndex], newImages[index]];
    images.value = newImages;
};
</script>

<template>
    <div class="space-y-4">
        <!-- Hidden file input -->
        <input
            ref="fileInput"
            type="file"
            :accept="accept"
            multiple
            style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;"
            @change="handleFileSelect"
        />

        <!-- Image grid -->
        <div v-if="images.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <div
                v-for="(imageUrl, index) in images"
                :key="index"
                class="relative group aspect-square"
            >
                <img
                    :src="imageUrl"
                    :alt="`${label} ${index + 1}`"
                    class="w-full h-full object-cover rounded-lg border border-gray-200"
                />
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-1">
                    <!-- Move up -->
                    <button
                        v-if="index > 0"
                        type="button"
                        @click="moveImage(index, 'up')"
                        class="p-1.5 bg-white rounded-full text-gray-700 hover:bg-gray-100 transition-colors"
                        title="Move left"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <!-- Move down -->
                    <button
                        v-if="index < images.length - 1"
                        type="button"
                        @click="moveImage(index, 'down')"
                        class="p-1.5 bg-white rounded-full text-gray-700 hover:bg-gray-100 transition-colors"
                        title="Move right"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <!-- Remove -->
                    <button
                        type="button"
                        @click="removeImage(index)"
                        class="p-1.5 bg-red-500 rounded-full text-white hover:bg-red-600 transition-colors"
                        title="Remove image"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                <!-- Image number badge -->
                <div class="absolute top-2 left-2 bg-black/60 text-white text-xs px-2 py-0.5 rounded">
                    {{ index + 1 }}
                </div>
            </div>
        </div>

        <!-- Upload area -->
        <div
            v-if="canAddMore"
            @click="openFilePicker"
            @drop="handleDrop"
            @dragover="handleDragOver"
            @dragleave="handleDragLeave"
            :class="[
                'relative border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors',
                isDragging
                    ? 'border-gray-700 bg-gray-50'
                    : 'border-gray-300 hover:border-gray-400 hover:bg-gray-50',
            ]"
        >
            <!-- Upload spinner -->
            <div v-if="isUploading" class="flex flex-col items-center">
                <svg class="animate-spin h-8 w-8 text-gray-700 mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-gray-600">Uploading...</span>
            </div>

            <!-- Upload prompt -->
            <div v-else class="flex flex-col items-center">
                <svg class="h-10 w-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm font-medium text-gray-700">Click to upload images</span>
                <span class="text-xs text-gray-500 mt-1">or drag and drop multiple files</span>
                <span class="text-xs text-gray-400 mt-2">PNG, JPG, GIF up to 5MB each ({{ images.length }}/{{ maxImages }})</span>
            </div>
        </div>

        <!-- Max reached message -->
        <p v-else class="text-sm text-gray-500 text-center">
            Maximum {{ maxImages }} images reached
        </p>

        <!-- Error message -->
        <p v-if="uploadError" class="text-sm text-red-600">{{ uploadError }}</p>
    </div>
</template>
