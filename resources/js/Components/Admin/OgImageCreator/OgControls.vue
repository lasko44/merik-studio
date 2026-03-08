<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import InputLabel from '@/Components/InputLabel.vue';
import type { OgImageState } from '@/types/og-creator';

interface Props {
    modelValue: OgImageState;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:modelValue': [value: OgImageState];
}>();

const backgroundFileInput = ref<HTMLInputElement | null>(null);
const logoFileInput = ref<HTMLInputElement | null>(null);
const isUploadingBackground = ref(false);
const isUploadingLogo = ref(false);

const fontFamilies = [
    { value: 'Arial, sans-serif', label: 'Arial' },
    { value: 'Georgia, serif', label: 'Georgia' },
    { value: 'Helvetica, sans-serif', label: 'Helvetica' },
    { value: 'Times New Roman, serif', label: 'Times New Roman' },
    { value: 'Verdana, sans-serif', label: 'Verdana' },
    { value: 'system-ui, sans-serif', label: 'System UI' },
];

const updateState = (updates: Partial<OgImageState>) => {
    emit('update:modelValue', { ...props.modelValue, ...updates });
};

const updateBackground = (updates: Partial<OgImageState['background']>) => {
    updateState({
        background: { ...props.modelValue.background, ...updates },
    });
};

const updateTitle = (updates: Partial<OgImageState['title']>) => {
    updateState({
        title: { ...props.modelValue.title, ...updates },
    });
};

const updateLogo = (updates: Partial<NonNullable<OgImageState['logo']>>) => {
    if (props.modelValue.logo) {
        updateState({
            logo: { ...props.modelValue.logo, ...updates },
        });
    }
};

const uploadImage = async (file: File): Promise<string | null> => {
    if (!file.type.startsWith('image/')) {
        return null;
    }

    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await axios.post(route('admin.media.upload'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        return response.data.url;
    } catch {
        return null;
    }
};

const handleBackgroundFileSelect = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;

    isUploadingBackground.value = true;
    const url = await uploadImage(file);
    isUploadingBackground.value = false;

    if (url) {
        updateBackground({ type: 'image', imageUrl: url });
    }

    if (backgroundFileInput.value) {
        backgroundFileInput.value.value = '';
    }
};

const handleLogoFileSelect = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;

    isUploadingLogo.value = true;
    const url = await uploadImage(file);
    isUploadingLogo.value = false;

    if (url) {
        updateState({
            logo: {
                imageUrl: url,
                width: 150,
                height: 50,
                x: 60,
                y: 60,
            },
        });
    }

    if (logoFileInput.value) {
        logoFileInput.value.value = '';
    }
};

const removeLogo = () => {
    updateState({ logo: null });
};

const setBackgroundType = (type: 'color' | 'image') => {
    updateBackground({ type });
};
</script>

