<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { SeoData } from '@/types/seo';
import { useSeo } from '@/composables/useSeo';

interface Props {
    seo?: Partial<SeoData>;
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    seo: undefined,
    title: undefined,
});

const { defaultSeo, siteSettings, buildTitle, serializeJsonLd } = useSeo();

/**
 * Merged SEO data combining defaults with page-specific overrides.
 */
const seoData = computed<SeoData>(() => {
    const defaults = defaultSeo.value;
    const pageSeo = props.seo || {};

    return {
        meta: {
            ...defaults.meta,
            ...pageSeo.meta,
            title: props.title || pageSeo.meta?.title || defaults.meta.title,
        },
        openGraph: {
            ...defaults.openGraph,
            ...pageSeo.openGraph,
        },
        twitter: {
            ...defaults.twitter,
            ...pageSeo.twitter,
        },
        canonical: pageSeo.canonical || defaults.canonical,
        robots: pageSeo.robots || defaults.robots,
        jsonLd: pageSeo.jsonLd || defaults.jsonLd,
    };
});

/**
 * Full page title with site name.
 */
const pageTitle = computed(() => {
    const title = seoData.value.meta.title;
    return buildTitle(title);
});

/**
 * Whether JSON-LD data exists.
 */
const hasJsonLd = computed(() => {
    return seoData.value.jsonLd && seoData.value.jsonLd.length > 0;
});

/**
 * Serialized JSON-LD for script tag.
 */
const jsonLdScript = computed(() => {
    if (!hasJsonLd.value) return '';
    return serializeJsonLd(seoData.value.jsonLd);
});
</script>

<template>
    <Head>
        <!-- Primary Meta Tags -->
        <title>{{ pageTitle }}</title>
        <meta
            v-if="seoData.meta.description"
            name="description"
            :content="seoData.meta.description"
        />
        <meta
            v-if="seoData.meta.keywords"
            name="keywords"
            :content="seoData.meta.keywords"
        />
        <meta name="robots" :content="seoData.robots" />

        <!-- Canonical URL -->
        <link rel="canonical" :href="seoData.canonical" />

        <!-- Open Graph / Facebook -->
        <meta property="og:type" :content="seoData.openGraph.type" />
        <meta property="og:title" :content="seoData.openGraph.title" />
        <meta
            v-if="seoData.openGraph.description"
            property="og:description"
            :content="seoData.openGraph.description"
        />
        <meta property="og:url" :content="seoData.openGraph.url" />
        <meta property="og:site_name" :content="seoData.openGraph.siteName" />
        <meta
            v-if="seoData.openGraph.image"
            property="og:image"
            :content="seoData.openGraph.image"
        />

        <!-- X (Twitter) Card -->
        <meta name="twitter:card" :content="seoData.twitter.card" />
        <meta
            v-if="seoData.twitter.site"
            name="twitter:site"
            :content="seoData.twitter.site"
        />
        <meta name="twitter:title" :content="seoData.twitter.title" />
        <meta
            v-if="seoData.twitter.description"
            name="twitter:description"
            :content="seoData.twitter.description"
        />
        <meta
            v-if="seoData.twitter.image"
            name="twitter:image"
            :content="seoData.twitter.image"
        />

        <!-- JSON-LD Structured Data -->
        <component
            v-if="hasJsonLd"
            :is="'script'"
            type="application/ld+json"
            v-html="jsonLdScript"
        />
    </Head>
</template>
