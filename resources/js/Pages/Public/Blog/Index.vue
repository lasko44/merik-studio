<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import type { BlogPost, BlogCategory } from '@/types/admin';

interface NavItem {
    title: string;
    path: string;
}

interface Settings {
    siteName: string;
    tagline: string | null;
    logoPath: string | null;
    showSiteNameInNav: boolean;
    navLogoHeight: number;
    primaryColor: string;
    secondaryColor: string;
    accentColor: string;
    backgroundColor: string;
    textColor: string;
    successColor: string;
    warningColor: string;
    errorColor: string;
    infoColor: string;
    surfaceColor: string;
    borderColor: string;
    mutedColor: string;
    contactEmail: string | null;
    contactPhone: string | null;
    contactAddress: string | null;
    socialLinks: Array<{ platform: string; url: string }> | null;
}

interface BlogSettings {
    enableSearch: boolean;
    enableCategoryFilter: boolean;
}

interface Filters {
    search: string | null;
    category: string | null;
}

interface PaginatedPosts {
    data: BlogPost[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
}

interface Props {
    posts: PaginatedPosts;
    categories: BlogCategory[];
    settings: Settings;
    blogSettings: BlogSettings;
    filters: Filters;
    navigation: NavItem[];
    seo: {
        title: string;
        description: string | null;
    };
}

const props = defineProps<Props>();

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const applyFilters = () => {
    const params: Record<string, string> = {};

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }
    if (selectedCategory.value) {
        params.category = selectedCategory.value;
    }

