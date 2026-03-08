import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { PageProps } from '@/types';
import type { SeoData, JsonLdSchema } from '@/types/seo';

/**
 * Composable for accessing and managing SEO data in Vue components.
 */
export function useSeo() {
    const page = usePage<PageProps>();

    /**
     * Get default SEO data from shared props.
     */
    const defaultSeo = computed<SeoData>(() => page.props.seo);

    /**
     * Get site settings from shared props.
     */
    const siteSettings = computed(() => page.props.siteSettings);

    /**
     * Build complete page title with template.
     */
    const buildTitle = (title: string): string => {
        const template = defaultSeo.value?.meta?.titleTemplate;
        if (template) {
            return template.replace('%s', title);
        }
        return `${title} | ${siteSettings.value?.siteName || ''}`;
    };

    /**
     * Merge page-specific SEO with defaults.
     */
    const mergeSeo = (pageSeo: Partial<SeoData>): SeoData => {
        const defaults = defaultSeo.value;

        return {
            meta: {
                ...defaults.meta,
                ...pageSeo.meta,
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
    };

    /**
     * Serialize JSON-LD schemas for script injection.
     */
    const serializeJsonLd = (schemas: JsonLdSchema[] | undefined): string => {
        if (!schemas || schemas.length === 0) {
            return '{}';
        }
        return JSON.stringify(
            schemas.length === 1 ? schemas[0] : schemas,
            null,
            2
        );
    };

    /**
     * Get robots meta content string.
     */
    const getRobotsMeta = (noIndex: boolean, noFollow: boolean): string => {
        const directives: string[] = [];
        directives.push(noIndex ? 'noindex' : 'index');
        directives.push(noFollow ? 'nofollow' : 'follow');
        return directives.join(', ');
    };

    return {
        defaultSeo,
        siteSettings,
        buildTitle,
        mergeSeo,
        serializeJsonLd,
        getRobotsMeta,
    };
}
