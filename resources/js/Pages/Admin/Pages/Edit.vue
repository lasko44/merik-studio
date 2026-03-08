<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BlockEditor from '@/Components/PageBuilder/BlockEditor.vue';
import SectionEditor from '@/Components/PageBuilder/SectionEditor.vue';
import SidebarBlockEditor from '@/Components/PageBuilder/SidebarBlockEditor.vue';
import FormSection from '@/Components/Admin/FormSection.vue';
import SlugInput from '@/Components/Admin/SlugInput.vue';
import TemplateSelector from '@/Components/Admin/TemplateSelector.vue';
import TemplateOptionsPanel from '@/Components/Admin/TemplateOptionsPanel.vue';
import ImageUploader from '@/Components/Admin/ImageUploader.vue';
import OgImageCreator from '@/Components/Admin/OgImageCreator/OgImageCreator.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import type { AdminPage, PageComponent, ContentBlock, TemplateConfig, TemplateOptions, Section, SectionBranding, SectionedContent } from '@/types/admin';

interface ComponentCategory {
    label: string;
    components: PageComponent[];
}

interface AvailablePage {
    id: number;
    title: string;
    path: string;
}

interface Props {
    page: AdminPage;
    components: Record<string, ComponentCategory>;
    templateConfig: TemplateConfig;
    availablePages: AvailablePage[];
}

const props = defineProps<Props>();

const activeTab = ref<'content' | 'settings' | 'seo' | 'advanced'>('content');
const showOgCreator = ref(false);
const showDeleteModal = ref(false);
const isDeleting = ref(false);

// Helper to get default branding
const getDefaultBranding = (): SectionBranding => ({
    backgroundColor: null,
    backgroundImage: null,
    backgroundOverlay: 0,
    textColor: null,
    paddingTop: 'md',
    paddingBottom: 'md',
    borderTop: false,
    borderBottom: false,
    shadow: 'none',
    rounded: 'none',
    maxWidth: 'max-w-7xl',
});

// Check if content is sectioned
const isSectionedContent = (content: unknown): content is SectionedContent => {
    return content !== null && typeof content === 'object' && 'sections' in (content as object);
};

// Normalize a block to use 'data' instead of 'props' (handles both formats)
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const normalizeBlock = (block: any): ContentBlock => {
    const data = (block.data || block.props || {}) as Record<string, unknown>;
    return {
        id: block.id || `block_${Math.random().toString(36).substring(2, 11)}`,
        type: block.type,
        data,
    };
};

// Get initial content (handling both formats and normalizing props to data)
const getInitialContent = (): ContentBlock[] | SectionedContent => {
    const rawContent = props.page.draft_content ?? props.page.content;
    if (isSectionedContent(rawContent)) {
        // Normalize blocks within sections
        return {
            ...rawContent,
            sections: rawContent.sections.map(section => ({
                ...section,
                // eslint-disable-next-line @typescript-eslint/no-explicit-any
                blocks: section.blocks.map((block: any) => normalizeBlock(block)),
            })),
        };
    }
    // Normalize flat blocks
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const blocks = (rawContent || []) as any[];
    return blocks.map(block => normalizeBlock(block));
};

const form = useForm({
    title: props.page.title,
    slug: props.page.slug,
    path: props.page.path,
    meta_description: props.page.meta_description || '',
    meta_keywords: props.page.meta_keywords || '',
    og_title: props.page.og_title || '',
    og_description: props.page.og_description || '',
    og_image: props.page.og_image || '',
    twitter_card_type: props.page.twitter_card_type || 'summary_large_image' as const,
    canonical_url: props.page.canonical_url || '',
    no_index: props.page.no_index || false,
    no_follow: props.page.no_follow || false,
    schema_type: props.page.schema_type || 'WebPage',
    faqs: (props.page.faqs || []) as { question: string; answer: string }[],
    speakable_selectors: (props.page.speakable_selectors || []) as string[],
    priority: props.page.priority || 0.5,
    change_frequency: props.page.change_frequency || 'weekly',
    content: getInitialContent(),
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    sidebar_content: ((props.page.draft_sidebar_content ?? props.page.sidebar_content) || []).map((block: any) => normalizeBlock(block)) as ContentBlock[],
    template: props.page.template || 'default',
    template_options: (props.page.template_options || {}) as TemplateOptions,
    is_published: props.page.is_published,
    show_in_nav: props.page.show_in_nav ?? true,
    sort_order: props.page.sort_order || 0,
    publish: false,
});

