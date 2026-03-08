<script setup lang="ts">
import { computed } from 'vue';

interface ColorValue {
    color: string;
    opacity: number;
}

interface Props {
    size?: 'small' | 'medium' | 'large';
    showLine?: boolean;
    lineColor?: string | ColorValue;
}

const props = withDefaults(defineProps<Props>(), {
    size: 'medium',
    showLine: false,
    lineColor: '#e5e7eb',
});

const sizeHeights: Record<string, string> = {
    small: '1rem',
    medium: '2rem',
    large: '4rem',
};

const spacerStyle = computed(() => ({
    height: sizeHeights[props.size],
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
    <div :style="spacerStyle" class="sidebar-spacer w-full flex items-center">
        <hr
            v-if="showLine"
            class="w-full border-t"
            :style="{ borderColor: parsedLineColor }"
        />
    </div>
</template>
