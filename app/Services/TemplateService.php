<?php

namespace App\Services;

use Illuminate\Support\Arr;

/**
 * Manages page template definitions and configuration.
 */
class TemplateService
{
    /**
     * Get all template definitions.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAll(): array
    {
        $config = config('templates', []);
        $templates = [];

        foreach ($config as $slug => $definition) {
            if ($slug === 'categories') {
                continue;
            }

            $templates[$slug] = [
                'slug' => $slug,
                'name' => Arr::get($definition, 'name', ucfirst($slug)),
                'description' => Arr::get($definition, 'description', ''),
                'category' => Arr::get($definition, 'category', 'general'),
                'sections' => Arr::get($definition, 'sections', ['content']),
                'section_definitions' => Arr::get($definition, 'section_definitions', []),
                'options' => Arr::get($definition, 'options', []),
                'preview' => Arr::get($definition, 'preview', $slug),
            ];
        }

        return $templates;
    }

    /**
     * Get a specific template definition by slug.
     */
    public function get(string $slug): ?array
    {
        $templates = $this->getAll();

        return Arr::get($templates, $slug);
    }

    /**
     * Get all template slugs for validation.
     *
     * @return array<int, string>
     */
    public function getSlugs(): array
    {
        return array_keys($this->getAll());
    }

    /**
     * Get template categories.
     *
     * @return array<string, array<string, string>>
     */
    public function getCategories(): array
    {
        return config('templates.categories', [
            'general' => [
                'label' => 'General',
                'description' => 'Standard layouts for most pages.',
            ],
            'marketing' => [
                'label' => 'Marketing',
                'description' => 'Layouts optimized for landing pages.',
            ],
            'content' => [
                'label' => 'Content',
                'description' => 'Layouts optimized for reading.',
            ],
        ]);
    }

    /**
     * Get templates grouped by category.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getGroupedByCategory(): array
    {
        $templates = $this->getAll();
        $categories = $this->getCategories();
        $grouped = [];

        foreach ($categories as $categorySlug => $categoryInfo) {
            $grouped[$categorySlug] = [
                'label' => Arr::get($categoryInfo, 'label', ucfirst($categorySlug)),
                'description' => Arr::get($categoryInfo, 'description', ''),
                'templates' => [],
            ];
        }

        foreach ($templates as $slug => $template) {
            $category = Arr::get($template, 'category', 'general');
            if (isset($grouped[$category])) {
                $grouped[$category]['templates'][$slug] = $template;
            }
        }

        return $grouped;
    }

    /**
     * Get the default options for a template.
     *
     * @return array<string, mixed>
     */
    public function getDefaultOptions(string $slug): array
    {
        $template = $this->get($slug);

        return Arr::get($template, 'options', []);
    }

    /**
     * Check if a template slug is valid.
     */
    public function isValid(string $slug): bool
    {
        return in_array($slug, $this->getSlugs(), true);
    }

    /**
     * Get template data formatted for frontend use.
     *
     * @return array<string, mixed>
     */
    public function getForFrontend(): array
    {
        return [
            'templates' => $this->getAll(),
            'categories' => $this->getCategories(),
            'grouped' => $this->getGroupedByCategory(),
        ];
    }

    /**
     * Check if a template has sidebar support.
     */
    public function hasSidebar(string $slug): bool
    {
        $template = $this->get($slug);
        $sections = Arr::get($template, 'sections', []);

        return in_array('sidebar', $sections, true);
    }

    /**
     * Check if a template has configurable options.
     */
    public function hasOptions(string $slug): bool
    {
        $template = $this->get($slug);
        $options = Arr::get($template, 'options', []);

        return count($options) > 0;
    }

    /**
     * Get section definitions for a template.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getSectionDefinitions(string $slug): array
    {
        $template = $this->get($slug);

        return Arr::get($template, 'section_definitions', []);
    }

    /**
     * Check if a template has section definitions.
     */
    public function hasSectionDefinitions(string $slug): bool
    {
        $definitions = $this->getSectionDefinitions($slug);

        return count($definitions) > 0;
    }

    /**
     * Get default branding values for a section.
     *
     * @return array<string, mixed>
     */
    public function getDefaultBranding(): array
    {
        return [
            'backgroundColor' => null,
            'backgroundImage' => null,
            'backgroundOverlay' => 0,
            'textColor' => null,
            'paddingTop' => 'md',
            'paddingBottom' => 'md',
            'borderTop' => false,
            'borderBottom' => false,
            'shadow' => 'none',
            'rounded' => 'none',
            'maxWidth' => 'max-w-7xl',
        ];
    }

    /**
     * Initialize sections for a template.
     *
     * @return array<int, array<string, mixed>>
     */
    public function initializeSections(string $slug): array
    {
        $definitions = $this->getSectionDefinitions($slug);
        $sections = [];

        foreach ($definitions as $sectionSlug => $definition) {
            $defaultBranding = $this->getDefaultBranding();
            $customBranding = Arr::get($definition, 'defaultBranding', []);

            // Initialize blocks with unique IDs
            $blocks = [];
            $defaultBlocks = Arr::get($definition, 'defaultBlocks', []);
            foreach ($defaultBlocks as $block) {
                $blocks[] = [
                    'id' => 'block_' . uniqid(),
                    'type' => Arr::get($block, 'type', ''),
                    'data' => Arr::get($block, 'data', []),
                ];
            }

            $sections[] = [
                'id' => 'section_' . uniqid(),
                'slug' => $sectionSlug,
                'label' => Arr::get($definition, 'label', ucfirst($sectionSlug)),
                'blocks' => $blocks,
                'branding' => array_merge($defaultBranding, $customBranding),
                'isRequired' => Arr::get($definition, 'required', false),
            ];
        }

        return $sections;
    }

    /**
     * Get template data formatted for frontend use including section definitions.
     *
     * @return array<string, mixed>
     */
    public function getForFrontendWithSections(): array
    {
        $data = $this->getForFrontend();

        // Add section_definitions to each template
        foreach ($data['templates'] as $slug => &$template) {
            $template['section_definitions'] = $this->getSectionDefinitions($slug);
        }

        foreach ($data['grouped'] as $category => &$categoryData) {
            foreach ($categoryData['templates'] as $slug => &$template) {
                $template['section_definitions'] = $this->getSectionDefinitions($slug);
            }
        }

        return $data;
    }
}
