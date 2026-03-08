<script setup lang="ts">
import { ref, computed } from 'vue';
import axios from 'axios';
import InputLabel from '@/Components/InputLabel.vue';
import type { HeroImageState, GradientColorStop, ThemeColors } from '@/types/hero-creator';

interface Props {
    modelValue: HeroImageState;
    themeColors?: ThemeColors;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:modelValue': [value: HeroImageState];
}>();

const backgroundFileInput = ref<HTMLInputElement | null>(null);
const isUploadingBackground = ref(false);

const presetGradients = computed(() => {
    const primary = props.themeColors?.primaryColor || '#3B82F6';
    const secondary = props.themeColors?.secondaryColor || '#10B981';
    const accent = props.themeColors?.accentColor || '#F59E0B';

    return [
        {
            name: 'Theme Primary to Secondary',
            config: {
                type: 'linear' as const,
                angle: 135,
                colorStops: [
                    { color: primary, position: 0 },
                    { color: secondary, position: 100 },
                ],
            },
        },
        {
            name: 'Theme Trio',
            config: {
                type: 'linear' as const,
                angle: 135,
                colorStops: [
                    { color: primary, position: 0 },
                    { color: secondary, position: 50 },
                    { color: accent, position: 100 },
                ],
            },
        },
        {
            name: 'Indigo to Purple',
            config: {
                type: 'linear' as const,
                angle: 135,
                colorStops: [
                    { color: '#4F46E5', position: 0 },
                    { color: '#7C3AED', position: 100 },
                ],
            },
        },
        {
            name: 'Ocean Blue',
            config: {
                type: 'linear' as const,
                angle: 180,
                colorStops: [
                    { color: '#0EA5E9', position: 0 },
                    { color: '#2563EB', position: 100 },
                ],
            },
        },
        {
            name: 'Sunset',
            config: {
                type: 'linear' as const,
                angle: 135,
                colorStops: [
                    { color: '#F59E0B', position: 0 },
                    { color: '#EF4444', position: 50 },
                    { color: '#EC4899', position: 100 },
                ],
            },
        },
        {
            name: 'Forest',
            config: {
                type: 'linear' as const,
                angle: 135,
                colorStops: [
                    { color: '#059669', position: 0 },
                    { color: '#10B981', position: 100 },
                ],
            },
        },
        {
            name: 'Dark Radial',
            config: {
                type: 'radial' as const,
                angle: 0,
                colorStops: [
                    { color: '#374151', position: 0 },
                    { color: '#111827', position: 100 },
                ],
            },
        },
    ];
});

const updateState = (updates: Partial<HeroImageState>) => {
    emit('update:modelValue', { ...props.modelValue, ...updates });
};

const updateBackground = (updates: Partial<HeroImageState['background']>) => {
    updateState({
        background: { ...props.modelValue.background, ...updates },
    });
};

const updateGradient = (updates: Partial<HeroImageState['background']['gradient']>) => {
    updateBackground({
        gradient: { ...props.modelValue.background.gradient, ...updates },
    });
};

const updateOverlay = (updates: Partial<HeroImageState['background']['overlay']>) => {
    updateBackground({
        overlay: { ...props.modelValue.background.overlay, ...updates },
    });
};

const updateText = (updates: Partial<HeroImageState['text']>) => {
    updateState({
        text: { ...props.modelValue.text, ...updates },
    });
};

const updateColorStop = (index: number, updates: Partial<GradientColorStop>) => {
    const newStops = [...props.modelValue.background.gradient.colorStops];
    newStops[index] = { ...newStops[index], ...updates };
    updateGradient({ colorStops: newStops });
};

const addColorStop = () => {
    const stops = props.modelValue.background.gradient.colorStops;
    const lastStop = stops[stops.length - 1];
    const newPosition = Math.min(lastStop.position + 25, 100);
    updateGradient({
        colorStops: [...stops, { color: '#FFFFFF', position: newPosition }],
    });
};

