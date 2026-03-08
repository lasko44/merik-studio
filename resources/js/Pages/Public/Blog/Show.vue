<script setup lang="ts">
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import HeroSection from '@/Components/Blocks/HeroSection.vue';
import FeaturesSection from '@/Components/Blocks/FeaturesSection.vue';
import TextSection from '@/Components/Blocks/TextSection.vue';
import CtaSection from '@/Components/Blocks/CtaSection.vue';
import ContactSection from '@/Components/Blocks/ContactSection.vue';
import PricingSection from '@/Components/Blocks/PricingSection.vue';
import ImageSection from '@/Components/Blocks/ImageSection.vue';
import TwoColumnSection from '@/Components/Blocks/TwoColumnSection.vue';
import HeadingSection from '@/Components/Blocks/HeadingSection.vue';
import SpacerSection from '@/Components/Blocks/SpacerSection.vue';
import { Link } from '@inertiajs/vue3';
import type { BlogPost, BlogCategory } from '@/types/admin';

interface ContentBlock {
    type: string;
    data: Record<string, unknown>;
}

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

interface Props {
    post: BlogPost;
    categories: BlogCategory[];
    settings: Settings;
    navigation: NavItem[];
    seo: {
        title: string;
        description: string | null;
        ogTitle?: string;
        ogDescription?: string | null;
        ogImage?: string | null;
        noIndex?: boolean;
    };
    canEdit?: boolean;
    editUrl?: string | null;
    isPreview?: boolean;
}

const props = defineProps<Props>();

const getComponent = (type: string) => {
    const components: Record<string, unknown> = {
        hero: HeroSection,
        features: FeaturesSection,
        text: TextSection,
        cta: CtaSection,
        contact_info: ContactSection,
        contact_form: ContactSection,
        pricing: PricingSection,
        image: ImageSection,
        'two-column-layout': TwoColumnSection,
        heading: HeadingSection,
        spacer: SpacerSection,
    };
    return components[type] || null;
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
        <SeoHead
            :seo="{
                meta: { title: seo.title, description: seo.description, keywords: null },
                openGraph: { type: 'article', title: seo.ogTitle || seo.title, description: seo.ogDescription || seo.description, image: seo.ogImage ?? null, url: '', siteName: settings.siteName },
                robots: seo.noIndex ? 'noindex, nofollow' : 'index, follow',
            }"
            :title="post.title"
        />

        <!-- Draft Preview Banner -->
        <div v-if="isPreview" class="bg-amber-500 text-amber-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                <div class="flex items-center justify-center gap-2 text-sm font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Preview Mode - This post is not published yet</span>
                    <a
                        v-if="editUrl"
                        :href="editUrl"
                        class="ml-2 underline hover:no-underline"
                    >
                        Edit &amp; Publish
                    </a>
                </div>
            </div>
        </div>

        <article class="bg-white py-12 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-3">
                        <!-- Header -->
                        <header class="mb-8">
                            <Link href="/blog" class="text-sm text-indigo-600 hover:text-indigo-500 mb-4 inline-flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Blog
                            </Link>

                            <div class="flex items-center gap-x-4 text-sm mt-4">
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

                            <h1 class="mt-4 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                                {{ post.title }}
                            </h1>

                            <p v-if="post.excerpt" class="mt-6 text-xl text-gray-600">
                                {{ post.excerpt }}
                            </p>

                            <div v-if="post.author" class="mt-8 flex items-center gap-x-4">
                                <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center">
                                    <span class="text-lg font-medium text-gray-600">
                                        {{ post.author.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <div class="text-sm leading-6">
                                    <p class="font-semibold text-gray-900">{{ post.author.name }}</p>
                                    <p class="text-gray-500">Author</p>
                                </div>
                            </div>
                        </header>

                        <!-- Featured Image -->
                        <div v-if="post.featured_image" class="mb-12">
                            <img
                                :src="post.featured_image"
                                :alt="post.title"
                                class="w-full rounded-2xl object-cover max-h-[500px]"
                            />
                        </div>

                        <!-- Content Blocks -->
                        <div class="prose prose-lg max-w-none">
                            <template v-if="post.content && post.content.length > 0">
                                <component
                                    v-for="(block, index) in post.content"
                                    :key="index"
                                    :is="getComponent(block.type)"
                                    v-bind="block.data"
                                    :settings="settings"
                                />
                            </template>

                            <div v-else class="text-gray-500">
                                <p>This post has no content yet.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <aside class="lg:col-span-1">
                        <div class="sticky top-24 space-y-8">
                            <!-- Categories -->
                            <div v-if="categories.length > 0" class="rounded-2xl bg-gray-50 p-6">
                                <h2 class="text-sm font-semibold leading-6 text-gray-900">Categories</h2>
                                <ul class="mt-4 space-y-3">
                                    <li v-for="category in categories" :key="category.id">
                                        <Link
                                            :href="route('blog.category', category.slug)"
                                            :class="[
                                                'flex items-center justify-between text-sm',
                                                post.category?.id === category.id
                                                    ? 'text-indigo-600 font-medium'
                                                    : 'text-gray-600 hover:text-gray-900'
                                            ]"
                                        >
                                            <span>{{ category.name }}</span>
                                            <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-600">
                                                {{ category.published_posts_count }}
                                            </span>
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </article>

        <!-- Edit Button (floating, only shown when user can edit) -->
        <a
            v-if="canEdit && editUrl"
            :href="editUrl"
            class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:bg-indigo-500 transition-colors"
            title="Edit this post"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span class="hidden sm:inline">Edit Post</span>
        </a>
    </PublicLayout>
</template>
