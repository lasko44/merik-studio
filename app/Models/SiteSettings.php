<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Manages site-wide settings including branding and theming.
 */
class SiteSettings extends Model
{
    protected $fillable = [
        'site_name',
        'tagline',
        'logo_path',
        'show_site_name_in_nav',
        'nav_logo_height',
        'favicon_path',
        'theme',
        'wizard_data',
        'primary_color',
        'secondary_color',
        'accent_color',
        'background_color',
        'text_color',
        'success_color',
        'warning_color',
        'error_color',
        'info_color',
        'surface_color',
        'border_color',
        'muted_color',
        'heading_font',
        'body_font',
        'social_links',
        'contact_email',
        'contact_phone',
        'contact_address',
        'default_meta_description',
        'default_meta_keywords',
        'og_default_image',
        'twitter_handle',
        'twitter_card_type',
        'organization_name',
        'organization_logo',
        'organization_type',
        'same_as',
        'llms_txt_content',
        'llms_crawl_delay',
        'llms_allow_training',
        'robots_txt_content',
        'sitemap_enabled',
        'geo_enabled',
        'head_scripts',
        'body_scripts',
        'stripe_publishable_key',
        'stripe_secret_key',
        'stripe_webhook_secret',
        'stripe_test_mode',
        'blog_show_in_nav',
        'blog_nav_label',
        'blog_enable_search',
        'blog_enable_category_filter',
        'blog_nav_order',
        'products_show_in_nav',
        'products_nav_label',
        'products_nav_order',
        'products_enable_search',
        'products_enable_category_filter',
    ];

    protected $casts = [
        'social_links' => 'array',
        'same_as' => 'array',
        'wizard_data' => 'array',
        'show_site_name_in_nav' => 'boolean',
        'llms_allow_training' => 'boolean',
        'sitemap_enabled' => 'boolean',
        'geo_enabled' => 'boolean',
        'stripe_secret_key' => 'encrypted',
        'stripe_webhook_secret' => 'encrypted',
        'stripe_test_mode' => 'boolean',
        'blog_show_in_nav' => 'boolean',
        'blog_enable_search' => 'boolean',
        'blog_enable_category_filter' => 'boolean',
        'blog_nav_order' => 'integer',
        'products_show_in_nav' => 'boolean',
        'products_enable_search' => 'boolean',
        'products_enable_category_filter' => 'boolean',
        'products_nav_order' => 'integer',
    ];