// Check if the current template has section definitions
const hasSectionDefinitions = computed(() => {
    const template = props.templateConfig.templates[form.template];
    return template?.section_definitions && Object.keys(template.section_definitions).length > 0;
});

// Get section definitions for the current template
const sectionDefinitions = computed(() => {
    return props.templateConfig.templates[form.template]?.section_definitions || {};
});

// Generate unique block ID
const generateBlockId = (): string => {
    return 'block_' + Math.random().toString(36).substring(2, 11);
};

// Initialize sections for a template
const initializeSections = (templateSlug: string): Section[] => {
    const template = props.templateConfig.templates[templateSlug];
    if (!template?.section_definitions) return [];

    const sections: Section[] = [];
    for (const [slug, definition] of Object.entries(template.section_definitions)) {
        const defaultBranding = getDefaultBranding();
        const customBranding = definition.defaultBranding || {};

        // Initialize blocks from defaultBlocks if present
        const blocks: ContentBlock[] = [];
        const defaultBlocks = definition.defaultBlocks || [];
        for (const block of defaultBlocks) {
            blocks.push({
                id: generateBlockId(),
                type: block.type,
                data: { ...block.data },
            });
        }

        sections.push({
            id: 'section_' + Math.random().toString(36).substring(2, 11),
            slug,
            label: definition.label,
            blocks,
            branding: { ...defaultBranding, ...customBranding } as SectionBranding,
            isRequired: definition.required,
        });
    }
    return sections;
};

// Migrate flat blocks to sections for templates that require sections
const migrateFlatToSections = (flatBlocks: ContentBlock[], templateSlug: string): Section[] => {
    const template = props.templateConfig.templates[templateSlug];
    if (!template?.section_definitions) return [];

    const sections: Section[] = [];
    const remainingBlocks = [...flatBlocks];

    // Create a section for each definition
    for (const [slug, definition] of Object.entries(template.section_definitions)) {
        const defaultBranding = getDefaultBranding();
        const customBranding = (definition as { defaultBranding?: Partial<SectionBranding> }).defaultBranding || {};

        // Try to match blocks to this section based on type
        const sectionBlocks: ContentBlock[] = [];

        // Match blocks by type (e.g., hero blocks go to hero section)
        for (let i = remainingBlocks.length - 1; i >= 0; i--) {
            const block = remainingBlocks[i];
            if (block.type === slug || (slug === 'content' && !['hero', 'cta'].includes(block.type))) {
                sectionBlocks.unshift(block);
                remainingBlocks.splice(i, 1);
            } else if (slug === 'hero' && block.type === 'hero') {
                sectionBlocks.unshift(block);
                remainingBlocks.splice(i, 1);
            } else if (slug === 'cta' && (block.type === 'cta' || block.type === 'call-to-action')) {
                sectionBlocks.unshift(block);
                remainingBlocks.splice(i, 1);
            }
        }

        sections.push({
            id: 'section_' + Math.random().toString(36).substring(2, 11),
            slug,
            label: (definition as { label: string }).label,
            blocks: sectionBlocks,
            branding: { ...defaultBranding, ...customBranding } as SectionBranding,
            isRequired: (definition as { required?: boolean }).required ?? false,
        });
    }

    // Put any remaining blocks in the 'content' section
    const contentSection = sections.find(s => s.slug === 'content');
    if (contentSection && remainingBlocks.length > 0) {
        contentSection.blocks.push(...remainingBlocks);
    }

    return sections;
};

