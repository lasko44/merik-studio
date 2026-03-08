<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BlockEditor from '@/Components/PageBuilder/BlockEditor.vue';
import SectionEditor from '@/Components/PageBuilder/SectionEditor.vue';
import SidebarBlockEditor from '@/Components/PageBuilder/SidebarBlockEditor.vue';
import FormSection from '@/Components/Admin/FormSection.vue';
import SlugInput from '@/Components/Admin/SlugInput.vue';
import TemplateSelector from '@/Components/Admin/TemplateSelector.vue';
import TemplateOptionsPanel from '@/Components/Admin/TemplateOptionsPanel.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import type { PageComponent, ContentBlock, TemplateConfig, TemplateOptions, Section, SectionBranding, SectionedContent } from '@/types/admin';

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
    components: Record<string, ComponentCategory>;
    templateConfig: TemplateConfig;
    availablePages: AvailablePage[];
}

const props = defineProps<Props>();

const activeTab = ref<'settings' | 'seo' | 'advanced'>('settings');

const form = useForm({
    title: '',
    slug: '',
    path: '',
    meta_description: '',
    meta_keywords: '',
    og_title: '',
    og_description: '',
    og_image: '',
    twitter_card_type: 'summary_large_image' as const,
    canonical_url: '',
    no_index: false,
    no_follow: false,
    schema_type: 'WebPage',
    faqs: [] as { question: string; answer: string }[],
    speakable_selectors: [] as string[],
    priority: 0.5,
    change_frequency: 'weekly',
    content: [] as ContentBlock[] | SectionedContent,
    sidebar_content: [] as ContentBlock[],
    template: 'default',
    template_options: {} as TemplateOptions,
    is_published: false,
    is_admin_page: false,
    sort_order: 0,
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

// Check if content is sectioned
const isSectionedContent = (content: ContentBlock[] | SectionedContent): content is SectionedContent => {
    return content !== null && typeof content === 'object' && 'sections' in content;
};

// Get sections from content (for v-model binding)
const sections = computed({
    get: (): Section[] => {
        if (isSectionedContent(form.content)) {
            return form.content.sections;
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

const selectedTemplate = computed(() => {
    return props.templateConfig.templates[form.template] || null;
});

const hasSidebar = computed(() => {
    return selectedTemplate.value?.sections?.includes('sidebar') ?? false;
});

watch(() => form.title, (newTitle) => {
    if (!form.slug || form.slug === slugify(form.title.slice(0, -1))) {
        form.slug = slugify(newTitle);
        form.path = '/' + form.slug;
    }
});

// Watch for template changes and initialize sections
watch(() => form.template, (newTemplate, oldTemplate) => {
    if (newTemplate !== oldTemplate) {
        const template = props.templateConfig.templates[newTemplate];
        if (template?.section_definitions && Object.keys(template.section_definitions).length > 0) {
            // Initialize with sections
            form.content = { sections: initializeSections(newTemplate) };
        } else {
            // Reset to flat blocks
            form.content = [];
        }
        // Reset sidebar content when template changes
        form.sidebar_content = [];
    }
}, { immediate: true });

const slugify = (text: string): string => {
    return text
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
};

const submit = () => {
    form.post(route('admin.pages.store'));
};

const addFaq = () => {
    form.faqs.push({ question: '', answer: '' });
};

const removeFaq = (index: number) => {
    form.faqs.splice(index, 1);
};
</script>

<template>
    <Head title="Create Page" />

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
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Create Page
                    </h2>
                </div>
                <div class="flex items-center gap-3">
                    <SecondaryButton @click="form.is_published = false; submit();" :disabled="form.processing">
                        Save as Draft
                    </SecondaryButton>
                    <PrimaryButton @click="form.is_published = true; submit();" :disabled="form.processing">
                        Publish
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="flex gap-8">
                    <!-- Main Content - Block Editor -->
                    <div class="flex-1 min-w-0">
                        <div class="bg-white shadow-sm sm:rounded-lg p-6">
                            <div class="mb-6">
                                <InputLabel for="title" value="Page Title" />
                                <TextInput
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1 block w-full text-lg"
                                    placeholder="Enter page title..."
                                    autofocus
                                />
                                <InputError :message="form.errors.title" class="mt-2" />
                            </div>

                            <div class="border-t border-gray-200 pt-6">
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
                    </div>

                    <!-- Sidebar -->
                    <div class="w-96 flex-shrink-0">
                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden sticky top-6">
                            <!-- Tabs -->
                            <div class="border-b border-gray-200">
                                <nav class="-mb-px flex">
                                    <button
                                        type="button"
                                        @click="activeTab = 'settings'"
                                        :class="[
                                            activeTab === 'settings'
                                                ? 'border-indigo-500 text-gray-700'
                                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                            'flex-1 border-b-2 py-3 px-1 text-center text-sm font-medium'
                                        ]"
                                    >
                                        Settings
                                    </button>
                                    <button
                                        type="button"
                                        @click="activeTab = 'seo'"
                                        :class="[
                                            activeTab === 'seo'
                                                ? 'border-indigo-500 text-gray-700'
                                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                            'flex-1 border-b-2 py-3 px-1 text-center text-sm font-medium'
                                        ]"
                                    >
                                        SEO
                                    </button>
                                    <button
                                        type="button"
                                        @click="activeTab = 'advanced'"
                                        :class="[
                                            activeTab === 'advanced'
                                                ? 'border-indigo-500 text-gray-700'
                                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                            'flex-1 border-b-2 py-3 px-1 text-center text-sm font-medium'
                                        ]"
                                    >
                                        Advanced
                                    </button>
                                </nav>
                            </div>

                            <div class="p-4 max-h-[calc(100vh-250px)] overflow-y-auto">
                                <!-- Settings Tab -->
                                <div v-show="activeTab === 'settings'" class="space-y-6">
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
                                                <InputError :message="form.errors.path" class="mt-2" />
                                            </div>
                                        </div>
                                    </FormSection>

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

                                    <FormSection title="Options">
                                        <div class="space-y-3">
                                            <label class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="form.is_admin_page"
                                                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                                                />
                                                <span class="ml-2 text-sm text-gray-600">Admin page (doesn't count toward limit)</span>
                                            </label>
                                        </div>
                                    </FormSection>
                                </div>

                                <!-- SEO Tab -->
                                <div v-show="activeTab === 'seo'" class="space-y-6">
                                    <FormSection title="Meta Tags">
                                        <div class="space-y-4">
                                            <div>
                                                <InputLabel for="meta_description" value="Meta Description" />
                                                <textarea
                                                    id="meta_description"
                                                    v-model="form.meta_description"
                                                    rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                                    placeholder="Brief description for search engines..."
                                                ></textarea>
                                                <p class="mt-1 text-xs text-gray-500">{{ form.meta_description?.length || 0 }}/160 characters</p>
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
                                                    rows="2"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                                    placeholder="Leave empty to use meta description"
                                                ></textarea>
                                                <InputError :message="form.errors.og_description" class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel for="og_image" value="OG Image URL" />
                                                <TextInput
                                                    id="og_image"
                                                    v-model="form.og_image"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    placeholder="https://..."
                                                />
                                                <InputError :message="form.errors.og_image" class="mt-2" />
                                            </div>
                                        </div>
                                    </FormSection>

                                    <FormSection title="X">
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

                                    <FormSection title="Robots">
                                        <div class="space-y-3">
                                            <label class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="form.no_index"
                                                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                                                />
                                                <span class="ml-2 text-sm text-gray-600">No Index</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="form.no_follow"
                                                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                                                />
                                                <span class="ml-2 text-sm text-gray-600">No Follow</span>
                                            </label>
                                        </div>
                                    </FormSection>
                                </div>

                                <!-- Advanced Tab -->
                                <div v-show="activeTab === 'advanced'" class="space-y-6">
                                    <FormSection title="Schema Type">
                                        <select
                                            v-model="form.schema_type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                        >
                                            <option value="WebPage">Web Page</option>
                                            <option value="Article">Article</option>
                                            <option value="FAQPage">FAQ Page</option>
                                            <option value="AboutPage">About Page</option>
                                            <option value="ContactPage">Contact Page</option>
                                        </select>
                                        <InputError :message="form.errors.schema_type" class="mt-2" />
                                    </FormSection>

                                    <FormSection title="FAQs">
                                        <div class="space-y-4">
                                            <div v-for="(faq, index) in form.faqs" :key="index" class="relative border border-gray-200 rounded-lg p-3">
                                                <button
                                                    type="button"
                                                    @click="removeFaq(index)"
                                                    class="absolute top-2 right-2 text-gray-400 hover:text-red-500"
                                                >
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                                <div class="space-y-2">
                                                    <TextInput
                                                        v-model="faq.question"
                                                        type="text"
                                                        class="block w-full text-sm"
                                                        placeholder="Question"
                                                    />
                                                    <textarea
                                                        v-model="faq.answer"
                                                        rows="2"
                                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 text-sm"
                                                        placeholder="Answer"
                                                    ></textarea>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                @click="addFaq"
                                                class="inline-flex items-center text-sm text-gray-700 hover:text-gray-600"
                                            >
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add FAQ
                                            </button>
                                        </div>
                                    </FormSection>

                                    <FormSection title="Sitemap">
                                        <div class="space-y-4">
                                            <div>
                                                <InputLabel for="priority" value="Priority (0-1)" />
                                                <input
                                                    id="priority"
                                                    v-model.number="form.priority"
                                                    type="range"
                                                    min="0"
                                                    max="1"
                                                    step="0.1"
                                                    class="mt-1 block w-full"
                                                />
                                                <p class="text-sm text-gray-500">{{ form.priority }}</p>
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
                                        <TextInput
                                            v-model="form.canonical_url"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="Leave empty to use current URL"
                                        />
                                        <InputError :message="form.errors.canonical_url" class="mt-2" />
                                    </FormSection>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