    /**
     * Available themes with their default color schemes.
     */
    public const THEMES = [
        'default' => [
            'name' => 'Default',
            'primary_color' => '#3B82F6',
            'secondary_color' => '#10B981',
            'accent_color' => '#F59E0B',
            'background_color' => '#FFFFFF',
            'text_color' => '#1F2937',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'error_color' => '#EF4444',
            'info_color' => '#3B82F6',
            'surface_color' => '#FFFFFF',
            'border_color' => '#E5E7EB',
            'muted_color' => '#6B7280',
        ],
        'modern' => [
            'name' => 'Modern',
            'primary_color' => '#6366F1',
            'secondary_color' => '#EC4899',
            'accent_color' => '#14B8A6',
            'background_color' => '#F9FAFB',
            'text_color' => '#111827',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'error_color' => '#EF4444',
            'info_color' => '#6366F1',
            'surface_color' => '#FFFFFF',
            'border_color' => '#E5E7EB',
            'muted_color' => '#6B7280',
        ],
        'classic' => [
            'name' => 'Classic',
            'primary_color' => '#1E40AF',
            'secondary_color' => '#059669',
            'accent_color' => '#D97706',
            'background_color' => '#FFFBEB',
            'text_color' => '#292524',
            'success_color' => '#059669',
            'warning_color' => '#D97706',
            'error_color' => '#DC2626',
            'info_color' => '#1E40AF',
            'surface_color' => '#FFFFFF',
            'border_color' => '#D6D3D1',
            'muted_color' => '#78716C',
        ],
        'ocean' => [
            'name' => 'Ocean',
            'primary_color' => '#0891B2',
            'secondary_color' => '#0D9488',
            'accent_color' => '#F97316',
            'background_color' => '#F0FDFA',
            'text_color' => '#134E4A',
            'success_color' => '#0D9488',
            'warning_color' => '#F97316',
            'error_color' => '#EF4444',
            'info_color' => '#0891B2',
            'surface_color' => '#FFFFFF',
            'border_color' => '#99F6E4',
            'muted_color' => '#5EEAD4',
        ],
        'forest' => [
            'name' => 'Forest',
            'primary_color' => '#16A34A',
            'secondary_color' => '#65A30D',
            'accent_color' => '#CA8A04',
            'background_color' => '#F7FEE7',
            'text_color' => '#14532D',
            'success_color' => '#16A34A',
            'warning_color' => '#CA8A04',
            'error_color' => '#DC2626',
            'info_color' => '#0891B2',
            'surface_color' => '#FFFFFF',
            'border_color' => '#BEF264',
            'muted_color' => '#65A30D',
        ],
        'sunset' => [
            'name' => 'Sunset',
            'primary_color' => '#EA580C',
            'secondary_color' => '#DC2626',
            'accent_color' => '#FACC15',
            'background_color' => '#FFF7ED',
            'text_color' => '#431407',
            'success_color' => '#16A34A',
            'warning_color' => '#FACC15',
            'error_color' => '#DC2626',
            'info_color' => '#EA580C',
            'surface_color' => '#FFFFFF',
            'border_color' => '#FDBA74',
            'muted_color' => '#9A3412',
        ],
        'royal' => [
            'name' => 'Royal',
            'primary_color' => '#7C3AED',
            'secondary_color' => '#A855F7',
            'accent_color' => '#F472B6',
            'background_color' => '#FAF5FF',
            'text_color' => '#3B0764',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'error_color' => '#EF4444',
            'info_color' => '#7C3AED',
            'surface_color' => '#FFFFFF',
            'border_color' => '#DDD6FE',
            'muted_color' => '#A78BFA',
        ],
        'midnight' => [
            'name' => 'Midnight',
            'primary_color' => '#4F46E5',
            'secondary_color' => '#6366F1',
            'accent_color' => '#22D3EE',
            'background_color' => '#0F172A',
            'text_color' => '#E2E8F0',
            'success_color' => '#34D399',
            'warning_color' => '#FBBF24',
            'error_color' => '#F87171',
            'info_color' => '#60A5FA',
            'surface_color' => '#1E293B',
            'border_color' => '#334155',
            'muted_color' => '#94A3B8',
        ],
        'rose' => [
            'name' => 'Rose',
            'primary_color' => '#E11D48',
            'secondary_color' => '#DB2777',
            'accent_color' => '#F59E0B',
            'background_color' => '#FFF1F2',
            'text_color' => '#4C0519',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'error_color' => '#E11D48',
            'info_color' => '#3B82F6',
            'surface_color' => '#FFFFFF',
            'border_color' => '#FECDD3',
            'muted_color' => '#FB7185',
        ],
        'slate' => [
            'name' => 'Slate',
            'primary_color' => '#475569',
            'secondary_color' => '#64748B',
            'accent_color' => '#0EA5E9',
            'background_color' => '#F8FAFC',
            'text_color' => '#1E293B',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'error_color' => '#EF4444',
            'info_color' => '#0EA5E9',
            'surface_color' => '#FFFFFF',
            'border_color' => '#CBD5E1',
            'muted_color' => '#94A3B8',
        ],
        'emerald' => [
            'name' => 'Emerald',
            'primary_color' => '#059669',
            'secondary_color' => '#10B981',
            'accent_color' => '#8B5CF6',
            'background_color' => '#ECFDF5',
            'text_color' => '#064E3B',
            'success_color' => '#059669',
            'warning_color' => '#F59E0B',
            'error_color' => '#EF4444',
            'info_color' => '#3B82F6',
            'surface_color' => '#FFFFFF',
            'border_color' => '#A7F3D0',
            'muted_color' => '#34D399',
        ],
        'coral' => [
            'name' => 'Coral',
            'primary_color' => '#F97316',
            'secondary_color' => '#FB923C',
            'accent_color' => '#EC4899',
            'background_color' => '#FFF7ED',
            'text_color' => '#7C2D12',
            'success_color' => '#10B981',
            'warning_color' => '#FBBF24',
            'error_color' => '#EF4444',
            'info_color' => '#3B82F6',
            'surface_color' => '#FFFFFF',
            'border_color' => '#FED7AA',
            'muted_color' => '#FDBA74',
        ],
        'arctic' => [
            'name' => 'Arctic',
            'primary_color' => '#0EA5E9',
            'secondary_color' => '#38BDF8',
            'accent_color' => '#A855F7',
            'background_color' => '#F0F9FF',
            'text_color' => '#0C4A6E',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'error_color' => '#EF4444',
            'info_color' => '#0EA5E9',
            'surface_color' => '#FFFFFF',
            'border_color' => '#BAE6FD',
            'muted_color' => '#7DD3FC',
        ],
        'lavender' => [
            'name' => 'Lavender',
            'primary_color' => '#A855F7',
            'secondary_color' => '#C084FC',
            'accent_color' => '#F472B6',
            'background_color' => '#FAF5FF',
            'text_color' => '#581C87',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'error_color' => '#EF4444',
            'info_color' => '#A855F7',
            'surface_color' => '#FFFFFF',
            'border_color' => '#E9D5FF',
            'muted_color' => '#D8B4FE',
        ],
        'copper' => [
            'name' => 'Copper',
            'primary_color' => '#B45309',
            'secondary_color' => '#D97706',
            'accent_color' => '#059669',
            'background_color' => '#FFFBEB',
            'text_color' => '#78350F',
            'success_color' => '#059669',
            'warning_color' => '#D97706',
            'error_color' => '#DC2626',
            'info_color' => '#0891B2',
            'surface_color' => '#FFFFFF',
            'border_color' => '#FDE68A',
            'muted_color' => '#FBBF24',
        ],
        'sage' => [
            'name' => 'Sage',
            'primary_color' => '#65A30D',
            'secondary_color' => '#84CC16',
            'accent_color' => '#0891B2',
            'background_color' => '#F7FEE7',
            'text_color' => '#365314',
            'success_color' => '#65A30D',
            'warning_color' => '#EAB308',
            'error_color' => '#DC2626',
            'info_color' => '#0891B2',
            'surface_color' => '#FFFFFF',
            'border_color' => '#D9F99D',
            'muted_color' => '#BEF264',
        ],
        'berry' => [
            'name' => 'Berry',
            'primary_color' => '#BE185D',
            'secondary_color' => '#DB2777',
            'accent_color' => '#7C3AED',
            'background_color' => '#FDF2F8',
            'text_color' => '#831843',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'error_color' => '#E11D48',
            'info_color' => '#3B82F6',
            'surface_color' => '#FFFFFF',
            'border_color' => '#FBCFE8',
            'muted_color' => '#F9A8D4',
        ],
        'sand' => [
            'name' => 'Sand',
            'primary_color' => '#A8A29E',
            'secondary_color' => '#D6D3D1',
            'accent_color' => '#0891B2',
            'background_color' => '#FAFAF9',
            'text_color' => '#44403C',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'error_color' => '#EF4444',
            'info_color' => '#0891B2',
            'surface_color' => '#FFFFFF',
            'border_color' => '#E7E5E4',
            'muted_color' => '#A8A29E',
        ],
        'steel' => [
            'name' => 'Steel',
            'primary_color' => '#3B82F6',
            'secondary_color' => '#60A5FA',
            'accent_color' => '#F97316',
            'background_color' => '#F1F5F9',
            'text_color' => '#1E3A5F',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'error_color' => '#EF4444',
            'info_color' => '#3B82F6',
            'surface_color' => '#FFFFFF',
            'border_color' => '#CBD5E1',
            'muted_color' => '#94A3B8',
        ],
        'autumn' => [
            'name' => 'Autumn',
            'primary_color' => '#C2410C',
            'secondary_color' => '#EA580C',
            'accent_color' => '#CA8A04',
            'background_color' => '#FEF3C7',
            'text_color' => '#78350F',
            'success_color' => '#16A34A',
            'warning_color' => '#CA8A04',
            'error_color' => '#DC2626',
            'info_color' => '#0891B2',
            'surface_color' => '#FFFBEB',
            'border_color' => '#FDE68A',
            'muted_color' => '#D97706',
        ],
        'monochrome' => [
            'name' => 'Monochrome',
            'primary_color' => '#171717',
            'secondary_color' => '#404040',
            'accent_color' => '#737373',
            'background_color' => '#FAFAFA',
            'text_color' => '#171717',
            'success_color' => '#171717',
            'warning_color' => '#525252',
            'error_color' => '#171717',
            'info_color' => '#404040',
            'surface_color' => '#FFFFFF',
            'border_color' => '#E5E5E5',
            'muted_color' => '#A3A3A3',
        ],
    ];

