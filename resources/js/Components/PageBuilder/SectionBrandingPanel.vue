<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import ImageUploader from '@/Components/Admin/ImageUploader.vue';
import InputLabel from '@/Components/InputLabel.vue';
import type { SectionBranding } from '@/types/admin';

interface Props {
    modelValue: SectionBranding;
    show: boolean;
    sectionLabel: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:modelValue': [value: SectionBranding];
    close: [];
}>();

const localBranding = ref<SectionBranding>({ ...props.modelValue });

watch(() => props.modelValue, (newValue) => {
    localBranding.value = { ...newValue };
}, { deep: true });

watch(() => props.show, (show) => {
    if (show) {
        localBranding.value = { ...props.modelValue };
    }
});

const updateBranding = <K extends keyof SectionBranding>(key: K, value: SectionBranding[K]) => {
    localBranding.value = { ...localBranding.value, [key]: value };
    emit('update:modelValue', localBranding.value);
};

const paddingOptions = [
    { value: 'none', label: 'None' },
    { value: 'sm', label: 'Small' },
    { value: 'md', label: 'Medium' },
    { value: 'lg', label: 'Large' },
    { value: 'xl', label: 'Extra Large' },
] as const;

const shadowOptions = [
    { value: 'none', label: 'None' },
    { value: 'sm', label: 'Small' },
    { value: 'md', label: 'Medium' },
    { value: 'lg', label: 'Large' },
] as const;

const roundedOptions = [
    { value: 'none', label: 'None' },
    { value: 'sm', label: 'Small' },
    { value: 'md', label: 'Medium' },
    { value: 'lg', label: 'Large' },
] as const;

const maxWidthOptions = [
    { value: 'full', label: 'Full Width' },
    { value: 'max-w-7xl', label: '7XL (80rem)' },
    { value: 'max-w-6xl', label: '6XL (72rem)' },
    { value: 'max-w-5xl', label: '5XL (64rem)' },
    { value: 'max-w-4xl', label: '4XL (56rem)' },
    { value: 'max-w-3xl', label: '3XL (48rem)' },
] as const;

const close = () => {
    emit('close');
};

const resetToDefaults = () => {
    const defaults: SectionBranding = {
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
    };
    localBranding.value = defaults;
    emit('update:modelValue', defaults);
};

