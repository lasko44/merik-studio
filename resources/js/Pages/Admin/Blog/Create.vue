<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BlockEditor from '@/Components/PageBuilder/BlockEditor.vue';
import FormSection from '@/Components/Admin/FormSection.vue';
import SlugInput from '@/Components/Admin/SlugInput.vue';
import ImageUploader from '@/Components/Admin/ImageUploader.vue';
import OgImageCreator from '@/Components/Admin/OgImageCreator/OgImageCreator.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import type { PageComponent, ContentBlock, BlogCategory } from '@/types/admin';

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
    categories: BlogCategory[];
    components: Record<string, ComponentCategory>;
    availablePages?: AvailablePage[];
}

const props = withDefaults(defineProps<Props>(), {
    availablePages: () => [],
});

const activeTab = ref<'content' | 'settings' | 'seo'>('content');
const showOgCreator = ref(false);

const form = useForm({
    title: '',
    slug: '',
    excerpt: '',
    featured_image: '',
    category_id: null as number | null,
    content: [] as ContentBlock[],
    meta_description: '',
    og_title: '',
    og_description: '',
    og_image: '',
    no_index: false,
    is_published: false,
});

watch(() => form.title, (newTitle) => {
    if (!form.slug || form.slug === slugify(form.title.slice(0, -1))) {
        form.slug = slugify(newTitle);
    }
});

const slugify = (text: string): string => {
    return text
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
};

const submit = () => {
    form.post(route('admin.blog.posts.store'));
};

const tabs = [
    { key: 'content', label: 'Content' },
    { key: 'settings', label: 'Settings' },
    { key: 'seo', label: 'SEO' },
] as const;
</script>

<template>
    <Head title="Create Blog Post" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('admin.blog.posts.index')"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                        </svg>
                    </Link>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Create Blog Post
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
                <!-- Post Title -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                    <InputLabel for="title" value="Post Title" />
                    <TextInput
                        id="title"
                        v-model="form.title"
                        type="text"
                        class="mt-1 block w-full text-lg"
                        placeholder="Enter post title..."
                        autofocus
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
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Post Content</h3>
                            <BlockEditor
                                v-model="form.content"
                                :components="components"
                                :available-pages="availablePages"
                            />
                            <InputError :message="form.errors.content" class="mt-2" />
                        </div>

                        <!-- Settings Tab -->
                        <div v-show="activeTab === 'settings'">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <FormSection title="URL Settings">
                                        <SlugInput
                                            v-model="form.slug"
                                            :source="form.title"
                                            label="Slug"
                                            :error="form.errors.slug"
                                        />
                                    </FormSection>

                                    <FormSection title="Category">
                                        <select
                                            v-model="form.category_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                        >
                                            <option :value="null">No category</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.category_id" class="mt-2" />
                                    </FormSection>

                                    <FormSection title="Excerpt">
                                        <textarea
                                            v-model="form.excerpt"
                                            rows="4"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                            placeholder="Brief summary of the post..."
                                        ></textarea>
                                        <p class="mt-1 text-xs text-gray-500">A short summary that appears in post listings and search results.</p>
                                        <InputError :message="form.errors.excerpt" class="mt-2" />
                                    </FormSection>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-6">
                                    <FormSection title="Featured Image">
                                        <ImageUploader v-model="form.featured_image" label="Featured Image" />
                                        <p class="mt-2 text-xs text-gray-500">This image appears at the top of your post and in listings.</p>
                                        <InputError :message="form.errors.featured_image" class="mt-2" />
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
                                        </div>
                                    </FormSection>

                                    <FormSection title="Robots">
                                        <label class="flex items-center">
                                            <input
                                                type="checkbox"
                                                v-model="form.no_index"
                                                class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                                            />
                                            <span class="ml-2 text-sm text-gray-600">No Index</span>
                                            <span class="ml-2 text-xs text-gray-400">(Hide from search engines)</span>
                                        </label>
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
                                                    placeholder="Leave empty to use post title"
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
    </AuthenticatedLayout>
</template>
