<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { AdminPage } from '@/types/admin';

interface Props {
    pages: AdminPage[];
    pageLimit: number | null;
    remainingPages: number | null;
}

const props = defineProps<Props>();

const confirmingDeletion = ref<number | null>(null);
const processing = ref(false);

const publishPage = (pageId: number) => {
    router.post(route('admin.pages.publish', pageId), {}, {
        preserveScroll: true,
    });
};

const unpublishPage = (pageId: number) => {
    router.post(route('admin.pages.unpublish', pageId), {}, {
        preserveScroll: true,
    });
};

const duplicatePage = (pageId: number) => {
    router.post(route('admin.pages.duplicate', pageId), {}, {
        preserveScroll: true,
    });
};

const deletePage = (pageId: number) => {
    processing.value = true;
    router.delete(route('admin.pages.destroy', pageId), {
        preserveScroll: true,
        onFinish: () => {
            processing.value = false;
            confirmingDeletion.value = null;
        },
    });
};

const getStatusBadgeClass = (page: AdminPage): string => {
    if (page.is_admin_page) {
        return 'bg-purple-100 text-purple-800';
    }
    return page.is_published
        ? 'bg-green-100 text-green-800'
        : 'bg-yellow-100 text-yellow-800';
};

const getStatusText = (page: AdminPage): string => {
    if (page.is_admin_page) {
        return 'Admin';
    }
    return page.is_published ? 'Published' : 'Draft';
};
</script>

<template>
    <Head title="Pages" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Pages
                </h2>
                <Link
                    :href="route('admin.pages.create')"
                    class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-800"
                >
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Create Page
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Page Limit Info -->
                <div v-if="pageLimit !== null" class="mb-6 rounded-lg bg-blue-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Your plan allows up to <strong>{{ pageLimit }}</strong> public pages.
                                <span v-if="remainingPages !== null">
                                    You can create <strong>{{ remainingPages }}</strong> more.
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

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

                <!-- Pages Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div v-if="pages.length === 0" class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">No pages</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new page.</p>
                        <div class="mt-6">
                            <Link
                                :href="route('admin.pages.create')"
                                class="inline-flex items-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
                            >
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                </svg>
                                Create Page
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
                                    Path
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Status
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Updated
                                </th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="page in pages" :key="page.id">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="font-medium text-gray-900">{{ page.title }}</div>
                                    <div v-if="page.creator" class="text-gray-500">by {{ page.creator.name }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <code class="rounded bg-gray-100 px-2 py-1 text-xs">{{ page.path }}</code>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span
                                        :class="getStatusBadgeClass(page)"
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    >
                                        {{ getStatusText(page) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ new Date(page.updated_at).toLocaleDateString() }}
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('admin.pages.edit', page.id)"
                                            class="text-gray-700 hover:text-gray-900"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            v-if="!page.is_admin_page && page.is_published"
                                            @click="unpublishPage(page.id)"
                                            class="text-yellow-600 hover:text-yellow-900"
                                        >
                                            Unpublish
                                        </button>
                                        <button
                                            v-else-if="!page.is_admin_page"
                                            @click="publishPage(page.id)"
                                            class="text-green-600 hover:text-green-900"
                                        >
                                            Publish
                                        </button>
                                        <button
                                            @click="duplicatePage(page.id)"
                                            class="text-gray-600 hover:text-gray-900"
                                        >
                                            Duplicate
                                        </button>
                                        <button
                                            v-if="confirmingDeletion !== page.id"
                                            @click="confirmingDeletion = page.id"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                        <div v-else class="flex items-center gap-1">
                                            <button
                                                @click="deletePage(page.id)"
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
