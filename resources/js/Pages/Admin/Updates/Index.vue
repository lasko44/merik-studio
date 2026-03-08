<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Release {
    version: string;
    name: string;
    notes: string;
    url: string;
    published_at: string;
    is_current: boolean;
    is_newer: boolean;
}

interface VersionInfo {
    current_version: string;
    latest_version: string;
    update_available: boolean;
    versions_behind: number;
    releases: Release[];
    checked_at: string;
    release_date: string | null;
}

interface Props {
    versionInfo: VersionInfo;
    githubRepo: string;
}

const props = defineProps<Props>();

const checking = ref(false);
const localVersionInfo = ref(props.versionInfo);

const formatDate = (dateStr: string): string => {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatDateTime = (dateStr: string): string => {
    return new Date(dateStr).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const checkForUpdates = async () => {
    checking.value = true;

    router.post(route('admin.updates.check'), {}, {
        preserveScroll: true,
        onSuccess: (page) => {
            // @ts-ignore
            if (page.props.versionInfo) {
                // @ts-ignore
                localVersionInfo.value = page.props.versionInfo;
            }
        },
        onFinish: () => {
            checking.value = false;
        },
    });
};

const parseMarkdown = (text: string): string => {
    if (!text) return '';

    // Basic markdown parsing for release notes
    return text
        // Headers
        .replace(/^### (.*$)/gim, '<h4 class="font-semibold text-gray-900 mt-4 mb-2">$1</h4>')
        .replace(/^## (.*$)/gim, '<h3 class="font-semibold text-gray-900 text-lg mt-4 mb-2">$1</h3>')
        // Bold
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        // Lists
        .replace(/^\* (.*$)/gim, '<li class="ml-4">$1</li>')
        .replace(/^- (.*$)/gim, '<li class="ml-4">$1</li>')
        // Links
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" class="text-indigo-600 hover:text-indigo-800">$1</a>')
        // Newlines
        .replace(/\n/g, '<br>');
};
</script>

<template>
    <Head title="Updates" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">System Updates</h2>
                <button
                    @click="checkForUpdates"
                    :disabled="checking"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
                >
                    <svg v-if="checking" class="animate-spin -ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ checking ? 'Checking...' : 'Check for Updates' }}
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Version Status Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Current Version</h3>
                                <div class="mt-2 flex items-center gap-3">
                                    <span class="text-3xl font-bold text-gray-900">
                                        v{{ localVersionInfo.current_version }}
                                    </span>
                                    <span
                                        v-if="localVersionInfo.update_available"
                                        class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-800"
                                    >
                                        <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Update Available
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800"
                                    >
                                        <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Up to Date
                                    </span>
                                </div>
                                <p v-if="localVersionInfo.release_date" class="mt-1 text-sm text-gray-500">
                                    Released {{ formatDate(localVersionInfo.release_date) }}
                                </p>
                            </div>
                            <div v-if="localVersionInfo.update_available" class="text-right">
                                <p class="text-sm text-gray-500">Latest version</p>
                                <p class="text-2xl font-bold text-indigo-600">v{{ localVersionInfo.latest_version }}</p>
                                <p v-if="localVersionInfo.versions_behind > 0" class="text-sm text-amber-600">
                                    {{ localVersionInfo.versions_behind }} version{{ localVersionInfo.versions_behind > 1 ? 's' : '' }} behind
                                </p>
                            </div>
                        </div>

                        <div v-if="localVersionInfo.update_available" class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-amber-800">Update Instructions</h3>
                                    <div class="mt-2 text-sm text-amber-700">
                                        <p>To update your installation, run the following commands:</p>
                                        <pre class="mt-2 p-3 bg-amber-100 rounded text-xs overflow-x-auto">git fetch origin
git checkout v{{ localVersionInfo.latest_version }}
composer install --no-dev
php artisan migrate
npm install && npm run build
php artisan optimize</pre>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p v-if="localVersionInfo.checked_at" class="mt-4 text-xs text-gray-400">
                            Last checked: {{ formatDateTime(localVersionInfo.checked_at) }}
                        </p>
                    </div>
                </div>

                <!-- Release History -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Release History</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Recent releases from
                            <a :href="`https://github.com/${githubRepo}/releases`" target="_blank" class="text-indigo-600 hover:text-indigo-800">
                                {{ githubRepo }}
                            </a>
                        </p>
                    </div>

                    <div v-if="localVersionInfo.releases.length === 0" class="p-6 text-center text-gray-500">
                        No releases found. Make sure the GitHub repository is accessible.
                    </div>

                    <div v-else class="divide-y divide-gray-200">
                        <div
                            v-for="release in localVersionInfo.releases"
                            :key="release.version"
                            :class="[
                                'p-6',
                                release.is_current ? 'bg-indigo-50' : '',
                                release.is_newer ? 'bg-amber-50' : ''
                            ]"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-lg font-semibold text-gray-900">
                                        v{{ release.version }}
                                    </span>
                                    <span
                                        v-if="release.is_current"
                                        class="inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800"
                                    >
                                        Current
                                    </span>
                                    <span
                                        v-if="release.is_newer"
                                        class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800"
                                    >
                                        New
                                    </span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-500">
                                        {{ formatDate(release.published_at) }}
                                    </span>
                                    <a
                                        :href="release.url"
                                        target="_blank"
                                        class="text-indigo-600 hover:text-indigo-800"
                                    >
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <p v-if="release.name && release.name !== `v${release.version}`" class="mt-1 text-sm font-medium text-gray-700">
                                {{ release.name }}
                            </p>
                            <div
                                v-if="release.notes"
                                class="mt-3 prose prose-sm max-w-none text-gray-600"
                                v-html="parseMarkdown(release.notes)"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