// Get sections from content (for v-model binding)
const sections = computed({
    get: (): Section[] => {
        if (isSectionedContent(form.content)) {
            return form.content.sections;
        }
        // If we have flat blocks and a section-based template, migrate them
        if (Array.isArray(form.content) && form.content.length > 0 && hasSectionDefinitions.value) {
            const migrated = migrateFlatToSections(form.content, form.template);
            // Update form.content to sectioned format
            form.content = { sections: migrated };
            return migrated;
        }
        // If no content but section definitions exist, initialize empty sections
        if (hasSectionDefinitions.value) {
            const initialized = initializeSections(form.template);
            form.content = { sections: initialized };
            return initialized;
        }
        return [];
    },
    set: (value: Section[]) => {
        form.content = { sections: value };
    },
});

// Get flat blocks from content (for v-model binding)
const flatBlocks = computed({
    get: (): ContentBlock[] => {
        if (Array.isArray(form.content)) {
            return form.content;
        }
        return [];
    },
    set: (value: ContentBlock[]) => {
        form.content = value;
    },
});

// Watch for template changes and migrate content format if needed
watch(() => form.template, (newTemplate, oldTemplate) => {
    if (newTemplate !== oldTemplate) {
        const template = props.templateConfig.templates[newTemplate];
        const hasDefinitions = template?.section_definitions && Object.keys(template.section_definitions).length > 0;

        if (hasDefinitions && !isSectionedContent(form.content)) {
            // Migrating from flat to sectioned - initialize sections
            form.content = { sections: initializeSections(newTemplate) };
        } else if (!hasDefinitions && isSectionedContent(form.content)) {
            // Migrating from sectioned to flat - extract all blocks
            const allBlocks: ContentBlock[] = [];
            for (const section of form.content.sections) {
                allBlocks.push(...section.blocks);
            }
            form.content = allBlocks;
        }
    }
});

const selectedTemplate = computed(() => {
    return props.templateConfig.templates[form.template] || null;
});

const hasSidebar = computed(() => {
    return selectedTemplate.value?.sections?.includes('sidebar') ?? false;
});

const saveDraft = () => {
    form.publish = false;
    form.patch(route('admin.pages.update', props.page.id));
};

const saveAndPublish = () => {
    form.publish = true;
    form.is_published = true;
    form.patch(route('admin.pages.update', props.page.id));
};

const unpublishPage = () => {
    router.post(route('admin.pages.unpublish', props.page.id), {}, {
        preserveScroll: true,
    });
};

const deletePage = () => {
    isDeleting.value = true;
    router.delete(route('admin.pages.destroy', props.page.id), {
        onFinish: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
        },
    });
};

const addFaq = () => {
    form.faqs.push({ question: '', answer: '' });
};

const removeFaq = (index: number) => {
    form.faqs.splice(index, 1);
};

const tabs = [
    { key: 'content', label: 'Content', icon: 'document' },
    { key: 'settings', label: 'Settings', icon: 'cog' },
    { key: 'seo', label: 'SEO', icon: 'search' },
    { key: 'advanced', label: 'Advanced', icon: 'adjustments' },
] as const;
</script>

