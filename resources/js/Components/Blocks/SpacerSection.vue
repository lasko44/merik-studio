<script setup lang="ts">
import { computed } from 'vue';

interface ColorValue {
    color: string;
    opacity: number;
}

interface Props {
    size?: 'small' | 'medium' | 'large' | 'xlarge';
    customHeight?: string | null;
    showLine?: boolean;
    lineColor?: string | ColorValue;
}

const props = withDefaults(defineProps<Props>(), {
    size: 'medium',
    customHeight: null,
    showLine: false,
    lineColor: '#e5e7eb',
});

const sizeHeights: Record<string, string> = {
    small: '2rem',
    medium: '4rem',
    large: '8rem',
    xlarge: '12rem',
};

const spacerStyle = computed(() => ({
    height: props.customHeight || sizeHeights[props.size],
}));

// Parse line color - handle both string and object formats
const parsedLineColor = computed(() => {
    if (typeof props.lineColor === 'string') {
        return props.lineColor;
    }

    // Object format with color and opacity
    const { color, opacity } = props.lineColor;
    const hex = color || '#e5e7eb';
    const alpha = (opacity ?? 100) / 100;

    // Convert hex to rgba
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);

    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
});
</script>

<template>
    <div
        :style="spacerStyle"
        class="spacer-section w-full"
    >
        <div
            v-if="showLine"
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center"
        >
            <hr
                class="w-full border-t"
                :style="{ borderColor: parsedLineColor }"
            />
        </div>
    </div>
</template>
