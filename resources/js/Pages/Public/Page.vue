<script setup lang="ts">
import { computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SectionRenderer from '@/Components/Public/SectionRenderer.vue';
import HeroSection from '@/Components/Blocks/HeroSection.vue';
import FeaturesSection from '@/Components/Blocks/FeaturesSection.vue';
import TextSection from '@/Components/Blocks/TextSection.vue';
import CtaSection from '@/Components/Blocks/CtaSection.vue';
import ContactSection from '@/Components/Blocks/ContactSection.vue';
import PricingSection from '@/Components/Blocks/PricingSection.vue';
import ImageSection from '@/Components/Blocks/ImageSection.vue';
import TwoColumnSection from '@/Components/Blocks/TwoColumnSection.vue';
import HeadingSection from '@/Components/Blocks/HeadingSection.vue';
import TestimonialsSection from '@/Components/Blocks/TestimonialsSection.vue';
import SidebarCard from '@/Components/Blocks/SidebarCard.vue';
import SidebarContact from '@/Components/Blocks/SidebarContact.vue';
import SidebarLinks from '@/Components/Blocks/SidebarLinks.vue';
import SidebarCTA from '@/Components/Blocks/SidebarCTA.vue';
import SidebarImage from '@/Components/Blocks/SidebarImage.vue';
import SpacerSection from '@/Components/Blocks/SpacerSection.vue';
import SidebarSpacer from '@/Components/Blocks/SidebarSpacer.vue';
import PortfolioItem from '@/Components/Blocks/PortfolioItem.vue';
// Commerce components
import ProductCard from '@/Components/Blocks/ProductCard.vue';
import CheckoutButton from '@/Components/Blocks/CheckoutButton.vue';
import StripePricingTable from '@/Components/Blocks/StripePricingTable.vue';
import type { Section, SectionBranding, SectionedContent } from '@/types/admin';
// Template layouts
import DefaultLayout from '@/Components/Templates/DefaultLayout.vue';
import FullWidthLayout from '@/Components/Templates/FullWidthLayout.vue';
import SidebarLayout from '@/Components/Templates/SidebarLayout.vue';
import SidebarLeftLayout from '@/Components/Templates/SidebarLeftLayout.vue';
import LandingLayout from '@/Components/Templates/LandingLayout.vue';
import BlogLayout from '@/Components/Templates/BlogLayout.vue';
import PortfolioLayout from '@/Components/Templates/PortfolioLayout.vue';
import DocumentationLayout from '@/Components/Templates/DocumentationLayout.vue';
import CenteredLayout from '@/Components/Templates/CenteredLayout.vue';
import ProductLayout from '@/Components/Templates/ProductLayout.vue';
import type { SeoData } from '@/types/seo';

interface ContentBlock {
    type: string;
    props?: Record<string, unknown>;
    data?: Record<string, unknown>;
}

// Helper to get block data (supports both 'props' and 'data' keys)
const getBlockData = (block: ContentBlock): Record<string, unknown> => {
    return block.props || block.data || {};
};

type TemplateType = 'default' | 'full-width' | 'sidebar' | 'sidebar-left' | 'landing' | 'blog' | 'portfolio' | 'documentation' | 'centered' | 'product';

interface PageData {
    id: number;
    title: string;
    slug: string;
    path: string;
    content: ContentBlock[] | SectionedContent | null;
    sidebar_content: ContentBlock[] | null;
    template: TemplateType;
    template_options: Record<string, unknown> | null;
}

// Helper to check if content is sectioned
const isSectionedContent = (content: unknown): content is SectionedContent => {
    return content !== null && typeof content === 'object' && 'sections' in (content as object);
};

interface NavItem {
    title: string;
    path: string;
}

interface Settings {
    siteName: string;
    tagline: string | null;
    logoPath: string | null;
    showSiteNameInNav: boolean;
    navLogoHeight: number;
    primaryColor: string;
    secondaryColor: string;
    accentColor: string;
    backgroundColor: string;
    textColor: string;
    successColor: string;
    warningColor: string;
    errorColor: string;
    infoColor: string;
    surfaceColor: string;
    borderColor: string;
    mutedColor: string;
    contactEmail: string | null;
    contactPhone: string | null;
    contactAddress: string | null;
    socialLinks: Array<{ platform: string; url: string }> | null;
}

interface Props {
    page: PageData;
    seo: SeoData;
    settings: Settings;
    navigation: NavItem[];
    canEdit?: boolean;
    editUrl?: string | null;
    isPreview?: boolean;
}

const props = defineProps<Props>();

// Check if content is sectioned
const hasSectionedContent = computed(() => {
    return isSectionedContent(props.page.content);
});

// Get sections if content is sectioned
const sections = computed((): Section[] => {
    if (isSectionedContent(props.page.content)) {
        return props.page.content.sections;
    }
    return [];
});

// Get flat blocks if content is flat
const flatBlocks = computed((): ContentBlock[] => {
    if (Array.isArray(props.page.content)) {
        return props.page.content;
    }
    return [];
});

// Get sidebar section blocks (for sidebar templates with sectioned content)
const sidebarSectionBlocks = computed((): ContentBlock[] => {
    if (isSectionedContent(props.page.content)) {
        const sidebarSection = props.page.content.sections.find(s => s.slug === 'sidebar');
        return sidebarSection?.blocks || [];
    }
    return [];
});

// Check if there's any sidebar content (sectioned or flat)
const hasSidebarContent = computed(() => {
    if (hasSectionedContent.value) {
        return sidebarSectionBlocks.value.length > 0;
    }
    return (props.page.sidebar_content?.length || 0) > 0;
});

const componentMap: Record<string, unknown> = {
    hero: HeroSection,
    features: FeaturesSection,
    text: TextSection,
    cta: CtaSection,
    'call-to-action': CtaSection, // Alias for setup wizard
    contact_info: ContactSection,
    contact_form: ContactSection,
    'contact-section': ContactSection, // Alias for setup wizard
    testimonials: TestimonialsSection,
    pricing: PricingSection,
    image: ImageSection,
    'two-column-layout': TwoColumnSection,
    heading: HeadingSection,
    // Sidebar components
    'sidebar-card': SidebarCard,
    'sidebar-contact': SidebarContact,
    'sidebar-links': SidebarLinks,
    'sidebar-cta': SidebarCTA,
    'sidebar-image': SidebarImage,
    'sidebar-spacer': SidebarSpacer,
    // Layout components
    spacer: SpacerSection,
    // Portfolio components
    'portfolio-item': PortfolioItem,
    // Commerce components
    'product-card': ProductCard,
    'checkout-button': CheckoutButton,
    'stripe-pricing-table': StripePricingTable,
};

const getComponent = (type: string) => {
    return componentMap[type] || null;
};

const template = computed(() => props.page.template || 'default');
const templateOptions = computed(() => props.page.template_options || {});

const layoutMap: Record<TemplateType, unknown> = {
    'default': DefaultLayout,
    'full-width': FullWidthLayout,
    'sidebar': SidebarLayout,
    'sidebar-left': SidebarLeftLayout,
    'landing': LandingLayout,
    'blog': BlogLayout,
    'portfolio': PortfolioLayout,
    'documentation': DocumentationLayout,
    'centered': CenteredLayout,
    'product': ProductLayout,
};

const layoutComponent = computed(() => {
    return layoutMap[template.value as TemplateType] || DefaultLayout;
});

const hasSidebar = computed(() => {
    return ['sidebar', 'sidebar-left'].includes(template.value);
});

const isLanding = computed(() => template.value === 'landing');
const isDocumentation = computed(() => template.value === 'documentation');
</script>

<template>
    <PublicLayout :settings="settings" :navigation="navigation">
        <SeoHead :seo="seo" :title="page.title" />

        <!-- Draft Preview Banner -->
        <div v-if="isPreview" class="bg-amber-500 text-amber-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                <div class="flex items-center justify-center gap-2 text-sm font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Preview Mode - This page is not published yet</span>
                    <a
                        v-if="editUrl"
                        :href="editUrl"
                        class="ml-2 underline hover:no-underline"
                    >
                        Edit &amp; Publish
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar templates (left and right) -->
        <component
            v-if="hasSidebar"
            :is="layoutComponent"
            :options="templateOptions"
            class="page-content"
        >
            <template #content>
                <!-- Sectioned content -->
                <template v-if="hasSectionedContent && sections.length > 0">
                    <SectionRenderer
                        v-for="section in sections.filter(s => s.slug !== 'sidebar')"
                        :key="section.id"
                        :branding="section.branding"
                    >
                        <component
                            v-for="(block, blockIndex) in section.blocks"
                            :key="blockIndex"
                            :is="getComponent(block.type)"
                            v-bind="getBlockData(block)"
                            :settings="settings"
                        />
                    </SectionRenderer>
                </template>

                <!-- Flat content (backward compatible) -->
                <template v-else-if="flatBlocks.length > 0">
                    <component
                        v-for="(block, index) in flatBlocks"
                        :key="index"
                        :is="getComponent(block.type)"
                        v-bind="getBlockData(block)"
                        :settings="settings"
                    />
                </template>

                <div v-else>
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ page.title }}</h1>
                    <p class="text-gray-600">This page has no content yet.</p>
                </div>
            </template>

            <template #sidebar>
                <!-- Sectioned sidebar content -->
                <template v-if="hasSectionedContent && sidebarSectionBlocks.length > 0">
                    <component
                        v-for="(block, blockIndex) in sidebarSectionBlocks"
                        :key="blockIndex"
                        :is="getComponent(block.type)"
                        v-bind="getBlockData(block)"
                        :settings="settings"
                    />
                </template>

                <!-- Legacy flat sidebar content blocks -->
                <template v-else-if="!hasSectionedContent && page.sidebar_content && page.sidebar_content.length > 0">
                    <component
                        v-for="(block, index) in page.sidebar_content"
                        :key="index"
                        :is="getComponent(block.type)"
                        v-bind="getBlockData(block)"
                        :settings="settings"
                    />
                </template>

                <!-- Default sidebar content (when no custom content) -->
                <template v-else>
                    <!-- Contact Info Card -->
                    <div v-if="settings.contactEmail || settings.contactPhone" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Us</h3>
                        <div class="space-y-3 text-sm">
                            <p v-if="settings.contactEmail" class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ settings.contactEmail }}
                            </p>
                            <p v-if="settings.contactPhone" class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ settings.contactPhone }}
                            </p>
                            <p v-if="settings.contactAddress" class="flex items-start gap-2 text-gray-600">
                                <svg class="w-4 h-4 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ settings.contactAddress }}
                            </p>
                        </div>
                    </div>

                    <!-- Navigation Card -->
                    <div v-if="navigation.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
                        <nav class="space-y-2">
                            <a
                                v-for="item in navigation"
                                :key="item.path"
                                :href="item.path"
                                class="block text-sm text-gray-600 hover:text-gray-900 hover:underline"
                            >
                                {{ item.title }}
                            </a>
                        </nav>
                    </div>
                </template>
            </template>
        </component>

        <!-- Landing template -->
        <LandingLayout
            v-else-if="isLanding"
            :options="templateOptions"
            class="page-content"
        >
            <template #hero>
                <!-- Sectioned content - render hero section -->
                <template v-if="hasSectionedContent">
                    <template v-for="section in sections.filter(s => s.slug === 'hero')" :key="section.id">
                        <SectionRenderer :branding="section.branding">
                            <component
                                v-for="(block, blockIndex) in section.blocks"
                                :key="blockIndex"
                                :is="getComponent(block.type)"
                                v-bind="getBlockData(block)"
                                :settings="settings"
                            />
                        </SectionRenderer>
                    </template>
                </template>
                <!-- Flat content - render first hero block if present -->
                <template v-else-if="flatBlocks.length > 0 && flatBlocks[0].type === 'hero'">
                    <component
                        :is="getComponent('hero')"
                        v-bind="getBlockData(flatBlocks[0])"
                        :settings="settings"
                    />
                </template>
            </template>

            <template #content>
                <!-- Sectioned content - render non-hero sections -->
                <template v-if="hasSectionedContent">
                    <SectionRenderer
                        v-for="section in sections.filter(s => s.slug !== 'hero')"
                        :key="section.id"
                        :branding="section.branding"
                    >
                        <component
                            v-for="(block, blockIndex) in section.blocks"
                            :key="blockIndex"
                            :is="getComponent(block.type)"
                            v-bind="getBlockData(block)"
                            :settings="settings"
                        />
                    </SectionRenderer>
                </template>
                <!-- Flat content - render remaining blocks (skip first if it's a hero) -->
                <template v-else-if="flatBlocks.length > 0">
                    <component
                        v-for="(block, index) in flatBlocks.slice(flatBlocks[0]?.type === 'hero' ? 1 : 0)"
                        :key="index"
                        :is="getComponent(block.type)"
                        v-bind="getBlockData(block)"
                        :settings="settings"
                    />
                </template>
            </template>
        </LandingLayout>

        <!-- Documentation template -->
        <DocumentationLayout
            v-else-if="isDocumentation"
            :options="templateOptions"
            class="page-content"
        >
            <template #navigation>
                <nav class="space-y-1">
                    <a
                        v-for="item in navigation"
                        :key="item.path"
                        :href="item.path"
                        class="block px-3 py-2 text-sm rounded-md hover:bg-gray-100"
                        :class="page.path === item.path ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700'"
                    >
                        {{ item.title }}
                    </a>
                </nav>
            </template>

            <template #default>
                <!-- Sectioned content -->
                <template v-if="hasSectionedContent && sections.length > 0">
                    <SectionRenderer
                        v-for="section in sections.filter(s => s.slug === 'content')"
                        :key="section.id"
                        :branding="section.branding"
                    >
                        <component
                            v-for="(block, blockIndex) in section.blocks"
                            :key="blockIndex"
                            :is="getComponent(block.type)"
                            v-bind="getBlockData(block)"
                            :settings="settings"
                        />
                    </SectionRenderer>
                </template>

                <!-- Flat content (backward compatible) -->
                <template v-else-if="flatBlocks.length > 0">
                    <component
                        v-for="(block, index) in flatBlocks"
                        :key="index"
                        :is="getComponent(block.type)"
                        v-bind="getBlockData(block)"
                        :settings="settings"
                    />
                </template>

                <div v-else>
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ page.title }}</h1>
                    <p class="text-gray-600">This page has no content yet.</p>
                </div>
            </template>

            <template #toc>
                <div class="text-sm">
                    <h4 class="font-medium text-gray-900 mb-3">On this page</h4>
                    <p class="text-gray-500 italic">Table of contents</p>
                </div>
            </template>
        </DocumentationLayout>

        <!-- All other templates (default, full-width, blog, portfolio, centered) -->
        <component
            v-else
            :is="layoutComponent"
            :options="templateOptions"
            class="page-content"
        >
            <!-- Sectioned content -->
            <template v-if="hasSectionedContent && sections.length > 0">
                <SectionRenderer
                    v-for="section in sections"
                    :key="section.id"
                    :branding="section.branding"
                >
                    <component
                        v-for="(block, blockIndex) in section.blocks"
                        :key="blockIndex"
                        :is="getComponent(block.type)"
                        v-bind="getBlockData(block)"
                        :settings="settings"
                    />
                </SectionRenderer>
            </template>

            <!-- Flat content (backward compatible) -->
            <template v-else-if="flatBlocks.length > 0">
                <component
                    v-for="(block, index) in flatBlocks"
                    :key="index"
                    :is="getComponent(block.type)"
                    v-bind="getBlockData(block)"
                    :settings="settings"
                />
            </template>

            <div v-else class="py-16">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ page.title }}</h1>
                <p class="text-gray-600">This page has no content yet.</p>
            </div>
        </component>

        <!-- Edit Button (floating, only shown when user can edit) -->
        <a
            v-if="canEdit && editUrl"
            :href="editUrl"
            class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:bg-indigo-500 transition-colors"
            title="Edit this page"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span class="hidden sm:inline">Edit Page</span>
        </a>
    </PublicLayout>
</template>
