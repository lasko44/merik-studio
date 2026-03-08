<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import HelpSidebar from '@/Components/HelpSidebar.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash-es';

interface Category {
    id: number;
    name: string;
    slug: string;
}

interface Article {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    category: Category;
}

interface TableOfContentsItem {
    id: number;
    name: string;
    slug: string;
    icon: string | null;
    articles: { id: number; title: string; slug: string }[];
}

interface Props {
    query: string;
    results: Article[];
    tableOfContents: TableOfContentsItem[];
}

const props = defineProps<Props>();

const searchQuery = ref(props.query);

const performSearch = debounce((value: string) => {
    router.get(
        route('admin.help.search'),
        { q: value },
        { preserveState: true, preserveScroll: true }
    );
}, 300);

watch(searchQuery, (value) => {
    if (value.length >= 2 || value.length === 0) {
        performSearch(value);
    }
});
</script>

<template>
    <Head title="Search Help" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-2 text-sm">
                <Link :href="route('admin.help.index')" class="text-gray-500 hover:text-gray-700">
                    Help Center
                </Link>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 font-medium">Search</span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex gap-8">
                    <!-- Sidebar -->
                    <HelpSidebar :table-of-contents="tableOfContents" />

                    <!-- Main Content -->
                    <main class="flex-1 min-w-0">
                        <!-- Search Box -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search help articles..."
                                    class="block w-full pl-12 pr-4 py-3 border-gray-300 rounded-lg focus:ring-gray-700 focus:border-gray-700 text-lg"
                                    autofocus
                                />
                            </div>
                        </div>

                        <!-- Results -->
                        <div v-if="query">
                            <p class="text-sm text-gray-600 mb-4">
                                {{ results.length }} result{{ results.length !== 1 ? 's' : '' }} for "{{ query }}"
                            </p>

                            <div class="space-y-4">
                                <Link
                                    v-for="article in results"
                                    :key="article.id"
                                    :href="route('admin.help.show', article.slug)"
                                    class="block bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-indigo-200 transition-all"
                                >
                                    <div class="flex items-start gap-4">
                                        <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                                {{ article.title }}
                                            </h3>
                                            <p class="text-sm text-gray-700 mb-2">
                                                {{ article.category.name }}
                                            </p>
                                            <p v-if="article.excerpt" class="text-gray-600 text-sm line-clamp-2">
                                                {{ article.excerpt }}
                                            </p>
                                        </div>
                                    </div>
                                </Link>
                            </div>

                            <!-- No Results -->
                            <div v-if="results.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-600 mb-2">No results found for "{{ query }}"</p>
                                <p class="text-sm text-gray-500">Try different keywords or browse the categories.</p>
                            </div>
                        </div>

                        <!-- Initial State -->
                        <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <p class="text-gray-600">Enter at least 2 characters to search.</p>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
