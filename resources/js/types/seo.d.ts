/**
 * SEO and GEO TypeScript type definitions.
 */

export interface MetaTags {
    title: string;
    titleTemplate?: string;
    description: string | null;
    keywords: string | null;
}

export interface OpenGraphData {
    type: 'website' | 'article' | 'product' | 'business.business';
    title: string;
    description: string | null;
    image: string | null;
    url: string;
    siteName: string;
}

export interface TwitterCardData {
    card: 'summary' | 'summary_large_image' | 'app' | 'player';
    site: string | null;
    title: string;
    description: string | null;
    image: string | null;
}

export interface JsonLdSchema {
    '@context': string;
    '@type': string;
    '@id'?: string;
    [key: string]: unknown;
}

export interface SeoData {
    meta: MetaTags;
    openGraph: OpenGraphData;
    twitter: TwitterCardData;
    canonical: string;
    robots: string;
    jsonLd: JsonLdSchema[];
}

export interface FaqItem {
    question: string;
    answer: string;
}

export interface PageSeoFields {
    meta_description: string | null;
    meta_keywords: string | null;
    og_title: string | null;
    og_description: string | null;
    og_image: string | null;
    twitter_card_type: 'summary' | 'summary_large_image' | 'app' | 'player';
    canonical_url: string | null;
    no_index: boolean;
    no_follow: boolean;
    schema_type: SchemaType;
    faqs: FaqItem[] | null;
    speakable_selectors: string[] | null;
    priority: number;
    change_frequency: ChangeFrequency;
}

export type SchemaType =
    | 'WebPage'
    | 'Article'
    | 'BlogPosting'
    | 'NewsArticle'
    | 'AboutPage'
    | 'ContactPage'
    | 'FAQPage'
    | 'Product'
    | 'Service'
    | 'LocalBusiness';

export type ChangeFrequency =
    | 'always'
    | 'hourly'
    | 'daily'
    | 'weekly'
    | 'monthly'
    | 'yearly'
    | 'never';

export interface SiteSeoSettings {
    default_meta_description: string | null;
    default_meta_keywords: string | null;
    og_default_image: string | null;
    twitter_handle: string | null;
    twitter_card_type: 'summary' | 'summary_large_image' | 'app' | 'player';
    organization_name: string | null;
    organization_logo: string | null;
    organization_type: OrganizationType;
    same_as: string[] | null;
    llms_txt_content: string | null;
    llms_crawl_delay: number | null;
    llms_allow_training: boolean;
    robots_txt_content: string | null;
    sitemap_enabled: boolean;
    geo_enabled: boolean;
}

export type OrganizationType =
    | 'Organization'
    | 'Corporation'
    | 'LocalBusiness'
    | 'Restaurant'
    | 'Store'
    | 'MedicalBusiness'
    | 'LegalService'
    | 'RealEstateAgent'
    | 'FinancialService'
    | 'EducationalOrganization'
    | 'GovernmentOrganization'
    | 'NonProfit'
    | 'SportsOrganization';

export interface SiteSettings {
    siteName: string;
    tagline: string | null;
    logoPath: string | null;
    faviconPath: string | null;
    primaryColor: string;
    secondaryColor: string;
    accentColor: string;
    backgroundColor: string;
    textColor: string;
}
