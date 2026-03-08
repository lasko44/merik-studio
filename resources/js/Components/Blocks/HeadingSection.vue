<script setup lang="ts">
interface Props {
    text?: string;
    level?: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6';
    alignment?: 'left' | 'center' | 'right';
}

const props = withDefaults(defineProps<Props>(), {
    text: 'Heading',
    level: 'h2',
    alignment: 'left',
});

const alignmentClass = {
    left: 'text-left',
    center: 'text-center',
    right: 'text-right',
}[props.alignment];

const sizeClass = {
    h1: 'text-4xl md:text-5xl',
    h2: 'text-3xl md:text-4xl',
    h3: 'text-2xl md:text-3xl',
    h4: 'text-xl md:text-2xl',
    h5: 'text-lg md:text-xl',
    h6: 'text-base md:text-lg',
}[props.level];
</script>

<template>
    <div class="py-4">
        <component
            :is="level"
            :class="[alignmentClass, sizeClass, 'font-bold text-gray-900']"
        >
            {{ text }}
        </component>
    </div>
</template>