const overlayValue = computed({
    get: () => localBranding.value.backgroundOverlay,
    set: (value: number) => updateBranding('backgroundOverlay', value),
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-50 overflow-hidden">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75" @click="close" />

                <!-- Panel -->
                <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <Transition
                        enter-active-class="transition duration-300 ease-out transform"
                        enter-from-class="translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-active-class="transition duration-200 ease-in transform"
                        leave-from-class="translate-x-0"
                        leave-to-class="translate-x-full"
                    >
                        <div v-if="show" class="w-screen max-w-md">
                            <div class="flex h-full flex-col bg-white shadow-xl">
                                <!-- Header -->
                                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-4">
                                    <div>
                                        <h2 class="text-lg font-medium text-gray-900">Section Branding</h2>
                                        <p class="text-sm text-gray-500">{{ sectionLabel }}</p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="close"
                                        class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
                                    <!-- Background -->
                                    <div class="space-y-4">
                                        <h3 class="text-sm font-medium text-gray-900 border-b border-gray-200 pb-2">Background</h3>

                                        <div>
                                            <InputLabel value="Background Color" />
                                            <div class="mt-1 flex items-center gap-2">
                                                <input
                                                    type="color"
                                                    :value="localBranding.backgroundColor || '#ffffff'"
                                                    @input="updateBranding('backgroundColor', ($event.target as HTMLInputElement).value)"
                                                    class="h-10 w-14 rounded border border-gray-300 cursor-pointer"
                                                />
                                                <input
                                                    type="text"
                                                    :value="localBranding.backgroundColor || ''"
                                                    @input="updateBranding('backgroundColor', ($event.target as HTMLInputElement).value || null)"
                                                    class="flex-1 rounded-md border-gray-300 text-sm"
                                                    placeholder="No background"
                                                />
                                                <button
                                                    v-if="localBranding.backgroundColor"
                                                    type="button"
                                                    @click="updateBranding('backgroundColor', null)"
                                                    class="text-gray-400 hover:text-gray-600"
                                                    title="Clear"
                                                >
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div>
                                            <InputLabel value="Background Image" />
                                            <div class="mt-1">
                                                <ImageUploader
                                                    :model-value="localBranding.backgroundImage || ''"
                                                    @update:model-value="updateBranding('backgroundImage', $event || null)"
                                                    label="Background Image"
                                                />
                                            </div>
                                        </div>

                                        <div v-if="localBranding.backgroundImage">
                                            <InputLabel value="Overlay Opacity" />
                                            <div class="mt-1 flex items-center gap-3">
                                                <input
                                                    type="range"
                                                    v-model.number="overlayValue"
                                                    min="0"
                                                    max="100"
                                                    step="5"
                                                    class="flex-1"
                                                />
                                                <span class="text-sm text-gray-600 w-10">{{ overlayValue }}%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Text Color -->
                                    <div class="space-y-4">
                                        <h3 class="text-sm font-medium text-gray-900 border-b border-gray-200 pb-2">Text</h3>

                                        <div>
                                            <InputLabel value="Text Color" />
                                            <div class="mt-1 flex items-center gap-2">
                                                <input
                                                    type="color"
                                                    :value="localBranding.textColor || '#000000'"
                                                    @input="updateBranding('textColor', ($event.target as HTMLInputElement).value)"
                                                    class="h-10 w-14 rounded border border-gray-300 cursor-pointer"
                                                />
                                                <input
                                                    type="text"
                                                    :value="localBranding.textColor || ''"
                                                    @input="updateBranding('textColor', ($event.target as HTMLInputElement).value || null)"
                                                    class="flex-1 rounded-md border-gray-300 text-sm"
                                                    placeholder="Inherit"
                                                />
                                                <button
                                                    v-if="localBranding.textColor"
                                                    type="button"
                                                    @click="updateBranding('textColor', null)"
                                                    class="text-gray-400 hover:text-gray-600"
                                                    title="Clear"
                                                >
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Spacing -->
                                    <div class="space-y-4">
                                        <h3 class="text-sm font-medium text-gray-900 border-b border-gray-200 pb-2">Spacing</h3>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <InputLabel value="Padding Top" />
                                                <select
                                                    :value="localBranding.paddingTop"
                                                    @change="updateBranding('paddingTop', ($event.target as HTMLSelectElement).value as SectionBranding['paddingTop'])"
                                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm"
                                                >
                                                    <option v-for="opt in paddingOptions" :key="opt.value" :value="opt.value">
                                                        {{ opt.label }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div>
                                                <InputLabel value="Padding Bottom" />
                                                <select
                                                    :value="localBranding.paddingBottom"
                                                    @change="updateBranding('paddingBottom', ($event.target as HTMLSelectElement).value as SectionBranding['paddingBottom'])"
                                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm"
                                                >
                                                    <option v-for="opt in paddingOptions" :key="opt.value" :value="opt.value">
                                                        {{ opt.label }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <InputLabel value="Max Width" />
                                            <select
                                                :value="localBranding.maxWidth"
                                                @change="updateBranding('maxWidth', ($event.target as HTMLSelectElement).value as SectionBranding['maxWidth'])"
                                                class="mt-1 block w-full rounded-md border-gray-300 text-sm"
                                            >
                                                <option v-for="opt in maxWidthOptions" :key="opt.value" :value="opt.value">
                                                    {{ opt.label }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Borders -->
                                    <div class="space-y-4">
                                        <h3 class="text-sm font-medium text-gray-900 border-b border-gray-200 pb-2">Borders</h3>

                                        <div class="flex gap-6">
                                            <label class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    :checked="localBranding.borderTop"
                                                    @change="updateBranding('borderTop', ($event.target as HTMLInputElement).checked)"
                                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                />
                                                <span class="ml-2 text-sm text-gray-600">Border Top</span>
                                            </label>

                                            <label class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    :checked="localBranding.borderBottom"
                                                    @change="updateBranding('borderBottom', ($event.target as HTMLInputElement).checked)"
                                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                />
                                                <span class="ml-2 text-sm text-gray-600">Border Bottom</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Effects -->
                                    <div class="space-y-4">
                                        <h3 class="text-sm font-medium text-gray-900 border-b border-gray-200 pb-2">Effects</h3>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <InputLabel value="Shadow" />
                                                <select
                                                    :value="localBranding.shadow"
                                                    @change="updateBranding('shadow', ($event.target as HTMLSelectElement).value as SectionBranding['shadow'])"
                                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm"
                                                >
                                                    <option v-for="opt in shadowOptions" :key="opt.value" :value="opt.value">
                                                        {{ opt.label }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div>
                                                <InputLabel value="Rounded Corners" />
                                                <select
                                                    :value="localBranding.rounded"
                                                    @change="updateBranding('rounded', ($event.target as HTMLSelectElement).value as SectionBranding['rounded'])"
                                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm"
                                                >
                                                    <option v-for="opt in roundedOptions" :key="opt.value" :value="opt.value">
                                                        {{ opt.label }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="flex items-center justify-between border-t border-gray-200 px-4 py-4">
                                    <button
                                        type="button"
                                        @click="resetToDefaults"
                                        class="text-sm text-gray-500 hover:text-gray-700"
                                    >
                                        Reset to Defaults
                                    </button>
                                    <button
                                        type="button"
                                        @click="close"
                                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                                    >
                                        Done
                                    </button>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
