<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
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

interface PaginatedPosts {
    data: BlogPost[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
}

interface Props {
    category: BlogCategory;
    posts: PaginatedPosts;
    categories: BlogCategory[];
    settings: Settings;
    navigation: NavItem[];
    seo: {
        title: string;
        description: string | null;
    };
}

const props = defineProps<Props>();

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
        <SeoHead :seo="{ meta: { title: seo.title, description: seo.description, keywords: null } }" :title="category.name" />

        <div class="bg-white py-12 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-12">
                    <Link href="/blog" class="text-sm text-indigo-600 hover:text-indigo-500 mb-4 inline-flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Blog
                    </Link>
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">{{ category.name }}</h1>
                    <p v-if="category.description" class="mt-4 text-lg text-gray-600">{{ category.description }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-3">
                        <div v-if="posts.data.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No posts in this category</h3>
                            <p class="mt-1 text-sm text-gray-500">Check back later for new content.</p>
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
                                            ? 'bg-indigo-600 text-white'
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
                            <!-- Categories -->
                            <div v-if="categories.length > 0" class="rounded-2xl bg-gray-50 p-6">
                                <h2 class="text-sm font-semibold leading-6 text-gray-900">Categories</h2>
                                <ul class="mt-4 space-y-3">
                                    <li v-for="cat in categories" :key="cat.id">
                                        <Link
                                            :href="route('blog.category', cat.slug)"
                                            :class="[
                                                'flex items-center justify-between text-sm',
                                                cat.id === category.id
                                                    ? 'text-indigo-600 font-medium'
                                                    : 'text-gray-600 hover:text-gray-900'
                                            ]"
                                        >
                                            <span>{{ cat.name }}</span>
                                            <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-600">
                                                {{ cat.published_posts_count }}
                                            </span>
                                        </Link>
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
