<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { BlogPost } from '@/types/admin';

interface BlogSettings {
    showInNav: boolean;
    navLabel: string;
    enableSearch: boolean;
    enableCategoryFilter: boolean;
}

interface Props {
    posts: BlogPost[];
    blogSettings: BlogSettings;
}

const props = defineProps<Props>();

const confirmingDeletion = ref<number | null>(null);
const processing = ref(false);
const showSettings = ref(false);

const settingsForm = useForm({
    showInNav: props.blogSettings.showInNav,
    navLabel: props.blogSettings.navLabel,
    enableSearch: props.blogSettings.enableSearch,
    enableCategoryFilter: props.blogSettings.enableCategoryFilter,
});

const saveSettings = () => {
    settingsForm.patch(route('admin.blog.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            showSettings.value = false;
        },
    });
};

const publishPost = (postId: number) => {
    router.post(route('admin.blog.posts.publish', postId), {}, {
        preserveScroll: true,
    });
};

const unpublishPost = (postId: number) => {
    router.post(route('admin.blog.posts.unpublish', postId), {}, {
        preserveScroll: true,
    });
};

const deletePost = (postId: number) => {
    processing.value = true;
    router.delete(route('admin.blog.posts.destroy', postId), {
        preserveScroll: true,
        onFinish: () => {
            processing.value = false;
            confirmingDeletion.value = null;
        },
    });
};

const getStatusBadgeClass = (post: BlogPost): string => {
    return post.is_published
        ? 'bg-green-100 text-green-800'
        : 'bg-yellow-100 text-yellow-800';
};

const getStatusText = (post: BlogPost): string => {
    return post.is_published ? 'Published' : 'Draft';
};

const formatDate = (dateString: string | null): string => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString();
};
</script>

<template>
    <Head title="Blog Posts" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Blog Posts
                </h2>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="showSettings = !showSettings"
                        class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </button>
                    <Link
                        :href="route('admin.blog.categories.index')"
                        class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                    >
                        Manage Categories
                    </Link>
                    <Link
                        :href="route('admin.blog.posts.create')"
                        class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-800"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        New Post
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-lg bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="ml-3 text-sm text-green-700">{{ $page.props.flash.success }}</p>
                    </div>
                </div>

                <!-- Blog Settings Panel -->
                <div v-if="showSettings" class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Blog Settings</h3>
                    </div>
                    <form @submit.prevent="saveSettings" class="p-6 space-y-6">
                        <!-- Navigation Settings -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-medium text-gray-900">Navigation</h4>

                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="showInNav" class="text-sm font-medium text-gray-700">Show in Navigation</label>
                                    <p class="text-sm text-gray-500">Add a link to the blog in the main navigation menu</p>
                                </div>
                                <button
                                    type="button"
                                    @click="settingsForm.showInNav = !settingsForm.showInNav"
                                    :class="[
                                        settingsForm.showInNav ? 'bg-gray-800' : 'bg-gray-200',
                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-offset-2'
                                    ]"
                                    role="switch"
                                    :aria-checked="settingsForm.showInNav"
                                >
                                    <span
                                        :class="[
                                            settingsForm.showInNav ? 'translate-x-5' : 'translate-x-0',
                                            'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                        ]"
                                    />
                                </button>
                            </div>

                            <div v-if="settingsForm.showInNav">
                                <label for="navLabel" class="block text-sm font-medium text-gray-700">Navigation Label</label>
                                <input
                                    id="navLabel"
                                    v-model="settingsForm.navLabel"
                                    type="text"
                                    class="mt-1 block w-full max-w-xs rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm"
                                    placeholder="Blog"
                                />
                                <p v-if="settingsForm.errors.navLabel" class="mt-1 text-sm text-red-600">{{ settingsForm.errors.navLabel }}</p>
                            </div>
                        </div>

                        <!-- Blog Page Features -->
                        <div class="space-y-4 border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-medium text-gray-900">Blog Page Features</h4>

                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="enableSearch" class="text-sm font-medium text-gray-700">Enable Search</label>
                                    <p class="text-sm text-gray-500">Allow visitors to search blog posts</p>
                                </div>
                                <button
                                    type="button"
                                    @click="settingsForm.enableSearch = !settingsForm.enableSearch"
                                    :class="[
                                        settingsForm.enableSearch ? 'bg-gray-800' : 'bg-gray-200',
                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-offset-2'
                                    ]"
                                    role="switch"
                                    :aria-checked="settingsForm.enableSearch"
                                >
                                    <span
                                        :class="[
                                            settingsForm.enableSearch ? 'translate-x-5' : 'translate-x-0',
                                            'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                        ]"
                                    />
                                </button>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="enableCategoryFilter" class="text-sm font-medium text-gray-700">Enable Category Filter</label>
                                    <p class="text-sm text-gray-500">Allow visitors to filter posts by category</p>
                                </div>
                                <button
                                    type="button"
                                    @click="settingsForm.enableCategoryFilter = !settingsForm.enableCategoryFilter"
                                    :class="[
                                        settingsForm.enableCategoryFilter ? 'bg-gray-800' : 'bg-gray-200',
                                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-offset-2'
                                    ]"
                                    role="switch"
                                    :aria-checked="settingsForm.enableCategoryFilter"
                                >
                                    <span
                                        :class="[
                                            settingsForm.enableCategoryFilter ? 'translate-x-5' : 'translate-x-0',
                                            'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                                        ]"
                                    />
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
                            <button
                                type="button"
                                @click="showSettings = false"
                                class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="settingsForm.processing"
                                class="rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 disabled:opacity-50"
                            >
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Posts Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div v-if="posts.length === 0" class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">No blog posts</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new blog post.</p>
                        <div class="mt-6">
                            <Link
                                :href="route('admin.blog.posts.create')"
                                class="inline-flex items-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
                            >
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                </svg>
                                New Post
                            </Link>
                        </div>
                    </div>

                    <table v-else class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                    Title
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Category
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Status
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Published
                                </th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="post in posts" :key="post.id">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="font-medium text-gray-900">{{ post.title }}</div>
                                    <div v-if="post.author" class="text-gray-500">by {{ post.author.name }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span v-if="post.category" class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                        {{ post.category.name }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span
                                        :class="getStatusBadgeClass(post)"
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    >
                                        {{ getStatusText(post) }}
                                    </span>
                                    <span
                                        v-if="post.has_unpublished_changes"
                                        class="ml-1 inline-flex items-center rounded-full bg-orange-100 text-orange-800 px-2 py-0.5 text-xs font-medium"
                                    >
                                        Unsaved
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ formatDate(post.published_at) }}
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('admin.blog.posts.edit', post.id)"
                                            class="text-gray-700 hover:text-gray-900"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            v-if="post.is_published"
                                            @click="unpublishPost(post.id)"
                                            class="text-yellow-600 hover:text-yellow-900"
                                        >
                                            Unpublish
                                        </button>
                                        <button
                                            v-else
                                            @click="publishPost(post.id)"
                                            class="text-green-600 hover:text-green-900"
                                        >
                                            Publish
                                        </button>
                                        <button
                                            v-if="confirmingDeletion !== post.id"
                                            @click="confirmingDeletion = post.id"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                        <div v-else class="flex items-center gap-1">
                                            <button
                                                @click="deletePost(post.id)"
                                                :disabled="processing"
                                                class="text-red-600 hover:text-red-900 font-semibold"
                                            >
                                                Confirm
                                            </button>
                                            <button
                                                @click="confirmingDeletion = null"
                                                class="text-gray-600 hover:text-gray-900"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
