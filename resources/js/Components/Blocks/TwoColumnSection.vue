<script setup lang="ts">
import HeroSection from './HeroSection.vue';
import FeaturesSection from './FeaturesSection.vue';
import TextSection from './TextSection.vue';
import CtaSection from './CtaSection.vue';
import ContactSection from './ContactSection.vue';
import PricingSection from './PricingSection.vue';
import ImageSection from './ImageSection.vue';
import HeadingSection from './HeadingSection.vue';

interface ContentBlock {
    id: string;
    type: string;
    data: Record<string, unknown>;
}

interface Props {
    leftWidth?: string;
    rightWidth?: string;
    gap?: string;
    reverseOnMobile?: boolean;
    leftContent?: ContentBlock[];
    rightContent?: ContentBlock[];
}

const props = withDefaults(defineProps<Props>(), {
    leftWidth: '50%',
    rightWidth: '50%',
    gap: '2rem',
    reverseOnMobile: false,
    leftContent: () => [],
    rightContent: () => [],
});

const getComponent = (type: string) => {
    const components: Record<string, unknown> = {
        hero: HeroSection,
        features: FeaturesSection,
        text: TextSection,
        cta: CtaSection,
        contact_info: ContactSection,
        contact_form: ContactSection,
        pricing: PricingSection,
        image: ImageSection,
        heading: HeadingSection,
        // Note: 'two-column-layout' is intentionally excluded to prevent infinite nesting
    };
    return components[type] || null;
};
</script>

<template>
    <div class="py-8">
        <div
            class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-2"
            :class="[reverseOnMobile ? 'flex-col-reverse lg:flex-row' : '']"
            :style="{ gap }"
        >
            <!-- Left column -->
            <div>
                <template v-if="leftContent && leftContent.length > 0">
                    <component
                        v-for="block in leftContent"
                        :key="block.id"
                        :is="getComponent(block.type)"
                        v-bind="block.data"
                    />
                </template>
                <div v-else class="bg-gray-100 rounded-lg p-8 h-full min-h-[200px] flex items-center justify-center text-gray-400">
                    Left column - add content in editor
                </div>
            </div>

            <!-- Right column -->
            <div>
                <template v-if="rightContent && rightContent.length > 0">
                    <component
                        v-for="block in rightContent"
                        :key="block.id"
                        :is="getComponent(block.type)"
                        v-bind="block.data"
                    />
                </template>
                <div v-else class="bg-gray-100 rounded-lg p-8 h-full min-h-[200px] flex items-center justify-center text-gray-400">
                    Right column - add content in editor
                </div>
            </div>
        </div>
    </div>
</template>
