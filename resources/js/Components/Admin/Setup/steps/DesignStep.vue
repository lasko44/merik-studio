<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import ImageUploader from '@/Components/Admin/ImageUploader.vue';

interface Props {
    style: string;
    colorMood: string;
    primaryColor: string | null;
    logo: string | null;
    errors: Record<string, string>;
}

defineProps<Props>();

const emit = defineEmits<{
    'update:style': [value: string];
    'update:colorMood': [value: string];
    'update:primaryColor': [value: string | null];
    'update:logo': [value: string | null];
}>();

const styles = [
    { value: 'modern', label: 'Modern', description: 'Clean lines, bold typography, contemporary feel', colors: ['#6366F1', '#EC4899', '#14B8A6'] },
    { value: 'classic', label: 'Classic', description: 'Timeless elegance, sophisticated and refined', colors: ['#1E40AF', '#059669', '#D97706'] },
    { value: 'minimal', label: 'Minimal', description: 'Simple, clean, focus on content', colors: ['#475569', '#64748B', '#0EA5E9'] },
    { value: 'bold', label: 'Bold', description: 'Vibrant colors, strong visual impact', colors: ['#7C3AED', '#A855F7', '#F472B6'] },
];

const colorMoods = [
    { value: 'professional', label: 'Professional', description: 'Trust and reliability', color: '#1e40af' },
    { value: 'friendly', label: 'Friendly', description: 'Approachable and warm', color: '#2563eb' },
    { value: 'energetic', label: 'Energetic', description: 'Dynamic and exciting', color: '#dc2626' },
    { value: 'calm', label: 'Calm', description: 'Peaceful and serene', color: '#0d9488' },
    { value: 'luxury', label: 'Luxury', description: 'Premium and exclusive', color: '#1f2937' },
];
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Design Preferences</h3>
            <p class="mt-1 text-sm text-gray-500">Choose a style and color mood that matches your brand.</p>
        </div>

        <div class="space-y-6">
            <!-- Style Selection -->
            <div>
                <InputLabel value="Style Preference *" />
                <div class="mt-2 grid grid-cols-2 gap-4">
                    <button
                        v-for="s in styles"
                        :key="s.value"
                        type="button"
                        @click="emit('update:style', s.value)"
                        :class="[
                            style === s.value
                                ? 'border-indigo-600 ring-2 ring-indigo-600'
                                : 'border-gray-300 hover:border-gray-400',
                            'relative flex flex-col p-4 rounded-lg border cursor-pointer focus:outline-none'
                        ]"
                    >
                        <div class="flex gap-1 mb-2">
                            <div
                                v-for="(color, index) in s.colors"
                                :key="index"
                                :style="{ backgroundColor: color }"
                                class="w-6 h-6 rounded-full"
                            ></div>
                        </div>
                        <span class="block text-sm font-medium text-gray-900">{{ s.label }}</span>
                        <span class="block text-xs text-gray-500 mt-1">{{ s.description }}</span>
                    </button>
                </div>
                <InputError :message="errors.style" class="mt-2" />
            </div>

            <!-- Color Mood -->
            <div>
                <InputLabel value="Color Mood *" />
                <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <button
                        v-for="mood in colorMoods"
                        :key="mood.value"
                        type="button"
                        @click="emit('update:colorMood', mood.value)"
                        :class="[
                            colorMood === mood.value
                                ? 'border-indigo-600 ring-2 ring-indigo-600'
                                : 'border-gray-300 hover:border-gray-400',
                            'relative flex items-center gap-3 p-3 rounded-lg border cursor-pointer focus:outline-none'
                        ]"
                    >
                        <div
                            :style="{ backgroundColor: mood.color }"
                            class="w-8 h-8 rounded-full flex-shrink-0"
                        ></div>
                        <div class="text-left">
                            <span class="block text-sm font-medium text-gray-900">{{ mood.label }}</span>
                            <span class="block text-xs text-gray-500">{{ mood.description }}</span>
                        </div>
                    </button>
                </div>
                <InputError :message="errors.colorMood" class="mt-2" />
            </div>

            <!-- Primary Color Override -->
            <div>
                <InputLabel for="primaryColor" value="Custom Primary Color (optional)" />
                <div class="mt-2 flex items-center gap-3">
                    <input
                        type="color"
                        :value="primaryColor || '#3B82F6'"
                        @input="emit('update:primaryColor', ($event.target as HTMLInputElement).value)"
                        class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                    />
                    <input
                        type="text"
                        :value="primaryColor || ''"
                        @input="emit('update:primaryColor', ($event.target as HTMLInputElement).value || null)"
                        placeholder="#3B82F6"
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                    <button
                        v-if="primaryColor"
                        type="button"
                        @click="emit('update:primaryColor', null)"
                        class="text-sm text-gray-500 hover:text-gray-700"
                    >
                        Clear
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">Override the default primary color with your brand color</p>
                <InputError :message="errors.primaryColor" class="mt-2" />
            </div>

            <!-- Logo Upload -->
            <div>
                <InputLabel value="Logo (optional)" />
                <div class="mt-2">
                    <ImageUploader
                        :model-value="logo || ''"
                        @update:model-value="emit('update:logo', $event || null)"
                        label="Logo"
                    />
                </div>
                <p class="mt-1 text-xs text-gray-500">Upload your logo. You can also add this later in Settings.</p>
                <InputError :message="errors.logo" class="mt-2" />
            </div>
        </div>
    </div>
</template>
