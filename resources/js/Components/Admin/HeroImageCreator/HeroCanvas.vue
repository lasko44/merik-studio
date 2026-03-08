<script setup lang="ts">
import { ref, watch, onMounted, nextTick } from 'vue';
import type { HeroImageState, GradientColorStop } from '@/types/hero-creator';

interface Props {
    state: HeroImageState;
}

const props = defineProps<Props>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
const backgroundImage = ref<HTMLImageElement | null>(null);

const loadImage = (url: string): Promise<HTMLImageElement> => {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = url;
    });
};

const createGradient = (
    ctx: CanvasRenderingContext2D,
    width: number,
    height: number
): CanvasGradient => {
    const { gradient } = props.state.background;

    let canvasGradient: CanvasGradient;

    if (gradient.type === 'radial') {
        const centerX = width / 2;
        const centerY = height / 2;
        const radius = Math.max(width, height) / 2;
        canvasGradient = ctx.createRadialGradient(centerX, centerY, 0, centerX, centerY, radius);
    } else {
        // Linear gradient - convert angle to coordinates
        const angleRad = (gradient.angle - 90) * (Math.PI / 180);
        const x1 = width / 2 - Math.cos(angleRad) * width;
        const y1 = height / 2 - Math.sin(angleRad) * height;
        const x2 = width / 2 + Math.cos(angleRad) * width;
        const y2 = height / 2 + Math.sin(angleRad) * height;
        canvasGradient = ctx.createLinearGradient(x1, y1, x2, y2);
    }

    // Sort color stops by position and add them
    const sortedStops = [...gradient.colorStops].sort((a, b) => a.position - b.position);
    sortedStops.forEach((stop: GradientColorStop) => {
        canvasGradient.addColorStop(stop.position / 100, stop.color);
    });

    return canvasGradient;
};

const wrapText = (
    ctx: CanvasRenderingContext2D,
    text: string,
    x: number,
    y: number,
    maxWidth: number,
    lineHeight: number,
    alignment: 'left' | 'center' | 'right'
): number => {
    const words = text.split(' ');
    let line = '';
    let currentY = y;

    ctx.textAlign = alignment;

    for (let i = 0; i < words.length; i++) {
        const testLine = line + words[i] + ' ';
        const metrics = ctx.measureText(testLine);
        const testWidth = metrics.width;

        if (testWidth > maxWidth && i > 0) {
            ctx.fillText(line.trim(), x, currentY);
            line = words[i] + ' ';
            currentY += lineHeight;
        } else {
            line = testLine;
        }
    }
    ctx.fillText(line.trim(), x, currentY);

    return currentY;
};

const render = async () => {
    const canvas = canvasRef.value;
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const { width, height } = props.state.dimensions;
    canvas.width = width;
    canvas.height = height;

    // Clear canvas
    ctx.clearRect(0, 0, width, height);

    const { background } = props.state;

    // Draw background based on type
    if (background.type === 'image' && background.imageUrl) {
        try {
            if (!backgroundImage.value || backgroundImage.value.src !== background.imageUrl) {
                backgroundImage.value = await loadImage(background.imageUrl);
            }
            // Draw image covering the entire canvas
            const img = backgroundImage.value;
            const scale = Math.max(width / img.width, height / img.height);
            const x = (width - img.width * scale) / 2;
            const y = (height - img.height * scale) / 2;
            ctx.drawImage(img, x, y, img.width * scale, img.height * scale);
        } catch {
            // Fallback to solid color if image fails
            ctx.fillStyle = background.color;
            ctx.fillRect(0, 0, width, height);
        }
    } else if (background.type === 'gradient') {
        ctx.fillStyle = createGradient(ctx, width, height);
        ctx.fillRect(0, 0, width, height);
    } else {
        // Solid color
        ctx.fillStyle = background.color;
        ctx.fillRect(0, 0, width, height);
    }

    // Draw overlay if enabled
    if (background.overlay.enabled) {
        ctx.fillStyle = background.overlay.color;
        ctx.globalAlpha = background.overlay.opacity / 100;
        ctx.fillRect(0, 0, width, height);
        ctx.globalAlpha = 1;
    }

    // Draw text preview
    const { text } = props.state;
    const padding = 60;
    const maxTextWidth = width - padding * 2;

    let textX: number;
    if (text.alignment === 'center') {
        textX = width / 2;
    } else if (text.alignment === 'right') {
        textX = width - padding;
    } else {
        textX = padding;
    }

    ctx.fillStyle = text.color;

    // Draw heading
    if (text.heading) {
        ctx.font = 'bold 64px Arial, sans-serif';
        const headingY = height / 2 - 40;
        const lastHeadingY = wrapText(ctx, text.heading, textX, headingY, maxTextWidth, 76, text.alignment);

        // Draw subheading
        if (text.subheading) {
            ctx.font = '32px Arial, sans-serif';
            ctx.globalAlpha = 0.9;
            wrapText(ctx, text.subheading, textX, lastHeadingY + 50, maxTextWidth, 40, text.alignment);
            ctx.globalAlpha = 1;
        }
    }
};

const getCanvas = (): HTMLCanvasElement | null => {
    return canvasRef.value;
};

defineExpose({ getCanvas, render });

watch(() => props.state, render, { deep: true });

onMounted(async () => {
    await nextTick();
    render();
});
</script>

<template>
    <div class="hero-canvas-container">
        <canvas
            ref="canvasRef"
            class="hero-canvas"
        />
    </div>
</template>

<style scoped>
.hero-canvas-container {
    width: 100%;
    background-color: #f3f4f6;
    border-radius: 0.5rem;
    overflow: hidden;
}

.hero-canvas {
    width: 100%;
    height: auto;
    display: block;
}
</style>