    /**
     * Available fonts for typography.
     */
    public const FONTS = [
        // Sans-serif fonts
        'Inter' => [
            'name' => 'Inter',
            'category' => 'sans-serif',
            'google' => true,
            'weights' => '400;500;600;700',
        ],
        'Roboto' => [
            'name' => 'Roboto',
            'category' => 'sans-serif',
            'google' => true,
            'weights' => '400;500;700',
        ],
        'Open Sans' => [
            'name' => 'Open Sans',
            'category' => 'sans-serif',
            'google' => true,
            'weights' => '400;500;600;700',
        ],
        'Poppins' => [
            'name' => 'Poppins',
            'category' => 'sans-serif',
            'google' => true,
            'weights' => '400;500;600;700',
        ],
        'Montserrat' => [
            'name' => 'Montserrat',
            'category' => 'sans-serif',
            'google' => true,
            'weights' => '400;500;600;700',
        ],
        'Lato' => [
            'name' => 'Lato',
            'category' => 'sans-serif',
            'google' => true,
            'weights' => '400;700',
        ],
        'Nunito' => [
            'name' => 'Nunito',
            'category' => 'sans-serif',
            'google' => true,
            'weights' => '400;500;600;700',
        ],
        'Raleway' => [
            'name' => 'Raleway',
            'category' => 'sans-serif',
            'google' => true,
            'weights' => '400;500;600;700',
        ],
        'Work Sans' => [
            'name' => 'Work Sans',
            'category' => 'sans-serif',
            'google' => true,
            'weights' => '400;500;600;700',
        ],
        'DM Sans' => [
            'name' => 'DM Sans',
            'category' => 'sans-serif',
            'google' => true,
            'weights' => '400;500;700',
        ],
        'Plus Jakarta Sans' => [
            'name' => 'Plus Jakarta Sans',
            'category' => 'sans-serif',
            'google' => true,
            'weights' => '400;500;600;700',
        ],
        'Space Grotesk' => [
            'name' => 'Space Grotesk',
            'category' => 'sans-serif',
            'google' => true,
            'weights' => '400;500;600;700',
        ],
        // Serif fonts
        'Playfair Display' => [
            'name' => 'Playfair Display',
            'category' => 'serif',
            'google' => true,
            'weights' => '400;500;600;700',
        ],
        'Merriweather' => [
            'name' => 'Merriweather',
            'category' => 'serif',
            'google' => true,
            'weights' => '400;700',
        ],
        'Lora' => [
            'name' => 'Lora',
            'category' => 'serif',
            'google' => true,
            'weights' => '400;500;600;700',
        ],
        'Source Serif Pro' => [
            'name' => 'Source Serif Pro',
            'category' => 'serif',
            'google' => true,
            'weights' => '400;600;700',
        ],
        'Libre Baskerville' => [
            'name' => 'Libre Baskerville',
            'category' => 'serif',
            'google' => true,
            'weights' => '400;700',
        ],
        'Crimson Pro' => [
            'name' => 'Crimson Pro',
            'category' => 'serif',
            'google' => true,
            'weights' => '400;500;600;700',
        ],
        'DM Serif Display' => [
            'name' => 'DM Serif Display',
            'category' => 'serif',
            'google' => true,
            'weights' => '400',
        ],
        // Monospace fonts
        'JetBrains Mono' => [
            'name' => 'JetBrains Mono',
            'category' => 'monospace',
            'google' => true,
            'weights' => '400;500;700',
        ],
        'Fira Code' => [
            'name' => 'Fira Code',
            'category' => 'monospace',
            'google' => true,
            'weights' => '400;500;700',
        ],
        'Source Code Pro' => [
            'name' => 'Source Code Pro',
            'category' => 'monospace',
            'google' => true,
            'weights' => '400;500;700',
        ],
        // System fonts
        'System UI' => [
            'name' => 'System UI',
            'category' => 'system',
            'google' => false,
            'value' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
        ],
    ];

