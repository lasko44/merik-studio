<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

export interface ColorValue {
    color: string;
    opacity: number;
}

interface Props {
    modelValue: string | ColorValue;
    showOpacity?: boolean;
}

interface ThemeColors {
    primaryColor?: string;
    secondaryColor?: string;
    accentColor?: string;
    primary_color?: string;
    secondary_color?: string;
    accent_color?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: '',
    showOpacity: true,
});

const emit = defineEmits<{
    'update:modelValue': [value: ColorValue];
}>();

// Neutral colors palette
const neutralColors = [
    { name: 'White', value: '#ffffff' },
    { name: 'Gray 100', value: '#f3f4f6' },
    { name: 'Gray 200', value: '#e5e7eb' },
    { name: 'Gray 300', value: '#d1d5db' },
    { name: 'Gray 400', value: '#9ca3af' },
    { name: 'Gray 500', value: '#6b7280' },
    { name: 'Gray 600', value: '#4b5563' },
    { name: 'Gray 700', value: '#374151' },
    { name: 'Gray 800', value: '#1f2937' },
    { name: 'Gray 900', value: '#111827' },
    { name: 'Black', value: '#000000' },
];

// Parse model value - handle both string and object formats
const parsedValue = computed((): ColorValue => {
    if (typeof props.modelValue === 'string') {
        // Check if it's an rgba string
        const rgbaMatch = props.modelValue.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/);
        if (rgbaMatch) {
            const r = parseInt(rgbaMatch[1]);
            const g = parseInt(rgbaMatch[2]);
            const b = parseInt(rgbaMatch[3]);
            const a = rgbaMatch[4] ? parseFloat(rgbaMatch[4]) : 1;
            const hex = '#' + [r, g, b].map(x => x.toString(16).padStart(2, '0')).join('');
            return { color: hex, opacity: Math.round(a * 100) };
        }
        return {
            color: props.modelValue || '#e5e7eb',
            opacity: 100,
        };
    }
    return {
        color: props.modelValue.color || '#e5e7eb',
        opacity: props.modelValue.opacity ?? 100,
    };
});

// Get theme colors from page props
const themeColors = computed(() => {
    const page = usePage();
    const settings = (page.props as Record<string, unknown>).siteSettings as ThemeColors | undefined;
    return [
        { name: 'Primary', value: settings?.primaryColor || settings?.primary_color || '#6366F1' },
        { name: 'Secondary', value: settings?.secondaryColor || settings?.secondary_color || '#10B981' },
        { name: 'Accent', value: settings?.accentColor || settings?.accent_color || '#F59E0B' },
    ];
});

// Local state for the slider
const localOpacity = ref(parsedValue.value.opacity);

// Watch for external changes
watch(() => parsedValue.value.opacity, (newVal) => {
    localOpacity.value = newVal;
});

const updateColor = (colorValue: string) => {
    emit('update:modelValue', {
        color: colorValue,
        opacity: localOpacity.value,
    });
};

const updateOpacity = (opacity: number) => {
    localOpacity.value = opacity;
    emit('update:modelValue', {
        color: parsedValue.value.color,
        opacity: opacity,
    });
};

// Check if a color is currently selected
const isSelected = (colorValue: string): boolean => {
    return parsedValue.value.color.toLowerCase() === colorValue.toLowerCase();
};

// Get preview color with opacity
const previewColor = computed(() => {
    const hex = parsedValue.value.color;
    const opacity = localOpacity.value / 100;

    // Convert hex to rgba
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);

    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
});
</script>

<template>
    <div class="space-y-3">
        <!-- Color Preview -->
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-md border border-gray-300 shadow-sm relative overflow-hidden"
            >
                <!-- Checkered background for transparency -->
                <div
                    class="absolute inset-0"
                    style="background-image: linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%); background-size: 8px 8px; background-position: 0 0, 0 4px, 4px -4px, -4px 0px;"
                />
                <div class="absolute inset-0" :style="{ backgroundColor: previewColor }" />
            </div>
            <div class="text-sm text-gray-600">
                <div class="font-medium">{{ parsedValue.color.toUpperCase() }}</div>
                <div v-if="showOpacity" class="text-xs text-gray-400">{{ localOpacity }}% opacity</div>
            </div>
        </div>

        <!-- Theme Colors -->
        <div>
            <div class="text-xs font-medium text-gray-500 mb-1.5">Theme Colors</div>
            <div class="flex gap-1.5">
                <button
                    v-for="color in themeColors"
                    :key="color.name"
                    type="button"
                    @click="updateColor(color.value)"
                    class="w-8 h-8 rounded-md border-2 transition-all hover:scale-110"
                    :class="isSelected(color.value) ? 'border-gray-800 ring-2 ring-offset-1 ring-gray-400' : 'border-transparent hover:border-gray-300'"
                    :style="{ backgroundColor: color.value }"
                    :title="color.name"
                />
            </div>
        </div>

        <!-- Neutral Colors -->
        <div>
            <div class="text-xs font-medium text-gray-500 mb-1.5">Neutral Colors</div>
            <div class="flex flex-wrap gap-1.5">
                <button
                    v-for="color in neutralColors"
                    :key="color.name"
                    type="button"
                    @click="updateColor(color.value)"
                    class="w-6 h-6 rounded border-2 transition-all hover:scale-110"
                    :class="[
                        isSelected(color.value) ? 'border-gray-800 ring-2 ring-offset-1 ring-gray-400' : 'border-transparent hover:border-gray-400',
                        color.value === '#ffffff' ? 'border-gray-200' : ''
                    ]"
                    :style="{ backgroundColor: color.value }"
                    :title="color.name"
                />
            </div>
        </div>

        <!-- Opacity Slider -->
        <div v-if="showOpacity">
            <div class="text-xs font-medium text-gray-500 mb-1.5">Opacity</div>
            <div class="flex items-center gap-3">
                <div class="flex-1 relative h-6 rounded-md overflow-hidden border border-gray-200">
                    <!-- Checkered background for transparency preview -->
                    <div
                        class="absolute inset-0"
                        style="background-image: linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%); background-size: 8px 8px; background-position: 0 0, 0 4px, 4px -4px, -4px 0px;"
                    />
                    <!-- Color gradient overlay -->
                    <div
                        class="absolute inset-0"
                        :style="{
                            background: `linear-gradient(to right, transparent, ${parsedValue.color})`
                        }"
                    />
                    <!-- Range input -->
                    <input
                        type="range"
                        :value="localOpacity"
                        min="0"
                        max="100"
                        step="1"
                        @input="updateOpacity(parseInt(($event.target as HTMLInputElement).value))"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    />
                    <!-- Visual slider thumb position -->
                    <div
                        class="absolute top-0 bottom-0 w-1 bg-white border border-gray-400 rounded shadow"
                        :style="{ left: `calc(${localOpacity}% - 2px)` }"
                    />
                </div>
                <span class="text-sm font-medium text-gray-700 min-w-[3rem] text-right">
                    {{ localOpacity }}%
                </span>
            </div>
        </div>
    </div>
</template>