<template>
    <div class="space-y-6">
        <!-- Background Section -->
        <div class="border-b border-gray-200 pb-6">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Background</h4>

            <div class="flex gap-2 mb-4">
                <button
                    type="button"
                    @click="setBackgroundType('color')"
                    :class="[
                        'flex-1 px-3 py-2 text-sm font-medium rounded-md transition-colors',
                        modelValue.background.type === 'color'
                            ? 'bg-gray-800 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    ]"
                >
                    Color
                </button>
                <button
                    type="button"
                    @click="setBackgroundType('image')"
                    :class="[
                        'flex-1 px-3 py-2 text-sm font-medium rounded-md transition-colors',
                        modelValue.background.type === 'image'
                            ? 'bg-gray-800 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    ]"
                >
                    Image
                </button>
            </div>

            <div v-if="modelValue.background.type === 'color'">
                <InputLabel value="Background Color" />
                <div class="mt-1 flex items-center gap-2">
                    <input
                        type="color"
                        :value="modelValue.background.color"
                        @input="updateBackground({ color: ($event.target as HTMLInputElement).value })"
                        class="h-10 w-10 cursor-pointer rounded border border-gray-300 p-1"
                    />
                    <input
                        type="text"
                        :value="modelValue.background.color"
                        @blur="updateBackground({ color: ($event.target as HTMLInputElement).value })"
                        class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm uppercase"
                        maxlength="7"
                        placeholder="#000000"
                    />
                </div>
            </div>

            <div v-else>
                <input
                    ref="backgroundFileInput"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="handleBackgroundFileSelect"
                />
                <button
                    type="button"
                    @click="backgroundFileInput?.click()"
                    :disabled="isUploadingBackground"
                    class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:border-gray-400 hover:bg-gray-50 transition-colors disabled:opacity-50"
                >
                    <span v-if="isUploadingBackground">Uploading...</span>
                    <span v-else-if="modelValue.background.imageUrl">Change Background Image</span>
                    <span v-else>Upload Background Image</span>
                </button>
                <div v-if="modelValue.background.imageUrl" class="mt-2">
                    <img
                        :src="modelValue.background.imageUrl"
                        alt="Background preview"
                        class="w-full h-20 object-cover rounded-md"
                    />
                </div>
            </div>
        </div>

        <!-- Title Section -->
        <div class="border-b border-gray-200 pb-6">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Title Text</h4>

            <div class="space-y-4">
                <div>
                    <InputLabel for="og-title-content" value="Text" />
                    <textarea
                        id="og-title-content"
                        :value="modelValue.title.content"
                        @input="updateTitle({ content: ($event.target as HTMLTextAreaElement).value })"
                        rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        placeholder="Enter your title..."
                    ></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="og-title-font" value="Font" />
                        <select
                            id="og-title-font"
                            :value="modelValue.title.fontFamily"
                            @change="updateTitle({ fontFamily: ($event.target as HTMLSelectElement).value })"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        >
                            <option v-for="font in fontFamilies" :key="font.value" :value="font.value">
                                {{ font.label }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <InputLabel for="og-title-size" value="Size (px)" />
                        <input
                            id="og-title-size"
                            type="number"
                            :value="modelValue.title.fontSize"
                            @input="updateTitle({ fontSize: Number(($event.target as HTMLInputElement).value) })"
                            min="24"
                            max="120"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        />
                    </div>
                </div>

                <div>
                    <InputLabel value="Text Color" />
                    <div class="mt-1 flex items-center gap-2">
                        <input
                            type="color"
                            :value="modelValue.title.color"
                            @input="updateTitle({ color: ($event.target as HTMLInputElement).value })"
                            class="h-10 w-10 cursor-pointer rounded border border-gray-300 p-1"
                        />
                        <input
                            type="text"
                            :value="modelValue.title.color"
                            @blur="updateTitle({ color: ($event.target as HTMLInputElement).value })"
                            class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm uppercase"
                            maxlength="7"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="og-title-x" value="Position X" />
                        <input
                            id="og-title-x"
                            type="range"
                            :value="modelValue.title.x"
                            @input="updateTitle({ x: Number(($event.target as HTMLInputElement).value) })"
                            min="0"
                            max="400"
                            class="mt-1 block w-full"
                        />
                        <span class="text-xs text-gray-500">{{ modelValue.title.x }}px</span>
                    </div>

                    <div>
                        <InputLabel for="og-title-y" value="Position Y" />
                        <input
                            id="og-title-y"
                            type="range"
                            :value="modelValue.title.y"
                            @input="updateTitle({ y: Number(($event.target as HTMLInputElement).value) })"
                            min="0"
                            max="500"
                            class="mt-1 block w-full"
                        />
                        <span class="text-xs text-gray-500">{{ modelValue.title.y }}px</span>
                    </div>
                </div>

                <div>
                    <InputLabel for="og-title-width" value="Max Width" />
                    <input
                        id="og-title-width"
                        type="range"
                        :value="modelValue.title.maxWidth"
                        @input="updateTitle({ maxWidth: Number(($event.target as HTMLInputElement).value) })"
                        min="200"
                        max="1100"
                        class="mt-1 block w-full"
                    />
                    <span class="text-xs text-gray-500">{{ modelValue.title.maxWidth }}px</span>
                </div>
            </div>
        </div>

        <!-- Logo Section -->
        <div>
            <h4 class="text-sm font-medium text-gray-900 mb-4">Logo (Optional)</h4>

            <input
                ref="logoFileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleLogoFileSelect"
            />

            <div v-if="!modelValue.logo">
                <button
                    type="button"
                    @click="logoFileInput?.click()"
                    :disabled="isUploadingLogo"
                    class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:border-gray-400 hover:bg-gray-50 transition-colors disabled:opacity-50"
                >
                    <span v-if="isUploadingLogo">Uploading...</span>
                    <span v-else>Upload Logo</span>
                </button>
            </div>

            <div v-else class="space-y-4">
                <div class="flex items-center gap-4">
                    <img
                        :src="modelValue.logo.imageUrl"
                        alt="Logo preview"
                        class="h-12 w-auto object-contain"
                    />
                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="logoFileInput?.click()"
                            :disabled="isUploadingLogo"
                            class="px-3 py-1 text-sm text-gray-700 hover:text-gray-800"
                        >
                            Change
                        </button>
                        <button
                            type="button"
                            @click="removeLogo"
                            class="px-3 py-1 text-sm text-red-600 hover:text-red-800"
                        >
                            Remove
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="og-logo-width" value="Width" />
                        <input
                            id="og-logo-width"
                            type="range"
                            :value="modelValue.logo.width"
                            @input="updateLogo({ width: Number(($event.target as HTMLInputElement).value) })"
                            min="50"
                            max="400"
                            class="mt-1 block w-full"
                        />
                        <span class="text-xs text-gray-500">{{ modelValue.logo.width }}px</span>
                    </div>

                    <div>
                        <InputLabel for="og-logo-height" value="Height" />
                        <input
                            id="og-logo-height"
                            type="range"
                            :value="modelValue.logo.height"
                            @input="updateLogo({ height: Number(($event.target as HTMLInputElement).value) })"
                            min="20"
                            max="200"
                            class="mt-1 block w-full"
                        />
                        <span class="text-xs text-gray-500">{{ modelValue.logo.height }}px</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="og-logo-x" value="Position X" />
                        <input
                            id="og-logo-x"
                            type="range"
                            :value="modelValue.logo.x"
                            @input="updateLogo({ x: Number(($event.target as HTMLInputElement).value) })"
                            min="0"
                            max="1100"
                            class="mt-1 block w-full"
                        />
                        <span class="text-xs text-gray-500">{{ modelValue.logo.x }}px</span>
                    </div>

                    <div>
                        <InputLabel for="og-logo-y" value="Position Y" />
                        <input
                            id="og-logo-y"
                            type="range"
                            :value="modelValue.logo.y"
                            @input="updateLogo({ y: Number(($event.target as HTMLInputElement).value) })"
                            min="0"
                            max="580"
                            class="mt-1 block w-full"
                        />
                        <span class="text-xs text-gray-500">{{ modelValue.logo.y }}px</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
