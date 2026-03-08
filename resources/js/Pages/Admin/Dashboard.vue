<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface RecentPage {
    id: number;
    title: string;
    path: string;
    is_published: boolean;
    updated_at: string;
}

interface UpdateInfo {
    available: boolean;
    current_version: string;
    latest_version: string;
    release_name: string | null;
}

interface Props {
    stats: {
        totalPages: number;
        publishedPages: number;
        draftPages: number;
    };
    recentPages: RecentPage[];
    pageLimit: number | null;
    remainingPages: number | null;
    updateInfo: UpdateInfo | null;
}

const props = defineProps<Props>();

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Update Available Banner -->
                <div v-if="updateInfo?.available" class="mb-6 rounded-lg bg-amber-50 border border-amber-200 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-amber-800">Update Available</h3>
                            <p class="mt-1 text-sm text-amber-700">
                                A new version (v{{ updateInfo.latest_version }}) is available.
                                <span v-if="updateInfo.release_name">{{ updateInfo.release_name }}</span>
                            </p>
                        </div>
                        <div class="ml-auto pl-3">
                            <Link
                                :href="route('admin.updates.index')"
                                class="inline-flex items-center rounded-md bg-amber-100 px-3 py-2 text-sm font-medium text-amber-800 hover:bg-amber-200"
                            >
                                View Details
                                <svg class="ml-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                    <!-- Total Pages -->
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Total Pages</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                            {{ stats.totalPages }}
                        </dd>
                    </div>

                    <!-- Published Pages -->
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Published</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">
                            {{ stats.publishedPages }}
                        </dd>
                    </div>

                    <!-- Draft Pages -->
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Drafts</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-yellow-600">
                            {{ stats.draftPages }}
                        </dd>
                    </div>

                    <!-- Page Limit -->
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">
                            {{ pageLimit ? 'Remaining Pages' : 'Page Limit' }}
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-700">
                            {{ pageLimit ? remainingPages : 'Unlimited' }}
                        </dd>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <!-- Recent Pages -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Pages</h3>
                                <Link
                                    :href="route('admin.pages.index')"
                                    class="text-sm font-medium text-gray-700 hover:text-gray-600"
                                >
                                    View all
                                </Link>
                            </div>
                        </div>
                        <ul v-if="recentPages.length > 0" class="divide-y divide-gray-200">
                            <li v-for="page in recentPages" :key="page.id" class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="truncate">
                                        <Link
                                            :href="route('admin.pages.edit', page.id)"
                                            class="text-sm font-medium text-gray-700 hover:text-gray-600 truncate"
                                        >
                                            {{ page.title }}
                                        </Link>
                                        <p class="text-sm text-gray-500">{{ page.path }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span
                                            :class="[
                                                page.is_published
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-yellow-100 text-yellow-800',
                                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium'
                                            ]"
                                        >
                                            {{ page.is_published ? 'Published' : 'Draft' }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ formatDate(page.updated_at) }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div v-else class="px-4 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No pages yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first page.</p>
                            <div class="mt-6">
                                <Link
                                    :href="route('admin.pages.create')"
                                    class="inline-flex items-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
                                >
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                    </svg>
                                    Create Page
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4">
                                <Link
                                    :href="route('admin.pages.create')"
                                    class="relative flex flex-col items-center rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500"
                                >
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                                        <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <span class="mt-2 text-sm font-medium text-gray-900">Create Page</span>
                                </Link>

                                <Link
                                    :href="route('admin.pages.index')"
                                    class="relative flex flex-col items-center rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500"
                                >
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                                        <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <span class="mt-2 text-sm font-medium text-gray-900">Manage Pages</span>
                                </Link>

                                <Link
                                    :href="route('admin.settings.edit')"
                                    class="relative flex flex-col items-center rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500"
                                >
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                                        <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span class="mt-2 text-sm font-medium text-gray-900">Site Settings</span>
                                </Link>

                                <Link
                                    :href="route('admin.help.index')"
                                    class="relative flex flex-col items-center rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500"
                                >
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                                        <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span class="mt-2 text-sm font-medium text-gray-900">Help Center</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
