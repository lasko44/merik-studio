<script setup lang="ts">
import { ref, watch, onMounted, nextTick } from 'vue';
import type { OgImageState } from '@/types/og-creator';

interface Props {
    state: OgImageState;
}

const props = defineProps<Props>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
const backgroundImage = ref<HTMLImageElement | null>(null);
const logoImage = ref<HTMLImageElement | null>(null);

const CANVAS_WIDTH = 1200;
const CANVAS_HEIGHT = 630;

const loadImage = (url: string): Promise<HTMLImageElement> => {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = url;
    });
};

const wrapText = (
    ctx: CanvasRenderingContext2D,
    text: string,
    x: number,
    y: number,
    maxWidth: number,
    lineHeight: number
): void => {
    const words = text.split(' ');
    let line = '';
    let currentY = y;

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
};

const render = async () => {
    const canvas = canvasRef.value;
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Clear canvas
    ctx.clearRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);

    // Draw background
    if (props.state.background.type === 'image' && props.state.background.imageUrl) {
        try {
            if (!backgroundImage.value || backgroundImage.value.src !== props.state.background.imageUrl) {
                backgroundImage.value = await loadImage(props.state.background.imageUrl);
            }
            // Draw image covering the entire canvas
            const img = backgroundImage.value;
            const scale = Math.max(CANVAS_WIDTH / img.width, CANVAS_HEIGHT / img.height);
            const x = (CANVAS_WIDTH - img.width * scale) / 2;
            const y = (CANVAS_HEIGHT - img.height * scale) / 2;
            ctx.drawImage(img, x, y, img.width * scale, img.height * scale);
        } catch {
            // Fallback to color if image fails
            ctx.fillStyle = props.state.background.color;
            ctx.fillRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);
        }
    } else {
        ctx.fillStyle = props.state.background.color;
        ctx.fillRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);
    }

    // Draw title
    const { title } = props.state;
    if (title.content) {
        ctx.font = `bold ${title.fontSize}px ${title.fontFamily}`;
        ctx.fillStyle = title.color;
        ctx.textAlign = 'left';
        ctx.textBaseline = 'top';

        const lineHeight = title.fontSize * 1.2;
        wrapText(ctx, title.content, title.x, title.y, title.maxWidth, lineHeight);
    }

    // Draw logo
    if (props.state.logo?.imageUrl) {
        try {
            if (!logoImage.value || logoImage.value.src !== props.state.logo.imageUrl) {
                logoImage.value = await loadImage(props.state.logo.imageUrl);
            }
            const { x, y, width, height } = props.state.logo;
            ctx.drawImage(logoImage.value, x, y, width, height);
        } catch {
            // Logo failed to load, skip drawing
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
    <div class="og-canvas-container">
        <canvas
            ref="canvasRef"
            :width="CANVAS_WIDTH"
            :height="CANVAS_HEIGHT"
            class="og-canvas"
        />
    </div>
</template>

<style scoped>
.og-canvas-container {
    width: 100%;
    background-color: #f3f4f6;
    border-radius: 0.5rem;
    overflow: hidden;
}

.og-canvas {
    width: 100%;
    height: auto;
    display: block;
}
</style>
