<script setup lang="ts">
import { computed } from 'vue';
import type { SectionBranding } from '@/types/admin';

interface Props {
    branding: SectionBranding;
}

const props = defineProps<Props>();

const paddingClasses = {
    none: 'py-0',
    sm: 'py-4',
    md: 'py-8 md:py-12',
    lg: 'py-12 md:py-16',
    xl: 'py-16 md:py-24',
} as const;

const shadowClasses = {
    none: '',
    sm: 'shadow-sm',
    md: 'shadow-md',
    lg: 'shadow-lg',
} as const;

const roundedClasses = {
    none: 'rounded-none',
    sm: 'rounded',
    md: 'rounded-lg',
    lg: 'rounded-xl',
} as const;

const maxWidthClasses = {
    'full': 'max-w-full',
    'max-w-7xl': 'max-w-7xl',
    'max-w-6xl': 'max-w-6xl',
    'max-w-5xl': 'max-w-5xl',
    'max-w-4xl': 'max-w-4xl',
    'max-w-3xl': 'max-w-3xl',
} as const;

const sectionClasses = computed(() => {
    const classes: string[] = ['relative'];

    // Padding
    const paddingTop = props.branding.paddingTop || 'md';
    const paddingBottom = props.branding.paddingBottom || 'md';

    // Split padding classes for top and bottom
    const topClass = paddingClasses[paddingTop].split(' ').map(c => {
        if (c.startsWith('py-')) return c.replace('py-', 'pt-');
        if (c.startsWith('md:py-')) return c.replace('md:py-', 'md:pt-');
        return c;
    }).join(' ');

    const bottomClass = paddingClasses[paddingBottom].split(' ').map(c => {
        if (c.startsWith('py-')) return c.replace('py-', 'pb-');
        if (c.startsWith('md:py-')) return c.replace('md:py-', 'md:pb-');
        return c;
    }).join(' ');

    classes.push(topClass, bottomClass);

    // Shadow
    if (props.branding.shadow !== 'none') {
        classes.push(shadowClasses[props.branding.shadow]);
    }

    // Rounded
    if (props.branding.rounded !== 'none') {
        classes.push(roundedClasses[props.branding.rounded]);
    }

    // Borders
    if (props.branding.borderTop) {
        classes.push('border-t border-gray-200');
    }
    if (props.branding.borderBottom) {
        classes.push('border-b border-gray-200');
    }

    return classes.join(' ');
});

const containerClasses = computed(() => {
    const maxWidth = props.branding.maxWidth || 'max-w-7xl';
    if (maxWidth === 'full') {
        return 'w-full px-4 sm:px-6 lg:px-8';
    }
    return `${maxWidthClasses[maxWidth]} mx-auto px-4 sm:px-6 lg:px-8`;
});

const sectionStyles = computed(() => {
    const styles: Record<string, string> = {};

    if (props.branding.backgroundColor) {
        styles.backgroundColor = props.branding.backgroundColor;
    }

    if (props.branding.textColor) {
        styles.color = props.branding.textColor;
    }

    return styles;
});

const hasBackgroundImage = computed(() => !!props.branding.backgroundImage);

const backgroundImageStyles = computed(() => {
    if (!props.branding.backgroundImage) return {};

    return {
        backgroundImage: `url(${props.branding.backgroundImage})`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
    };
});

const overlayStyles = computed(() => {
    if (!props.branding.backgroundImage || props.branding.backgroundOverlay === 0) return {};

    return {
        backgroundColor: `rgba(0, 0, 0, ${props.branding.backgroundOverlay / 100})`,
    };
});
</script>

<template>
    <section
        :class="sectionClasses"
        :style="{ ...sectionStyles, ...(hasBackgroundImage ? backgroundImageStyles : {}) }"
    >
        <!-- Background overlay for images -->
        <div
            v-if="hasBackgroundImage && branding.backgroundOverlay > 0"
            class="absolute inset-0"
            :style="overlayStyles"
        />

        <!-- Content container -->
        <div :class="containerClasses" class="relative z-10">
            <slot />
        </div>
    </section>
</template>
