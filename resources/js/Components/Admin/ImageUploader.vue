<script setup lang="ts">
import { ref, computed } from 'vue';
import axios from 'axios';

interface Props {
    modelValue: string | null;
    label?: string;
    accept?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: null,
    label: 'Image',
    accept: 'image/*',
});

const emit = defineEmits<{
    'update:modelValue': [value: string | null];
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const isUploading = ref(false);
const uploadError = ref<string | null>(null);

const imageUrl = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const hasImage = computed(() => !!imageUrl.value);

const openFilePicker = () => {
    fileInput.value?.click();
};

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        uploadFile(file);
    }
};

const handleDrop = (event: DragEvent) => {
    event.preventDefault();
    isDragging.value = false;

    const file = event.dataTransfer?.files?.[0];
    if (file && file.type.startsWith('image/')) {
        uploadFile(file);
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

const uploadFile = async (file: File) => {
    if (!file.type.startsWith('image/')) {
        uploadError.value = 'Please select an image file';
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        uploadError.value = 'Image must be less than 5MB';
        return;
    }

    uploadError.value = null;
    isUploading.value = true;

    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await axios.post(route('admin.media.upload'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
        });

        imageUrl.value = response.data.url;
    } catch (error: unknown) {
        if (axios.isAxiosError(error)) {
            uploadError.value = error.response?.data?.message || 'Upload failed';
        } else {
            uploadError.value = 'Upload failed';
        }
    } finally {
        isUploading.value = false;
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    }
};

const removeImage = () => {
    imageUrl.value = null;
};
</script>

<template>
    <div class="space-y-2">
        <!-- Hidden file input -->
        <input
            ref="fileInput"
            type="file"
            :accept="accept"
            style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;"
            @change="handleFileSelect"
        />

        <!-- Image preview or upload area -->
        <div v-if="hasImage" class="relative group">
            <img
                :src="imageUrl!"
                :alt="label"
                class="w-full h-48 object-cover rounded-lg border border-gray-200"
            />
            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2">
                <button
                    type="button"
                    @click="openFilePicker"
                    class="p-2 bg-white rounded-full text-gray-700 hover:bg-gray-100 transition-colors"
                    title="Change image"
                    aria-label="Change image"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="removeImage"
                    class="p-2 bg-red-500 rounded-full text-white hover:bg-red-600 transition-colors"
                    title="Remove image"
                    aria-label="Remove image"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Upload area -->
        <div
            v-else
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
            <div v-if="isUploading" class="flex flex-col items-center" role="status" aria-live="polite">
                <svg class="animate-spin h-8 w-8 text-gray-700 mb-2" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-gray-600">Uploading...</span>
            </div>

            <!-- Upload prompt -->
            <div v-else class="flex flex-col items-center">
                <svg class="h-10 w-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm font-medium text-gray-700">Click to upload</span>
                <span class="text-xs text-gray-500 mt-1">or drag and drop</span>
                <span class="text-xs text-gray-400 mt-2">PNG, JPG, GIF up to 5MB</span>
            </div>
        </div>

        <!-- Error message -->
        <p v-if="uploadError" class="text-sm text-red-600" role="alert">{{ uploadError }}</p>
    </div>
</template>
