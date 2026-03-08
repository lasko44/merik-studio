<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import HelpSidebar from '@/Components/HelpSidebar.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

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
    content: string;
    updated_at: string;
}

interface TableOfContentsItem {
    id: number;
    name: string;
    slug: string;
    icon: string | null;
    articles: { id: number; title: string; slug: string }[];
}

interface Props {
    article: Article;
    category: Category;
    tableOfContents: TableOfContentsItem[];
}

const props = defineProps<Props>();

const formattedDate = computed(() => {
    return new Date(props.article.updated_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
});
</script>

<template>
    <Head :title="article.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-2 text-sm">
                <Link :href="route('admin.help.index')" class="text-gray-500 hover:text-gray-700">
                    Help Center
                </Link>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <Link :href="route('admin.help.category', category.slug)" class="text-gray-500 hover:text-gray-700">
                    {{ category.name }}
                </Link>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 font-medium">{{ article.title }}</span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex gap-8">
                    <!-- Sidebar -->
                    <HelpSidebar
                        :table-of-contents="tableOfContents"
                        :current-article="article.slug"
                        :current-category="category.slug"
                    />

                    <!-- Main Content -->
                    <main class="flex-1 min-w-0">
                        <article class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                            <!-- Header -->
                            <header class="mb-8">
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                                    {{ article.title }}
                                </h1>
                                <p class="text-sm text-gray-500">
                                    Last updated: {{ formattedDate }}
                                </p>
                            </header>

                            <!-- Content -->
                            <div
                                class="prose prose-slate prose-lg max-w-none prose-headings:font-semibold prose-a:text-gray-700 prose-code:before:content-none prose-code:after:content-none"
                                v-html="article.content"
                            />
                        </article>

                        <!-- Navigation -->
                        <div class="mt-6 flex items-center justify-between">
                            <Link
                                :href="route('admin.help.category', category.slug)"
                                class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-700 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to {{ category.name }}
                            </Link>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