    /**
     * Get the current site settings (singleton pattern).
     */
    public static function current(): self
    {
        return Cache::rememberForever('site_settings', function () {
            return static::firstOrCreate([], [
                'site_name' => config('app.name', 'Merik CMS'),
            ]);
        });
    }

    /**
     * Clear the cached settings.
     */
    public static function clearCache(): void
    {
        Cache::forget('site_settings');
    }

    /**
     * Get available theme names.
     */
    public static function availableThemes(): array
    {
        return array_keys(self::THEMES);
    }

    /**
     * Get theme details by name.
     */
    public static function getTheme(string $name): ?array
    {
        return self::THEMES[$name] ?? null;
    }

    /**
     * Apply theme defaults to the settings.
     */
    public function applyTheme(string $themeName): bool
    {
        $theme = self::getTheme($themeName);

        if (!$theme) {
            return false;
        }

        $this->update([
            'theme' => $themeName,
            'primary_color' => $theme['primary_color'],
            'secondary_color' => $theme['secondary_color'],
            'accent_color' => $theme['accent_color'],
            'background_color' => $theme['background_color'],
            'text_color' => $theme['text_color'],
            'success_color' => $theme['success_color'],
            'warning_color' => $theme['warning_color'],
            'error_color' => $theme['error_color'],
            'info_color' => $theme['info_color'],
            'surface_color' => $theme['surface_color'],
            'border_color' => $theme['border_color'],
            'muted_color' => $theme['muted_color'],
        ]);

        self::clearCache();

        return true;
    }

    /**
     * Get CSS custom properties for the current theme.
     */
    public function getCssVariables(): array
    {
        return [
            '--color-primary' => $this->primary_color,
            '--color-secondary' => $this->secondary_color,
            '--color-accent' => $this->accent_color,
            '--color-background' => $this->background_color,
            '--color-text' => $this->text_color,
            '--color-success' => $this->success_color,
            '--color-warning' => $this->warning_color,
            '--color-error' => $this->error_color,
            '--color-info' => $this->info_color,
            '--color-surface' => $this->surface_color,
            '--color-border' => $this->border_color,
            '--color-muted' => $this->muted_color,
            '--font-heading' => $this->heading_font,
            '--font-body' => $this->body_font,
        ];
    }

    /**
     * Generate inline CSS styles for theme variables.
     */
    public function getInlineStyles(): string
    {
        $variables = $this->getCssVariables();
        $styles = [];

        foreach ($variables as $property => $value) {
            $styles[] = "{$property}: {$value}";
        }

        return ':root { ' . implode('; ', $styles) . ' }';
    }

    /**
     * Boot method to clear cache on save.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            self::clearCache();
        });
    }
}
