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
    articles: Article[];
    published_articles: Article[];
}

interface TableOfContentsItem {
    id: number;
    name: string;
    slug: string;
    icon: string | null;
    articles: { id: number; title: string; slug: string }[];
}

interface Props {
    categories: Category[];
    tableOfContents: TableOfContentsItem[];
}

const props = defineProps<Props>();

const getCategoryIcon = (icon: string | null): string => {
    const icons: Record<string, string> = {
        'book': 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        'cog': 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'document': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'chart': 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'users': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        'globe': 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
        'default': 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    };
    return icons[icon || 'default'] || icons['default'];
};
</script>

<template>
    <Head title="Help Center" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Help Center
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex gap-8">
                    <!-- Sidebar -->
                    <HelpSidebar :table-of-contents="tableOfContents" />

                    <!-- Main Content -->
                    <main class="flex-1 min-w-0">
                        <!-- Welcome Section -->
                        <div class="bg-gradient-to-r from-gray-800 to-gray-700 rounded-xl p-8 mb-8 text-white">
                            <h1 class="text-3xl font-bold mb-2">Welcome to the Help Center</h1>
                            <p class="text-gray-300">
                                Find guides and documentation to help you get the most out of Merik Studio.
                            </p>
                        </div>

                        <!-- Category Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                v-for="category in categories"
                                :key="category.id"
                                class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
                            >
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getCategoryIcon(category.icon)" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <Link
                                            :href="route('admin.help.category', category.slug)"
                                            class="text-lg font-semibold text-gray-900 hover:text-gray-700 transition-colors"
                                        >
                                            {{ category.name }}
                                        </Link>
                                        <p v-if="category.description" class="mt-1 text-sm text-gray-600">
                                            {{ category.description }}
                                        </p>
                                        <p class="mt-2 text-xs text-gray-500">
                                            {{ category.published_articles?.length || 0 }} articles
                                        </p>

                                        <!-- Article Links -->
                                        <ul v-if="category.published_articles?.length" class="mt-4 space-y-2">
                                            <li v-for="article in category.published_articles.slice(0, 3)" :key="article.id">
                                                <Link
                                                    :href="route('admin.help.show', article.slug)"
                                                    class="text-sm text-gray-600 hover:text-gray-700 flex items-center gap-2"
                                                >
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                    {{ article.title }}
                                                </Link>
                                            </li>
                                            <li v-if="category.published_articles.length > 3">
                                                <Link
                                                    :href="route('admin.help.category', category.slug)"
                                                    class="text-sm text-gray-700 hover:text-gray-900 font-medium"
                                                >
                                                    View all {{ category.published_articles.length }} articles
                                                </Link>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="mt-8 bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Need more help?</h3>
                            <p class="text-gray-600">
                                If you can't find what you're looking for, contact your site administrator for assistance.
                            </p>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
