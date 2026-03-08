/**
 * Admin TypeScript type definitions.
 */

import type { User } from './index';
import type { SchemaType, ChangeFrequency, FaqItem } from './seo';

/**
 * Template options that can be configured per-page.
 */
export interface TemplateOptions {
    container_width?: string;
    content_width?: string;
    sidebar_width?: string;
    sidebar_sticky?: boolean;
    hero_full_width?: boolean;
    show_toc?: boolean;
    show_nav?: boolean;
    nav_sticky?: boolean;
    grid_columns?: number;
    vertical_center?: boolean;
}

/**
 * Page model as returned from admin API.
 */
export interface AdminPage {
    id: number;
    title: string;
    slug: string;
    path: string;
    meta_description: string | null;
    meta_keywords: string | null;
    og_title: string | null;
    og_description: string | null;
    og_image: string | null;
    twitter_card_type: 'summary' | 'summary_large_image' | null;
    canonical_url: string | null;
    no_index: boolean;
    no_follow: boolean;
    schema_type: SchemaType | null;
    faqs: FaqItem[] | null;
    speakable_selectors: string[] | null;
    priority: number;
    change_frequency: ChangeFrequency;
    content: ContentBlock[];
    sidebar_content: ContentBlock[] | null;
    draft_content: ContentBlock[] | null;
    draft_sidebar_content: ContentBlock[] | null;
    has_unpublished_changes: boolean;
    template: string;
    template_options: TemplateOptions | null;
    is_published: boolean;
    is_admin_page: boolean;
    show_in_nav: boolean;
    sort_order: number;
    created_by: number | null;
    updated_by: number | null;
    creator?: {
        id: number;
        name: string;
    };
    updater?: {
        id: number;
        name: string;
    };
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

/**
 * Content block for page builder.
 * Using 'any' for data to avoid complex type constraints with Inertia's useForm.
 */
export interface ContentBlock {
    id: string;
    type: string;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    data: Record<string, any>;
}

/**
 * Page component definition.
 */
export interface PageComponent {
    id: number;
    name: string;
    slug: string;
    category: ComponentCategory;
    description: string | null;
    icon: string | null;
    vue_component: string;
    default_props: Record<string, unknown>;
    prop_schema: Record<string, PropSchemaItem>;
    is_active: boolean;
    sort_order: number;
}

/**
 * Component category types.
 */
export type ComponentCategory =
    | 'layout'
    | 'content'
    | 'media'
    | 'navigation'
    | 'forms'
    | 'marketing'
    | 'commerce'
    | 'sidebar'
    | 'product';

/**
 * Property schema item for component props.
 */
export interface PropSchemaItem {
    type: 'text' | 'textarea' | 'richtext' | 'number' | 'boolean' | 'select' | 'image' | 'images' | 'icon' | 'color' | 'repeater' | 'array' | 'object';
    label: string;
    options?: (string | number)[];
    fields?: Record<string, PropSchemaItem>;
}

/**
 * Site settings model.
 */
export interface AdminSiteSettings {
    id: number;
    site_name: string;
    tagline: string | null;
    logo_path: string | null;
    favicon_path: string | null;
    theme: 'default' | 'modern' | 'classic';
    primary_color: string;
    secondary_color: string;
    accent_color: string;
    background_color: string;
    text_color: string;
    heading_font: string | null;
    body_font: string | null;
    contact_email: string | null;
    contact_phone: string | null;
    contact_address: string | null;
    social_links: SocialLinks | null;
    default_meta_description: string | null;
    default_meta_keywords: string | null;
    og_default_image: string | null;
    twitter_handle: string | null;
    twitter_card_type: 'summary' | 'summary_large_image' | null;
    organization_name: string | null;
    organization_logo: string | null;
    organization_type: string | null;
    same_as: string[] | null;
    llms_txt_content: string | null;
    llms_crawl_delay: number | null;
    llms_allow_training: boolean;
    robots_txt_content: string | null;
    sitemap_enabled: boolean;
    geo_enabled: boolean;
    head_scripts: string | null;
    body_scripts: string | null;
    created_at: string;
    updated_at: string;
}

/**
 * Social links configuration.
 */
export interface SocialLinks {
    facebook?: string;
    twitter?: string;
    instagram?: string;
    linkedin?: string;
    youtube?: string;
    tiktok?: string;
    [key: string]: string | undefined;
}

/**
 * Theme preset.
 */
export interface ThemePreset {
    name: string;
    primary_color: string;
    secondary_color: string;
    accent_color: string;
    background_color: string;
    text_color: string;
}

/**
 * Page form data for create/update.
 */
export interface PageFormData {
    title: string;
    slug: string;
    path: string;
    meta_description: string;
    meta_keywords: string;
    og_title: string;
    og_description: string;
    og_image: string;
    twitter_card_type: 'summary' | 'summary_large_image';
    canonical_url: string;
    no_index: boolean;
    no_follow: boolean;
    schema_type: SchemaType;
    faqs: FaqItem[];
    speakable_selectors: string[];
    priority: number;
    change_frequency: ChangeFrequency;
    content: ContentBlock[] | SectionedContent;
    sidebar_content: ContentBlock[];
    template: string;
    template_options: TemplateOptions;
    is_published: boolean;
    is_admin_page: boolean;
    sort_order: number;
}

/**
 * Blog category model.
 */
export interface BlogCategory {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    image: string | null;
    is_featured: boolean;
    sort_order: number;
    posts_count?: number;
    published_posts_count?: number;
    created_at: string;
    updated_at: string;
}

/**
 * Blog post model as returned from admin API.
 */
export interface BlogPost {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    featured_image: string | null;
    category_id: number | null;
    content: ContentBlock[];
    draft_content: ContentBlock[] | null;
    has_unpublished_changes: boolean;
    meta_description: string | null;
    og_title: string | null;
    og_description: string | null;
    og_image: string | null;
    no_index: boolean;
    is_published: boolean;
    is_featured: boolean;
    published_at: string | null;
    author_id: number;
    created_by: number | null;
    updated_by: number | null;
    category?: {
        id: number;
        name: string;
        slug: string;
    };
    author?: {
        id: number;
        name: string;
    };
    creator?: {
        id: number;
        name: string;
    };
    updater?: {
        id: number;
        name: string;
    };
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

/**
 * Blog post form data for create/update.
 */
export interface BlogPostFormData {
    title: string;
    slug: string;
    excerpt: string;
    featured_image: string;
    category_id: number | null;
    content: ContentBlock[];
    meta_description: string;
    og_title: string;
    og_description: string;
    og_image: string;
    no_index: boolean;
    is_published: boolean;
    author_id: number | null;
}

/**
 * Blog category form data for create/update.
 */
export interface BlogCategoryFormData {
    name: string;
    slug: string;
    description: string;
    sort_order: number;
}

/**
 * Product model as returned from admin API.
 */
export interface Product {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    currency: string;
    image: string | null;
    gallery_images: string[] | null;
    content: ContentBlock[] | null;
    draft_content: ContentBlock[] | null;
    has_unpublished_changes: boolean;
    category_id: number | null;
    sync_to_stripe: boolean;
    stripe_product_id: string | null;
    stripe_price_id: string | null;
    is_active: boolean;
    is_featured: boolean;
    type: 'one_time' | 'recurring';
    recurring_interval: 'month' | 'year' | null;
    track_inventory: boolean;
    quantity: number | null;
    created_by: number | null;
    updated_by: number | null;
    category?: {
        id: number;
        name: string;
        slug: string;
    };
    creator?: {
        id: number;
        name: string;
    };
    updater?: {
        id: number;
        name: string;
    };
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

/**
 * Product category model.
 */
export interface ProductCategory {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    image: string | null;
    is_featured: boolean;
    sort_order: number;
    products_count?: number;
    active_products_count?: number;
    created_at: string;
    updated_at: string;
}

/**
 * Product category form data for create/update.
 */
export interface ProductCategoryFormData {
    name: string;
    slug: string;
    description: string;
    image: string;
    is_featured: boolean;
    sort_order: number;
}

/**
 * Product statistics for admin dashboard.
 */
export interface ProductStats {
    total: number;
    active: number;
    synced_to_stripe: number;
    one_time: number;
    recurring: number;
}

/**
 * Product form data for create/update.
 */
export interface ProductFormData {
    name: string;
    slug: string;
    description: string;
    price: number;
    currency: string;
    image: string;
    gallery_images: string[];
    content: ContentBlock[];
    category_id: number | null;
    sync_to_stripe: boolean;
    is_active: boolean;
    is_featured: boolean;
    type: 'one_time' | 'recurring';
    recurring_interval: string;
    track_inventory: boolean;
    quantity: number | null;
}

/**
 * Settings form data.
 */
export interface SettingsFormData {
    site_name: string;
    tagline: string;
    logo_path: string;
    favicon_path: string;
    theme: string;
    primary_color: string;
    secondary_color: string;
    accent_color: string;
    background_color: string;
    text_color: string;
    heading_font: string;
    body_font: string;
    contact_email: string;
    contact_phone: string;
    contact_address: string;
    social_links: SocialLinks;
    default_meta_description: string;
    default_meta_keywords: string;
    og_default_image: string;
    twitter_handle: string;
    twitter_card_type: string;
    organization_name: string;
    organization_logo: string;
    organization_type: string;
    same_as: string[];
    llms_txt_content: string;
    llms_crawl_delay: number;
    llms_allow_training: boolean;
    robots_txt_content: string;
    sitemap_enabled: boolean;
    geo_enabled: boolean;
    head_scripts: string;
    body_scripts: string;
}

/**
 * Template definition from config.
 */
export interface TemplateDefinition {
    slug: string;
    name: string;
    description: string;
    category: 'general' | 'marketing' | 'content' | 'commerce';
    sections: string[];
    options: TemplateOptions;
    preview: string;
    section_definitions?: Record<string, SectionDefinition>;
}

/**
 * Template category definition.
 */
export interface TemplateCategory {
    label: string;
    description: string;
}

/**
 * Template category with templates.
 */
export interface TemplateCategoryGroup {
    label: string;
    description: string;
    templates: Record<string, TemplateDefinition>;
}

/**
 * Full template configuration from backend.
 */
export interface TemplateConfig {
    templates: Record<string, TemplateDefinition>;
    categories: Record<string, TemplateCategory>;
    grouped: Record<string, TemplateCategoryGroup>;
}

/**
 * Section branding options for styling page sections.
 */
export interface SectionBranding {
    backgroundColor: string | null;
    backgroundImage: string | null;
    backgroundOverlay: number;
    textColor: string | null;
    paddingTop: 'none' | 'sm' | 'md' | 'lg' | 'xl';
    paddingBottom: 'none' | 'sm' | 'md' | 'lg' | 'xl';
    borderTop: boolean;
    borderBottom: boolean;
    shadow: 'none' | 'sm' | 'md' | 'lg';
    rounded: 'none' | 'sm' | 'md' | 'lg';
    maxWidth: 'full' | 'max-w-7xl' | 'max-w-6xl' | 'max-w-5xl' | 'max-w-4xl' | 'max-w-3xl';
}

/**
 * A page section containing blocks with branding.
 */
export interface Section {
    id: string;
    slug: string;
    label: string;
    blocks: ContentBlock[];
    branding: SectionBranding;
    isRequired: boolean;
}

/**
 * Default block configuration for section initialization.
 */
export interface DefaultBlockConfig {
    type: string;
    data: Record<string, unknown>;
}

/**
 * Section definition from template configuration.
 */
export interface SectionDefinition {
    label: string;
    description?: string;
    required: boolean;
    defaultBranding: Partial<SectionBranding>;
    defaultBlocks?: DefaultBlockConfig[];
}

/**
 * Content structure with sections (new format).
 */
export interface SectionedContent {
    sections: Section[];
}

/**
 * Type guard to check if content is sectioned.
 */
export type PageContent = ContentBlock[] | SectionedContent;

/**
 * Extended template definition with section definitions.
 */
export interface TemplateDefinitionWithSections extends TemplateDefinition {
    section_definitions?: Record<string, SectionDefinition>;
}
