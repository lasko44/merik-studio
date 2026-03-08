<?php

namespace App\Services;

use App\Models\SiteSettings;
use App\Models\Page;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Arr;

/**
 * Handles AI-powered content generation using OpenAI.
 */
class AiGenerationService
{
    /**
     * Generate llms.txt content based on site information.
     */
    public function generateLlmsTxt(SiteSettings $settings): string
    {
        $siteContext = $this->buildSiteContext($settings);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert in AI crawler configurations and llms.txt format. Generate well-structured llms.txt content that helps AI crawlers understand a website. Use markdown format with clear sections.',
                ],
                [
                    'role' => 'user',
                    'content' => $this->buildLlmsTxtPrompt($siteContext),
                ],
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);

        return trim(Arr::get($response, 'choices.0.message.content', ''));
    }

    /**
     * Generate robots.txt content based on site information.
     */
    public function generateRobotsTxt(SiteSettings $settings): string
    {
        $siteContext = $this->buildSiteContext($settings);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert in SEO and robots.txt configuration. Generate a well-structured robots.txt file that follows best practices. Output only the robots.txt content, no explanations.',
                ],
                [
                    'role' => 'user',
                    'content' => $this->buildRobotsTxtPrompt($siteContext),
                ],
            ],
            'temperature' => 0.3,
            'max_tokens' => 500,
        ]);

        return trim(Arr::get($response, 'choices.0.message.content', ''));
    }

    /**
     * Generate meta description based on site information.
     */
    public function generateMetaDescription(SiteSettings $settings): string
    {
        $siteContext = $this->buildSiteContext($settings);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an SEO expert. Generate a compelling meta description that is between 150-160 characters. The description should be engaging and include relevant keywords naturally. Output only the meta description text, no quotes or explanations.',
                ],
                [
                    'role' => 'user',
                    'content' => $this->buildMetaDescriptionPrompt($siteContext),
                ],
            ],
            'temperature' => 0.7,
            'max_tokens' => 100,
        ]);

        return trim(Arr::get($response, 'choices.0.message.content', ''));
    }

    /**
     * Generate meta keywords based on site information.
     */
    public function generateMetaKeywords(SiteSettings $settings): string
    {
        $siteContext = $this->buildSiteContext($settings);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an SEO expert. Generate a comma-separated list of relevant keywords for a website. Include 8-12 keywords that are relevant to the business. Output only the keywords, no explanations.',
                ],
                [
                    'role' => 'user',
                    'content' => $this->buildMetaKeywordsPrompt($siteContext),
                ],
            ],
            'temperature' => 0.7,
            'max_tokens' => 150,
        ]);

        return trim(Arr::get($response, 'choices.0.message.content', ''));
    }

    /**
     * Build site context from settings for AI prompts.
     */
    protected function buildSiteContext(SiteSettings $settings): array
    {
        $pages = Page::where('is_published', true)
            ->select(['title', 'slug', 'meta_description'])
            ->get();

        $pageList = $pages->map(fn ($page) => [
            'title' => $page->title,
            'path' => $page->slug === 'home' ? '/' : '/' . $page->slug,
            'description' => $page->meta_description,
        ])->toArray();

        return [
            'site_name' => $settings->site_name,
            'tagline' => $settings->tagline,
            'organization_name' => $settings->organization_name,
            'organization_type' => $settings->organization_type,
            'contact_email' => $settings->contact_email,
            'contact_phone' => $settings->contact_phone,
            'contact_address' => $settings->contact_address,
            'pages' => $pageList,
            'has_blog' => config('cms.blog_enabled', false),
        ];
    }

    /**
     * Build prompt for llms.txt generation.
     */
    protected function buildLlmsTxtPrompt(array $context): string
    {
        $siteName = Arr::get($context, 'site_name', 'Website');
        $tagline = Arr::get($context, 'tagline', '');
        $orgName = Arr::get($context, 'organization_name', $siteName);
        $orgType = Arr::get($context, 'organization_type', 'Organization');
        $pages = Arr::get($context, 'pages', []);

        $pagesList = '';
        foreach ($pages as $page) {
            $pagesList .= "- {$page['title']} ({$page['path']})\n";
        }

        return <<<PROMPT
Generate an llms.txt file for a website with the following information:

Site Name: {$siteName}
Tagline: {$tagline}
Organization: {$orgName} ({$orgType})

Available Pages:
{$pagesList}

The llms.txt should include:
1. A clear title and description of the website
2. Guidelines for AI assistants about how to reference and interact with the site
3. Contact information if available
4. Key topics and content areas covered
5. Any special instructions for AI crawlers

Format it in markdown with clear headings.
PROMPT;
    }

    /**
     * Build prompt for robots.txt generation.
     */
    protected function buildRobotsTxtPrompt(array $context): string
    {
        $siteName = Arr::get($context, 'site_name', 'Website');
        $hasBlog = Arr::get($context, 'has_blog', false);

        $features = ['pages'];
        if ($hasBlog) {
            $features[] = 'blog';
        }

        $featuresList = implode(', ', $features);

        return <<<PROMPT
Generate a robots.txt file for a website called "{$siteName}" with the following features: {$featuresList}.

The robots.txt should:
1. Allow all legitimate search engine crawlers
2. Block access to admin areas (/admin/*)
3. Block access to api routes (/api/*)
4. Include a sitemap reference at /sitemap.xml
5. Follow SEO best practices
6. Be well-commented

Output only the robots.txt content, ready to be used directly.
PROMPT;
    }

    /**
     * Build prompt for meta description generation.
     */
    protected function buildMetaDescriptionPrompt(array $context): string
    {
        $siteName = Arr::get($context, 'site_name', 'Website');
        $tagline = Arr::get($context, 'tagline', '');
        $orgName = Arr::get($context, 'organization_name', $siteName);
        $orgType = Arr::get($context, 'organization_type', 'Organization');

        return <<<PROMPT
Generate a compelling meta description for a website with the following information:

Site Name: {$siteName}
Tagline: {$tagline}
Organization: {$orgName}
Organization Type: {$orgType}

The meta description should:
1. Be between 150-160 characters
2. Include the organization name or site name
3. Be engaging and encourage clicks
4. Naturally incorporate relevant keywords
5. Describe what visitors can expect

Output only the meta description text.
PROMPT;
    }

    /**
     * Build prompt for meta keywords generation.
     */
    protected function buildMetaKeywordsPrompt(array $context): string
    {
        $siteName = Arr::get($context, 'site_name', 'Website');
        $tagline = Arr::get($context, 'tagline', '');
        $orgType = Arr::get($context, 'organization_type', 'Organization');
        $pages = Arr::get($context, 'pages', []);

        $pagesList = '';
        foreach ($pages as $page) {
            $pagesList .= "- {$page['title']}\n";
        }

        return <<<PROMPT
Generate relevant SEO keywords for a website with the following information:

Site Name: {$siteName}
Tagline: {$tagline}
Organization Type: {$orgType}

Pages on the site:
{$pagesList}

Generate 8-12 comma-separated keywords that:
1. Are relevant to the business/organization type
2. Include the site/organization name
3. Include service or product-related terms
4. Include location-based keywords if applicable
5. Mix short-tail and long-tail keywords

Output only the comma-separated keywords.
PROMPT;
    }
}