const removeColorStop = (index: number) => {
    if (props.modelValue.background.gradient.colorStops.length <= 2) return;
    const newStops = props.modelValue.background.gradient.colorStops.filter((_, i) => i !== index);
    updateGradient({ colorStops: newStops });
};

const applyPresetGradient = (preset: typeof presetGradients.value[0]) => {
    updateBackground({
        type: 'gradient',
        gradient: { ...preset.config },
    });
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

const setBackgroundType = (type: 'solid' | 'gradient' | 'image') => {
    updateBackground({ type });
};
</script>

<template>
    <div class="space-y-6">
        <!-- Background Type Section -->
        <div class="border-b border-gray-200 pb-6">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Background Type</h4>

            <div class="flex gap-2 mb-4">
                <button
                    type="button"
                    @click="setBackgroundType('solid')"
                    :class="[
                        'flex-1 px-3 py-2 text-sm font-medium rounded-md transition-colors',
                        modelValue.background.type === 'solid'
                            ? 'bg-gray-800 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    ]"
                >
                    Solid
                </button>
                <button
                    type="button"
                    @click="setBackgroundType('gradient')"
                    :class="[
                        'flex-1 px-3 py-2 text-sm font-medium rounded-md transition-colors',
                        modelValue.background.type === 'gradient'
                            ? 'bg-gray-800 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    ]"
                >
                    Gradient
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

            <!-- Solid Color Controls -->
            <div v-if="modelValue.background.type === 'solid'">
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
                    />
                </div>
            </div>

            <!-- Gradient Controls -->
            <div v-else-if="modelValue.background.type === 'gradient'" class="space-y-4">
                <!-- Preset Gradients -->
                <div>
                    <InputLabel value="Preset Gradients" />
                    <div class="mt-2 grid grid-cols-4 gap-2">
                        <button
                            v-for="preset in presetGradients"
                            :key="preset.name"
                            type="button"
                            @click="applyPresetGradient(preset)"
                            class="h-10 rounded-md border border-gray-300 hover:border-gray-700 transition-colors"
                            :style="{
                                background: preset.config.type === 'linear'
                                    ? `linear-gradient(${preset.config.angle}deg, ${preset.config.colorStops.map(s => `${s.color} ${s.position}%`).join(', ')})`
                                    : `radial-gradient(circle, ${preset.config.colorStops.map(s => `${s.color} ${s.position}%`).join(', ')})`
                            }"
                            :title="preset.name"
                        ></button>
                    </div>
                </div>

                <!-- Gradient Type & Angle -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Type" />
                        <select
                            :value="modelValue.background.gradient.type"
                            @change="updateGradient({ type: ($event.target as HTMLSelectElement).value as 'linear' | 'radial' })"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        >
                            <option value="linear">Linear</option>
                            <option value="radial">Radial</option>
                        </select>
                    </div>

                    <div v-if="modelValue.background.gradient.type === 'linear'">
                        <InputLabel value="Angle" />
                        <div class="flex items-center gap-2 mt-1">
                            <input
                                type="range"
                                :value="modelValue.background.gradient.angle"
                                @input="updateGradient({ angle: Number(($event.target as HTMLInputElement).value) })"
                                min="0"
                                max="360"
                                class="flex-1"
                            />
                            <span class="text-sm text-gray-500 w-12">{{ modelValue.background.gradient.angle }}°</span>
                        </div>
                    </div>
                </div>

                <!-- Color Stops -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <InputLabel value="Color Stops" />
                        <button
                            type="button"
                            @click="addColorStop"
                            class="text-sm text-gray-700 hover:text-gray-800"
                        >
                            + Add Stop
                        </button>
                    </div>
                    <div class="space-y-2">
                        <div
                            v-for="(stop, index) in modelValue.background.gradient.colorStops"
                            :key="index"
                            class="flex items-center gap-2"
                        >
                            <input
                                type="color"
                                :value="stop.color"
                                @input="updateColorStop(index, { color: ($event.target as HTMLInputElement).value })"
                                class="h-8 w-8 cursor-pointer rounded border border-gray-300 p-0.5"
                            />
                            <input
                                type="range"
                                :value="stop.position"
                                @input="updateColorStop(index, { position: Number(($event.target as HTMLInputElement).value) })"
                                min="0"
                                max="100"
                                class="flex-1"
                            />
                            <span class="text-xs text-gray-500 w-8">{{ stop.position }}%</span>
                            <button
                                v-if="modelValue.background.gradient.colorStops.length > 2"
                                type="button"
                                @click="removeColorStop(index)"
                                class="p-1 text-gray-400 hover:text-red-500"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Upload Controls -->
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

        <!-- Overlay Section -->
        <div class="border-b border-gray-200 pb-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-medium text-gray-900">Color Overlay</h4>
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        :checked="modelValue.background.overlay.enabled"
                        @change="updateOverlay({ enabled: ($event.target as HTMLInputElement).checked })"
                        class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                    />
                    <span class="ml-2 text-sm text-gray-600">Enable</span>
                </label>
            </div>

            <div v-if="modelValue.background.overlay.enabled" class="space-y-4">
                <div>
                    <InputLabel value="Overlay Color" />
                    <div class="mt-1 flex items-center gap-2">
                        <input
                            type="color"
                            :value="modelValue.background.overlay.color"
                            @input="updateOverlay({ color: ($event.target as HTMLInputElement).value })"
                            class="h-10 w-10 cursor-pointer rounded border border-gray-300 p-1"
                        />
                        <input
                            type="text"
                            :value="modelValue.background.overlay.color"
                            @blur="updateOverlay({ color: ($event.target as HTMLInputElement).value })"
                            class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm uppercase"
                            maxlength="7"
                        />
                    </div>
                </div>

                <div>
                    <InputLabel value="Opacity" />
                    <div class="flex items-center gap-2 mt-1">
                        <input
                            type="range"
                            :value="modelValue.background.overlay.opacity"
                            @input="updateOverlay({ opacity: Number(($event.target as HTMLInputElement).value) })"
                            min="0"
                            max="100"
                            class="flex-1"
                        />
                        <span class="text-sm text-gray-500 w-10">{{ modelValue.background.overlay.opacity }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Text Preview Section -->
        <div>
            <h4 class="text-sm font-medium text-gray-900 mb-4">Text Preview (Optional)</h4>

            <div class="space-y-4">
                <div>
                    <InputLabel value="Heading" />
                    <input
                        type="text"
                        :value="modelValue.text.heading"
                        @input="updateText({ heading: ($event.target as HTMLInputElement).value })"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        placeholder="Enter heading for preview..."
                    />
                </div>

                <div>
                    <InputLabel value="Subheading" />
                    <input
                        type="text"
                        :value="modelValue.text.subheading"
                        @input="updateText({ subheading: ($event.target as HTMLInputElement).value })"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        placeholder="Enter subheading for preview..."
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Text Color" />
                        <div class="mt-1 flex items-center gap-2">
                            <input
                                type="color"
                                :value="modelValue.text.color"
                                @input="updateText({ color: ($event.target as HTMLInputElement).value })"
                                class="h-8 w-8 cursor-pointer rounded border border-gray-300 p-0.5"
                            />
                            <input
                                type="text"
                                :value="modelValue.text.color"
                                @blur="updateText({ color: ($event.target as HTMLInputElement).value })"
                                class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm uppercase"
                                maxlength="7"
                            />
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Alignment" />
                        <select
                            :value="modelValue.text.alignment"
                            @change="updateText({ alignment: ($event.target as HTMLSelectElement).value as 'left' | 'center' | 'right' })"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        >
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                        </select>
                    </div>
                </div>

                <p class="text-xs text-gray-500">
                    Note: Text is for preview only and will not be included in the exported image.
                    Configure your actual hero text in the block settings.
                </p>
            </div>
        </div>
    </div>
</template>
