<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Article {
    id: number;
    title: string;
    slug: string;
}

interface Category {
    id: number;
    name: string;
    slug: string;
    icon: string | null;
    articles: Article[];
}

interface Props {
    tableOfContents: Category[];
    currentArticle?: string;
    currentCategory?: string;
}

const props = defineProps<Props>();

const getCategoryIcon = (icon: string | null): string => {
    const icons: Record<string, string> = {
        'book': 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        'cog': 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'document': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'chart': 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'users': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        'search': 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        'globe': 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
        'default': 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    };
    return icons[icon || 'default'] || icons['default'];
};
</script>

<template>
    <aside class="w-64 flex-shrink-0">
        <div class="sticky top-4">
            <!-- Search -->
            <div class="mb-6">
                <Link
                    :href="route('admin.help.search')"
                    class="flex items-center gap-2 w-full px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search help articles...
                </Link>
            </div>

            <!-- Navigation -->
            <nav class="space-y-6">
                <div v-for="category in tableOfContents" :key="category.id">
                    <Link
                        :href="route('admin.help.category', category.slug)"
                        class="flex items-center gap-2 text-sm font-semibold text-gray-900 hover:text-indigo-600 transition-colors"
                        :class="{ 'text-indigo-600': currentCategory === category.slug }"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getCategoryIcon(category.icon)" />
                        </svg>
                        {{ category.name }}
                    </Link>

                    <ul class="mt-2 ml-7 space-y-1">
                        <li v-for="article in category.articles" :key="article.id">
                            <Link
                                :href="route('admin.help.show', article.slug)"
                                class="block text-sm text-gray-600 hover:text-indigo-600 py-1 transition-colors"
                                :class="{ 'text-indigo-600 font-medium': currentArticle === article.slug }"
                            >
                                {{ article.title }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </aside>
</template>
