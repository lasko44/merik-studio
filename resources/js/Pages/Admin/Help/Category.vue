<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import HelpSidebar from '@/Components/HelpSidebar.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Article {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
}

interface Category {
    id: number;
    name: string;
    slug: string;
    icon: string | null;
    description: string | null;
}

interface TableOfContentsItem {
    id: number;
    name: string;
    slug: string;
    icon: string | null;
    articles: { id: number; title: string; slug: string }[];
}

interface Props {
    category: Category;
    articles: Article[];
    tableOfContents: TableOfContentsItem[];
}

const props = defineProps<Props>();
</script>

<template>
    <Head :title="category.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-2 text-sm">
                <Link :href="route('admin.help.index')" class="text-gray-500 hover:text-gray-700">
                    Help Center
                </Link>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 font-medium">{{ category.name }}</span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex gap-8">
                    <!-- Sidebar -->
                    <HelpSidebar
                        :table-of-contents="tableOfContents"
                        :current-category="category.slug"
                    />

                    <!-- Main Content -->
                    <main class="flex-1 min-w-0">
                        <!-- Category Header -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                                {{ category.name }}
                            </h1>
                            <p v-if="category.description" class="text-gray-600">
                                {{ category.description }}
                            </p>
                        </div>

                        <!-- Articles List -->
                        <div class="space-y-4">
                            <Link
                                v-for="article in articles"
                                :key="article.id"
                                :href="route('admin.help.show', article.slug)"
                                class="block bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-indigo-200 transition-all"
                            >
                                <h2 class="text-lg font-semibold text-gray-900 group-hover:text-gray-700 mb-2">
                                    {{ article.title }}
                                </h2>
                                <p v-if="article.excerpt" class="text-gray-600 text-sm">
                                    {{ article.excerpt }}
                                </p>
                                <div class="mt-4 flex items-center text-sm text-gray-700 font-medium">
                                    Read article
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </Link>

                            <!-- Empty State -->
                            <div v-if="articles.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-gray-600">No articles in this category yet.</p>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
