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
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { PageComponent, ContentBlock, BlogPost, BlogCategory } from '@/types/admin';

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
    post: BlogPost;
    categories: BlogCategory[];
    components: Record<string, ComponentCategory>;
    availablePages?: AvailablePage[];
}

const props = withDefaults(defineProps<Props>(), {
    availablePages: () => [],
});

const activeTab = ref<'content' | 'settings' | 'seo'>('content');
const showOgCreator = ref(false);
const showDeleteModal = ref(false);
const isDeleting = ref(false);

const form = useForm({
    title: props.post.title,
    slug: props.post.slug,
    excerpt: props.post.excerpt || '',
    featured_image: props.post.featured_image || '',
    category_id: props.post.category_id,
    content: (props.post.draft_content ?? props.post.content) as ContentBlock[] || [],
    meta_description: props.post.meta_description || '',
    og_title: props.post.og_title || '',
    og_description: props.post.og_description || '',
    og_image: props.post.og_image || '',
    no_index: props.post.no_index || false,
    is_published: props.post.is_published,
    publish: false,
});

const saveDraft = () => {
    form.publish = false;
    form.patch(route('admin.blog.posts.update', props.post.id));
};

const saveAndPublish = () => {
    form.publish = true;
    form.is_published = true;
    form.patch(route('admin.blog.posts.update', props.post.id));
};

const unpublishPost = () => {
    router.post(route('admin.blog.posts.unpublish', props.post.id), {}, {
        preserveScroll: true,
    });
};

const deletePost = () => {
    isDeleting.value = true;
    router.delete(route('admin.blog.posts.destroy', props.post.id), {
        onFinish: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
        },
    });
};

const tabs = [
    { key: 'content', label: 'Content' },
    { key: 'settings', label: 'Settings' },
    { key: 'seo', label: 'SEO' },
] as const;
</script>

<template>
    <Head :title="`Edit: ${post.title}`" />

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
                    <div>
                        <h2 class="text-xl font-semibold leading-tight text-gray-800">
                            Edit Post
                        </h2>
                        <p class="text-sm text-gray-500">/blog/{{ post.slug }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a
                        :href="`/blog/${post.slug}`"
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
                        v-if="post.is_published"
                        @click="unpublishPost"
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

                <!-- Post Info -->
                <div class="mb-6 flex items-center gap-4 text-sm text-gray-500">
                    <span v-if="post.creator">Created by {{ post.creator.name }}</span>
                    <span v-if="post.updater">Last updated by {{ post.updater.name }}</span>
                    <span
                        :class="[
                            post.is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium'
                        ]"
                    >
                        {{ post.is_published ? 'Published' : 'Draft' }}
                    </span>
                    <span
                        v-if="post.has_unpublished_changes"
                        class="inline-flex items-center rounded-full bg-orange-100 text-orange-800 px-2.5 py-0.5 text-xs font-medium"
                    >
                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        Unpublished Changes
                    </span>
                </div>

                <!-- Post Title -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                    <InputLabel for="title" value="Post Title" />
                    <TextInput
                        id="title"
                        v-model="form.title"
                        type="text"
                        class="mt-1 block w-full text-lg"
                        placeholder="Enter post title..."
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

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" max-width="md" @close="showDeleteModal = false">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-medium text-center text-gray-900">Delete Post</h3>
                <p class="mt-2 text-sm text-center text-gray-500">
                    Are you sure you want to delete "{{ post.title }}"? This action cannot be undone.
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancel
                    </SecondaryButton>
                    <DangerButton @click="deletePost" :disabled="isDeleting">
                        {{ isDeleting ? 'Deleting...' : 'Delete Post' }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
