<script setup lang="ts">
import { computed } from 'vue';
import { getIconPath } from '@/utils/icons';

interface IconValue {
    icon: string;
    showBackground: boolean;
    color: 'primary' | 'secondary' | 'accent';
}

interface Feature {
    id?: string;
    icon: string | IconValue;
    title: string;
    description: string;
}

interface ParsedFeature extends Feature {
    uniqueId: string;
    parsedIcon: IconValue;
}

interface Props {
    title?: string;
    subtitle?: string;
    features: Feature[];
}

const props = withDefaults(defineProps<Props>(), {
    title: '',
    subtitle: '',
});

// Helper to parse icon value - handles both string and object formats
const parseIcon = (icon: string | IconValue): IconValue => {
    if (typeof icon === 'string') {
        return {
            icon: icon,
            showBackground: true,
            color: 'primary',
        };
    }
    return {
        icon: icon.icon || '',
        showBackground: icon.showBackground ?? true,
        color: icon.color || 'primary',
    };
};

// Pre-compute parsed features with unique IDs and parsed icons
const parsedFeatures = computed<ParsedFeature[]>(() =>
    props.features.map((feature, index) => ({
        ...feature,
        uniqueId: feature.id ?? `feature-${feature.title.toLowerCase().replace(/\s+/g, '-')}-${index}`,
        parsedIcon: parseIcon(feature.icon),
    }))
);

// Get CSS class for icon background based on color name
const getIconBgClass = (colorName: 'primary' | 'secondary' | 'accent'): string => {
    return `feature-icon-bg-${colorName}`;
};

// Get CSS class for icon color based on color name
const getIconColorClass = (colorName: 'primary' | 'secondary' | 'accent'): string => {
    return `feature-icon-color-${colorName}`;
};
</script>

<template>
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div v-if="title || subtitle" class="text-center mb-12">
                <h2 v-if="title" class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    {{ title }}
                </h2>
                <p v-if="subtitle" class="text-xl text-gray-600 max-w-2xl mx-auto">
                    {{ subtitle }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div
                    v-for="feature in parsedFeatures"
                    :key="feature.uniqueId"
                    class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow"
                >
                    <div
                        class="w-12 h-12 rounded-lg flex items-center justify-center mb-4"
                        :class="[
                            feature.parsedIcon.showBackground
                                ? getIconBgClass(feature.parsedIcon.color)
                                : 'border-2 border-gray-200'
                        ]"
                        aria-hidden="true"
                    >
                        <svg
                            class="w-6 h-6"
                            :class="[
                                feature.parsedIcon.showBackground
                                    ? 'text-white'
                                    : getIconColorClass(feature.parsedIcon.color)
                            ]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(feature.parsedIcon.icon)" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        {{ feature.title }}
                    </h3>
                    <p class="text-gray-600">
                        {{ feature.description }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.feature-icon-bg-primary {
    background-color: var(--primary-color);
}

.feature-icon-bg-secondary {
    background-color: var(--secondary-color);
}

.feature-icon-bg-accent {
    background-color: var(--accent-color);
}

.feature-icon-color-primary {
    color: var(--primary-color);
}

.feature-icon-color-secondary {
    color: var(--secondary-color);
}

.feature-icon-color-accent {
    color: var(--accent-color);
}
</style>