    router.get(route('blog.index'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
};

const handleCategoryChange = () => {
    applyFilters();
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    router.get(route('blog.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const hasActiveFilters = () => {
    return searchQuery.value || selectedCategory.value;
};

const formatDate = (dateString: string | null): string => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>
    <PublicLayout :settings="settings" :navigation="navigation">
        <SeoHead :seo="{ meta: { title: seo.title, description: seo.description, keywords: null } }" :title="seo.title" />

        <div class="bg-white py-12 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">Blog</h1>
                    <p class="mt-4 text-lg text-gray-600">The latest news, updates, and insights.</p>
                </div>

                <!-- Search and Filter Bar -->
                <div v-if="blogSettings.enableSearch || blogSettings.enableCategoryFilter" class="mb-8">
                    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                        <!-- Search Input -->
                        <div v-if="blogSettings.enableSearch" class="relative flex-1 w-full sm:max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search posts..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                @input="handleSearch"
                            />
                        </div>

                        <!-- Category Filter -->
                        <div v-if="blogSettings.enableCategoryFilter && categories.length > 0" class="w-full sm:w-auto">
                            <select
                                v-model="selectedCategory"
                                class="block w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                @change="handleCategoryChange"
                            >
                                <option value="">All Categories</option>
                                <option v-for="category in categories" :key="category.id" :value="category.slug">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        <button
                            v-if="hasActiveFilters()"
                            @click="clearFilters"
                            class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear filters
                        </button>
                    </div>

                    <!-- Active Filters Display -->
                    <div v-if="hasActiveFilters()" class="mt-3 flex flex-wrap gap-2">
                        <span
                            v-if="searchQuery"
                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-sm text-gray-700"
                        >
                            Search: "{{ searchQuery }}"
                        </span>
                        <span
                            v-if="selectedCategory"
                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-sm text-gray-700"
                        >
                            Category: {{ categories.find(c => c.slug === selectedCategory)?.name }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-3">
                        <div v-if="posts.data.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">
                                {{ hasActiveFilters() ? 'No matching posts' : 'No posts yet' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ hasActiveFilters() ? 'Try adjusting your search or filter.' : 'Check back later for new content.' }}
                            </p>
                            <button
                                v-if="hasActiveFilters()"
                                @click="clearFilters"
                                class="mt-4 text-sm text-gray-600 hover:text-gray-900 underline"
                            >
                                Clear all filters
                            </button>
                        </div>

                        <div v-else class="space-y-12">
                            <article
                                v-for="post in posts.data"
                                :key="post.id"
                                class="relative flex flex-col gap-8 lg:flex-row"
                            >
                                <div v-if="post.featured_image" class="relative aspect-[16/9] sm:aspect-[2/1] lg:aspect-square lg:w-64 lg:shrink-0">
                                    <img
                                        :src="post.featured_image"
                                        :alt="post.title"
                                        class="absolute inset-0 h-full w-full rounded-2xl bg-gray-50 object-cover"
                                    />
                                    <div class="absolute inset-0 rounded-2xl ring-1 ring-inset ring-gray-900/10" />
                                </div>

                                <div>
                                    <div class="flex items-center gap-x-4 text-xs">
                                        <time :datetime="post.published_at ?? undefined" class="text-gray-500">
                                            {{ formatDate(post.published_at) }}
                                        </time>
                                        <Link
                                            v-if="post.category"
                                            :href="route('blog.category', post.category.slug)"
                                            class="relative z-10 rounded-full bg-gray-50 px-3 py-1.5 font-medium text-gray-600 hover:bg-gray-100"
                                        >
                                            {{ post.category.name }}
                                        </Link>
                                    </div>

                                    <div class="group relative max-w-xl">
                                        <h3 class="mt-3 text-lg font-semibold leading-6 text-gray-900 group-hover:text-gray-600">
                                            <Link :href="route('blog.show', post.slug)">
                                                <span class="absolute inset-0" />
                                                {{ post.title }}
                                            </Link>
                                        </h3>
                                        <p class="mt-5 text-sm leading-6 text-gray-600">
                                            {{ post.excerpt }}
                                        </p>
                                    </div>

                                    <div v-if="post.author" class="mt-6 flex items-center gap-x-4">
                                        <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-600">
                                                {{ post.author.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div class="text-sm leading-6">
                                            <p class="font-semibold text-gray-900">{{ post.author.name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- Pagination -->
                        <nav v-if="posts.last_page > 1" class="mt-12 flex items-center justify-center gap-2">
                            <template v-for="link in posts.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg',
                                        link.active
                                            ? 'blog-pagination-active text-white'
                                            : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
                                    ]"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="px-4 py-2 text-sm font-medium text-gray-400"
                                    v-html="link.label"
                                />
                            </template>
                        </nav>
                    </div>

                    <!-- Sidebar -->
                    <aside class="lg:col-span-1">
                        <div class="sticky top-24 space-y-8">
                            <!-- Categories (in sidebar for desktop) -->
                            <div v-if="categories.length > 0 && !blogSettings.enableCategoryFilter" class="rounded-2xl bg-gray-50 p-6">
                                <h2 class="text-sm font-semibold leading-6 text-gray-900">Categories</h2>
                                <ul class="mt-4 space-y-3">
                                    <li v-for="category in categories" :key="category.id">
                                        <Link
                                            :href="route('blog.category', category.slug)"
                                            class="flex items-center justify-between text-sm text-gray-600 hover:text-gray-900"
                                        >
                                            <span>{{ category.name }}</span>
                                            <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-600">
                                                {{ category.published_posts_count }}
                                            </span>
                                        </Link>
                                    </li>
                                </ul>
                            </div>

                            <!-- Categories with counts (when filter is enabled) -->
                            <div v-if="categories.length > 0 && blogSettings.enableCategoryFilter" class="rounded-2xl bg-gray-50 p-6">
                                <h2 class="text-sm font-semibold leading-6 text-gray-900">Browse Categories</h2>
                                <ul class="mt-4 space-y-3">
                                    <li v-for="category in categories" :key="category.id">
                                        <button
                                            @click="selectedCategory = category.slug; handleCategoryChange()"
                                            :class="[
                                                'flex items-center justify-between w-full text-sm transition-colors',
                                                selectedCategory === category.slug
                                                    ? 'text-gray-900 font-medium'
                                                    : 'text-gray-600 hover:text-gray-900'
                                            ]"
                                        >
                                            <span>{{ category.name }}</span>
                                            <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-600">
                                                {{ category.published_posts_count }}
                                            </span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
.blog-pagination-active {
    background-color: var(--primary-color);
}
</style>