<template>
    <Head :title="`Edit: ${page.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('admin.pages.index')"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-xl font-semibold leading-tight text-gray-800">
                            Edit Page
                        </h2>
                        <p class="text-sm text-gray-500">{{ page.path }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a
                        :href="page.path"
                        target="_blank"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
                    >
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Preview
                    </a>
                    <button
                        type="button"
                        @click="showDeleteModal = true"
                        class="inline-flex items-center text-sm text-red-600 hover:text-red-800"
                    >
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                    <button
                        v-if="page.is_published"
                        @click="unpublishPage"
                        class="inline-flex items-center rounded-md bg-yellow-100 px-3 py-2 text-sm font-semibold text-yellow-800 hover:bg-yellow-200"
                    >
                        Unpublish
                    </button>
                    <SecondaryButton @click="saveDraft" :disabled="form.processing">
                        Save Draft
                    </SecondaryButton>
                    <PrimaryButton @click="saveAndPublish" :disabled="form.processing">
                        Save & Publish
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-lg bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="ml-3 text-sm text-green-700">{{ $page.props.flash.success }}</p>
                    </div>
                </div>

                <!-- Page Info -->
                <div class="mb-6 flex items-center gap-4 text-sm text-gray-500">
                    <span v-if="page.creator">Created by {{ page.creator.name }}</span>
                    <span v-if="page.updater">Last updated by {{ page.updater.name }}</span>
                    <span
                        :class="[
                            page.is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium'
                        ]"
                    >
                        {{ page.is_published ? 'Published' : 'Draft' }}
                    </span>
                    <span
                        v-if="page.has_unpublished_changes"
                        class="inline-flex items-center rounded-full bg-orange-100 text-orange-800 px-2.5 py-0.5 text-xs font-medium"
                    >
                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        Unpublished Changes
                    </span>
                </div>

                <!-- Page Title -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                    <InputLabel for="title" value="Page Title" />
                    <TextInput
                        id="title"
                        v-model="form.title"
                        type="text"
                        class="mt-1 block w-full text-lg"
                        placeholder="Enter page title..."
                    />
                    <InputError :message="form.errors.title" class="mt-2" />
                </div>

                <!-- Tabs -->
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button
                                v-for="tab in tabs"
                                :key="tab.key"
                                type="button"
                                @click="activeTab = tab.key"
                                :class="[
                                    activeTab === tab.key
                                        ? 'border-indigo-500 text-gray-700 bg-indigo-50'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50',
                                    'flex-1 border-b-2 py-4 px-4 text-center text-sm font-medium transition-colors'
                                ]"
                            >
                                {{ tab.label }}
                            </button>
                        </nav>
                    </div>

                    <div class="p-6">
                        <!-- Content Tab -->
                        <div v-show="activeTab === 'content'">
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Page Content</h3>

                                <!-- Section-based editor -->
                                <SectionEditor
                                    v-if="hasSectionDefinitions"
                                    v-model="sections"
                                    :components="components"
                                    :section-definitions="sectionDefinitions"
                                    :available-pages="availablePages"
                                />

                                <!-- Flat block editor (backward compatible) -->
                                <BlockEditor
                                    v-else
                                    v-model="flatBlocks"
                                    :components="components"
                                    :available-pages="availablePages"
                                />
                                <InputError :message="form.errors.content" class="mt-2" />
                            </div>

                            <!-- Sidebar Content Editor (only for non-sectioned templates with sidebar) -->
                            <div v-if="hasSidebar && !hasSectionDefinitions" class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Sidebar Widgets</h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    Add widgets that will appear in the sidebar alongside your main content.
                                </p>
                                <SidebarBlockEditor
                                    v-model="form.sidebar_content"
                                    :components="components"
                                    :available-pages="availablePages"
                                />
                                <InputError :message="form.errors.sidebar_content" class="mt-2" />
                            </div>
                        </div>

                        <!-- Settings Tab -->
                        <div v-show="activeTab === 'settings'">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <FormSection title="URL Settings">
                                        <div class="space-y-4">
                                            <SlugInput
                                                v-model="form.slug"
                                                :source="form.title"
                                                label="Slug"
                                                :error="form.errors.slug"
                                            />

                                            <div>
                                                <InputLabel for="path" value="Path" />
                                                <TextInput
                                                    id="path"
                                                    v-model="form.path"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    placeholder="/about-us"
                                                />
                                                <p class="mt-1 text-xs text-gray-500">The full URL path for this page</p>
                                                <InputError :message="form.errors.path" class="mt-2" />
                                            </div>
                                        </div>
                                    </FormSection>

                                    <FormSection title="Navigation">
                                        <div class="space-y-4">
                                            <label class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="form.show_in_nav"
                                                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                                                />
                                                <span class="ml-2 text-sm text-gray-600">Show in navigation menu</span>
                                            </label>

                                            <div>
                                                <InputLabel for="sort_order" value="Sort Order" />
                                                <input
                                                    id="sort_order"
                                                    v-model.number="form.sort_order"
                                                    type="number"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700"
                                                    min="0"
                                                />
                                                <p class="mt-1 text-xs text-gray-500">Lower numbers appear first in navigation</p>
                                                <InputError :message="form.errors.sort_order" class="mt-2" />
                                            </div>

                                            <p class="text-xs text-gray-500">
                                                Use the <a :href="route('admin.navigation.edit')" class="text-gray-700 hover:underline">Navigation Editor</a> to reorder and nest pages.
                                            </p>
                                        </div>
                                    </FormSection>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-6">
                                    <FormSection title="Template">
                                        <TemplateSelector
                                            v-model="form.template"
                                            :config="templateConfig"
                                        />
                                        <InputError :message="form.errors.template" class="mt-2" />
                                    </FormSection>

                                    <FormSection v-if="selectedTemplate && Object.keys(selectedTemplate.options || {}).length > 0" title="Template Options">
                                        <TemplateOptionsPanel
                                            v-model="form.template_options"
                                            :template="selectedTemplate"
                                        />
                                    </FormSection>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Tab -->
                        <div v-show="activeTab === 'seo'">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <FormSection title="Meta Tags">
                                        <div class="space-y-4">
                                            <div>
                                                <InputLabel for="meta_description" value="Meta Description" />
                                                <textarea
                                                    id="meta_description"
                                                    v-model="form.meta_description"
                                                    rows="4"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                                    placeholder="Brief description for search engines..."
                                                ></textarea>
                                                <p class="mt-1 text-xs text-gray-500">{{ form.meta_description?.length || 0 }}/160 characters recommended</p>
                                                <InputError :message="form.errors.meta_description" class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel for="meta_keywords" value="Meta Keywords" />
                                                <TextInput
                                                    id="meta_keywords"
                                                    v-model="form.meta_keywords"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    placeholder="keyword1, keyword2, keyword3"
                                                />
                                                <InputError :message="form.errors.meta_keywords" class="mt-2" />
                                            </div>
                                        </div>
                                    </FormSection>

                                    <FormSection title="Robots">
                                        <div class="space-y-3">
                                            <label class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="form.no_index"
                                                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                                                />
                                                <span class="ml-2 text-sm text-gray-600">No Index</span>
                                                <span class="ml-2 text-xs text-gray-400">(Hide from search engines)</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="form.no_follow"
                                                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                                                />
                                                <span class="ml-2 text-sm text-gray-600">No Follow</span>
                                                <span class="ml-2 text-xs text-gray-400">(Don't follow links on this page)</span>
                                            </label>
                                        </div>
                                    </FormSection>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-6">
                                    <FormSection title="Open Graph">
                                        <div class="space-y-4">
                                            <div>
                                                <InputLabel for="og_title" value="OG Title" />
                                                <TextInput
                                                    id="og_title"
                                                    v-model="form.og_title"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    placeholder="Leave empty to use page title"
                                                />
                                                <InputError :message="form.errors.og_title" class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel for="og_description" value="OG Description" />
                                                <textarea
                                                    id="og_description"
                                                    v-model="form.og_description"
                                                    rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                                    placeholder="Leave empty to use meta description"
                                                ></textarea>
                                                <InputError :message="form.errors.og_description" class="mt-2" />
                                            </div>

                                            <div>
                                                <div class="flex items-center justify-between">
                                                    <InputLabel for="og_image" value="OG Image" />
                                                    <button
                                                        type="button"
                                                        @click="showOgCreator = true"
                                                        class="text-sm text-gray-700 hover:text-indigo-800"
                                                    >
                                                        Create with Editor
                                                    </button>
                                                </div>
                                                <div class="mt-1">
                                                    <ImageUploader v-model="form.og_image" label="OG Image" />
                                                </div>
                                                <InputError :message="form.errors.og_image" class="mt-2" />
                                            </div>
                                        </div>
                                    </FormSection>

                                    <FormSection title="X (Twitter)">
                                        <div>
                                            <InputLabel for="twitter_card_type" value="Card Type" />
                                            <select
                                                id="twitter_card_type"
                                                v-model="form.twitter_card_type"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                            >
                                                <option value="summary">Summary</option>
                                                <option value="summary_large_image">Summary with Large Image</option>
                                            </select>
                                            <InputError :message="form.errors.twitter_card_type" class="mt-2" />
                                        </div>
                                    </FormSection>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Tab -->
                        <div v-show="activeTab === 'advanced'">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <FormSection title="Schema Type">
                                        <div>
                                            <select
                                                v-model="form.schema_type"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                            >
                                                <option value="WebPage">Web Page</option>
                                                <option value="Article">Article</option>
                                                <option value="FAQPage">FAQ Page</option>
                                                <option value="AboutPage">About Page</option>
                                                <option value="ContactPage">Contact Page</option>
                                            </select>
                                            <p class="mt-1 text-xs text-gray-500">Structured data type for search engines</p>
                                            <InputError :message="form.errors.schema_type" class="mt-2" />
                                        </div>
                                    </FormSection>

                                    <FormSection title="Sitemap">
                                        <div class="space-y-4">
                                            <div>
                                                <InputLabel for="priority" value="Priority" />
                                                <div class="flex items-center gap-4 mt-1">
                                                    <input
                                                        id="priority"
                                                        v-model.number="form.priority"
                                                        type="range"
                                                        min="0"
                                                        max="1"
                                                        step="0.1"
                                                        class="flex-1"
                                                    />
                                                    <span class="text-sm font-medium text-gray-700 w-10">{{ form.priority }}</span>
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">0 = lowest, 1 = highest importance</p>
                                                <InputError :message="form.errors.priority" class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel for="change_frequency" value="Change Frequency" />
                                                <select
                                                    id="change_frequency"
                                                    v-model="form.change_frequency"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                                >
                                                    <option value="always">Always</option>
                                                    <option value="hourly">Hourly</option>
                                                    <option value="daily">Daily</option>
                                                    <option value="weekly">Weekly</option>
                                                    <option value="monthly">Monthly</option>
                                                    <option value="yearly">Yearly</option>
                                                    <option value="never">Never</option>
                                                </select>
                                                <InputError :message="form.errors.change_frequency" class="mt-2" />
                                            </div>
                                        </div>
                                    </FormSection>

                                    <FormSection title="Canonical URL">
                                        <div>
                                            <TextInput
                                                v-model="form.canonical_url"
                                                type="text"
                                                class="block w-full"
                                                placeholder="Leave empty to use current URL"
                                            />
                                            <p class="mt-1 text-xs text-gray-500">Override the canonical URL for this page</p>
                                            <InputError :message="form.errors.canonical_url" class="mt-2" />
                                        </div>
                                    </FormSection>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-6">
                                    <FormSection title="FAQs" description="Add frequently asked questions for FAQ schema markup">
                                        <div class="space-y-4">
                                            <div v-for="(faq, index) in form.faqs" :key="index" class="relative border border-gray-200 rounded-lg p-4">
                                                <button
                                                    type="button"
                                                    @click="removeFaq(index)"
                                                    class="absolute top-3 right-3 text-gray-400 hover:text-red-500"
                                                >
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                                <div class="space-y-3 pr-8">
                                                    <div>
                                                        <InputLabel :for="`faq-q-${index}`" value="Question" />
                                                        <TextInput
                                                            :id="`faq-q-${index}`"
                                                            v-model="faq.question"
                                                            type="text"
                                                            class="mt-1 block w-full"
                                                            placeholder="What is your question?"
                                                        />
                                                    </div>
                                                    <div>
                                                        <InputLabel :for="`faq-a-${index}`" value="Answer" />
                                                        <textarea
                                                            :id="`faq-a-${index}`"
                                                            v-model="faq.answer"
                                                            rows="3"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                                            placeholder="Provide the answer..."
                                                        ></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                @click="addFaq"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                            >
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add FAQ
                                            </button>
                                        </div>
                                    </FormSection>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <OgImageCreator
            :show="showOgCreator"
            :initial-title="form.og_title || form.title"
            @close="showOgCreator = false"
            @created="(url) => { form.og_image = url; showOgCreator = false; }"
        />

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" max-width="md" @close="showDeleteModal = false">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-medium text-center text-gray-900">Delete Page</h3>
                <p class="mt-2 text-sm text-center text-gray-500">
                    Are you sure you want to delete "{{ page.title }}"? This action cannot be undone.
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancel
                    </SecondaryButton>
                    <DangerButton @click="deletePage" :disabled="isDeleting">
                        {{ isDeleting ? 'Deleting...' : 'Delete Page' }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
