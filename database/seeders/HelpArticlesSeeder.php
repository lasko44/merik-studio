<?php

namespace Database\Seeders;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Database\Seeder;

/**
 * Seeds the help center with CMS documentation.
 */
class HelpArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Getting Started
        $gettingStarted = HelpCategory::create([
            'name' => 'Getting Started',
            'slug' => 'getting-started',
            'icon' => 'book',
            'description' => 'Learn the basics of your CMS and get up and running quickly.',
            'sort_order' => 1,
        ]);

        $this->createArticle($gettingStarted, 1, 'welcome-to-your-cms', 'Welcome to Your CMS',
            'An introduction to your content management system and its key features.',
            $this->getWelcomeContent()
        );

        $this->createArticle($gettingStarted, 2, 'dashboard-overview', 'Dashboard Overview',
            'Learn how to navigate and use your admin dashboard.',
            $this->getDashboardContent()
        );

        $this->createArticle($gettingStarted, 3, 'your-first-page', 'Creating Your First Page',
            'Step-by-step guide to creating and publishing your first page.',
            $this->getFirstPageContent()
        );

        // Pages & Content
        $pagesContent = HelpCategory::create([
            'name' => 'Pages & Content',
            'slug' => 'pages-content',
            'icon' => 'document',
            'description' => 'Create, edit, and manage your website pages and content.',
            'sort_order' => 2,
        ]);

        $this->createArticle($pagesContent, 1, 'managing-pages', 'Managing Pages',
            'Learn how to create, edit, duplicate, and delete pages.',
            $this->getManagingPagesContent()
        );

        $this->createArticle($pagesContent, 2, 'page-builder', 'Using the Page Builder',
            'Learn how to use the drag-and-drop page builder to create beautiful pages.',
            $this->getPageBuilderContent()
        );

        $this->createArticle($pagesContent, 3, 'page-settings', 'Page Settings',
            'Configure page URLs, templates, visibility, and other settings.',
            $this->getPageSettingsContent()
        );

        // SEO & Marketing
        $seoMarketing = HelpCategory::create([
            'name' => 'SEO & Marketing',
            'slug' => 'seo-marketing',
            'icon' => 'chart',
            'description' => 'Optimize your site for search engines and AI assistants.',
            'sort_order' => 3,
        ]);

        $this->createArticle($seoMarketing, 1, 'seo-basics', 'SEO Basics',
            'Understanding search engine optimization and why it matters.',
            $this->getSeoBasicsContent()
        );

        $this->createArticle($seoMarketing, 2, 'page-seo-settings', 'Page SEO Settings',
            'Configure meta tags, Open Graph, and structured data for each page.',
            $this->getPageSeoContent()
        );

        $this->createArticle($seoMarketing, 3, 'social-media-sharing', 'Social Media Sharing',
            'Optimize how your pages appear when shared on social media.',
            $this->getSocialSharingContent()
        );

        $this->createArticle($seoMarketing, 4, 'ai-search-optimization', 'AI Search Optimization (GEO)',
            'Learn how to optimize your content for AI assistants like ChatGPT and Claude.',
            $this->getGeoContent()
        );

        // Branding & Design
        $brandingDesign = HelpCategory::create([
            'name' => 'Branding & Design',
            'slug' => 'branding-design',
            'icon' => 'cog',
            'description' => 'Customize your site\'s appearance with themes, colors, and branding.',
            'sort_order' => 4,
        ]);

        $this->createArticle($brandingDesign, 1, 'site-branding', 'Site Branding',
            'Upload your logo, favicon, and set your site name and tagline.',
            $this->getSiteBrandingContent()
        );

        $this->createArticle($brandingDesign, 2, 'themes-colors', 'Themes & Colors',
            'Choose a theme and customize your site\'s color scheme.',
            $this->getThemesColorsContent()
        );

        $this->createArticle($brandingDesign, 3, 'typography', 'Typography',
            'Select fonts for headings and body text.',
            $this->getTypographyContent()
        );

        // User Management
        $userManagement = HelpCategory::create([
            'name' => 'User Management',
            'slug' => 'user-management',
            'icon' => 'users',
            'description' => 'Manage users, roles, and permissions for your site.',
            'sort_order' => 5,
        ]);

        $this->createArticle($userManagement, 1, 'understanding-roles', 'Understanding User Roles',
            'Learn about the different user roles and what each can do.',
            $this->getUserRolesContent()
        );

        $this->createArticle($userManagement, 2, 'inviting-users', 'Inviting Users',
            'How to invite new users to help manage your site.',
            $this->getInvitingUsersContent()
        );

        $this->createArticle($userManagement, 3, 'managing-users', 'Managing Users',
            'Edit user details, change roles, and remove users.',
            $this->getManagingUsersContent()
        );

        // Blog
        $blog = HelpCategory::create([
            'name' => 'Blog',
            'slug' => 'blog',
            'icon' => 'document',
            'description' => 'Create and manage blog posts to engage your audience.',
            'sort_order' => 6,
        ]);

        $this->createArticle($blog, 1, 'creating-blog-posts', 'Creating Blog Posts',
            'Learn how to write and publish blog posts.',
            $this->getCreatingBlogPostsContent()
        );

        $this->createArticle($blog, 2, 'blog-categories', 'Blog Categories',
            'Organize your posts with categories.',
            $this->getBlogCategoriesContent()
        );

        $this->createArticle($blog, 3, 'blog-seo', 'Blog SEO & Featured Images',
            'Optimize your blog posts for search engines.',
            $this->getBlogSeoContent()
        );

        // Navigation
        $navigation = HelpCategory::create([
            'name' => 'Navigation',
            'slug' => 'navigation',
            'icon' => 'globe',
            'description' => 'Configure your site\'s navigation menus and structure.',
            'sort_order' => 7,
        ]);

        $this->createArticle($navigation, 1, 'managing-navigation', 'Managing Navigation',
            'Create and organize your site\'s navigation menu.',
            $this->getManagingNavigationContent()
        );

        $this->createArticle($navigation, 2, 'navigation-best-practices', 'Navigation Best Practices',
            'Design effective navigation for better user experience.',
            $this->getNavigationBestPracticesContent()
        );

        // Calendar & Events
        $calendar = HelpCategory::create([
            'name' => 'Calendar & Events',
            'slug' => 'calendar-events',
            'icon' => 'chart',
            'description' => 'Manage events and appointments with the calendar feature.',
            'sort_order' => 8,
        ]);

        $this->createArticle($calendar, 1, 'creating-events', 'Creating Events',
            'Add events to your calendar.',
            $this->getCreatingEventsContent()
        );

        $this->createArticle($calendar, 2, 'managing-calendar', 'Managing Your Calendar',
            'View, edit, and organize your events.',
            $this->getManagingCalendarContent()
        );

        // Email Campaigns
        $campaigns = HelpCategory::create([
            'name' => 'Email Campaigns',
            'slug' => 'email-campaigns',
            'icon' => 'globe',
            'description' => 'Create and send email marketing campaigns.',
            'sort_order' => 9,
        ]);

        $this->createArticle($campaigns, 1, 'creating-campaigns', 'Creating Email Campaigns',
            'Design and set up email campaigns.',
            $this->getCreatingCampaignsContent()
        );

        $this->createArticle($campaigns, 2, 'managing-subscribers', 'Managing Subscribers',
            'Build and maintain your email list.',
            $this->getManagingSubscribersContent()
        );

        $this->createArticle($campaigns, 3, 'campaign-analytics', 'Campaign Analytics',
            'Track opens, clicks, and performance.',
            $this->getCampaignAnalyticsContent()
        );

        // Media Library
        $media = HelpCategory::create([
            'name' => 'Media Library',
            'slug' => 'media-library',
            'icon' => 'document',
            'description' => 'Upload and manage images and files.',
            'sort_order' => 10,
        ]);

        $this->createArticle($media, 1, 'uploading-images', 'Uploading Images',
            'Add images to your media library.',
            $this->getUploadingImagesContent()
        );

        $this->createArticle($media, 2, 'image-optimization', 'Image Optimization',
            'Best practices for web-optimized images.',
            $this->getImageOptimizationContent()
        );
    }

    /**
     * Create a help article.
     */
    private function createArticle(
        HelpCategory $category,
        int $sortOrder,
        string $slug,
        string $title,
        string $excerpt,
        string $content
    ): HelpArticle {
        return HelpArticle::create([
            'help_category_id' => $category->id,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'sort_order' => $sortOrder,
            'is_published' => true,
        ]);
    }

    private function getWelcomeContent(): string
    {
        return <<<'HTML'
<h2>Welcome to Your CMS</h2>
<p>Congratulations on setting up your new content management system! This guide will help you get familiar with the key features and get your website up and running quickly.</p>

<h3>What You Can Do</h3>
<p>Your CMS is designed to make website management simple, even if you have no programming experience. Here's what you can accomplish:</p>

<ul>
    <li><strong>Create and manage pages</strong> - Build beautiful pages using our drag-and-drop page builder</li>
    <li><strong>Customize your branding</strong> - Upload your logo, choose colors, and make the site your own</li>
    <li><strong>Optimize for search engines</strong> - Built-in SEO tools help your site rank better</li>
    <li><strong>Manage users</strong> - Invite team members to help manage your content</li>
</ul>

<h3>Key Concepts</h3>

<h4>Pages</h4>
<p>Pages are the building blocks of your website. Each page has its own URL and can contain different types of content like text, images, buttons, and more.</p>

<h4>Components</h4>
<p>Components are reusable content blocks you can add to pages. Examples include hero sections, feature grids, testimonials, and contact forms.</p>

<h4>Templates</h4>
<p>Templates determine the overall layout of a page. Different templates are suited for different purposes, like landing pages, blog posts, or contact pages.</p>

<h3>Getting Help</h3>
<p>If you need assistance at any time, you can:</p>
<ul>
    <li>Browse this Help Center for guides and documentation</li>
    <li>Use the search feature to find specific topics</li>
    <li>Contact your site administrator for personalized help</li>
</ul>

<p>Ready to get started? Check out the <a href="#">Dashboard Overview</a> to learn your way around.</p>
HTML;
    }

    private function getDashboardContent(): string
    {
        return <<<'HTML'
<h2>Dashboard Overview</h2>
<p>The dashboard is your home base for managing your website. Here you'll find quick access to all the important features and a summary of your site's activity.</p>

<h3>Navigation</h3>
<p>The main navigation menu is located on the left side of the screen. Here's what you'll find:</p>

<ul>
    <li><strong>Dashboard</strong> - Your home screen with quick stats and shortcuts</li>
    <li><strong>Pages</strong> - Create and manage your website pages</li>
    <li><strong>Settings</strong> - Configure your site's branding, SEO, and more</li>
    <li><strong>Users</strong> - Manage team members (Admin only)</li>
    <li><strong>Help</strong> - Access this help center</li>
</ul>

<h3>Quick Actions</h3>
<p>From the dashboard, you can quickly:</p>
<ul>
    <li>Create a new page</li>
    <li>View your most recent pages</li>
    <li>See site statistics</li>
    <li>Access frequently used settings</li>
</ul>

<h3>Your Profile</h3>
<p>Click on your name in the top-right corner to access:</p>
<ul>
    <li><strong>Profile Settings</strong> - Update your name, email, and password</li>
    <li><strong>Log Out</strong> - Securely sign out of the admin area</li>
</ul>

<h3>Tips for Getting Started</h3>
<ol>
    <li>First, update your <strong>Site Settings</strong> with your branding</li>
    <li>Create your <strong>homepage</strong> and any essential pages</li>
    <li>Configure <strong>SEO settings</strong> for better search visibility</li>
    <li>Invite <strong>team members</strong> if you need help managing content</li>
</ol>
HTML;
    }

    private function getFirstPageContent(): string
    {
        return <<<'HTML'
<h2>Creating Your First Page</h2>
<p>Let's walk through creating your first page step by step. By the end of this guide, you'll have a published page on your website.</p>

<h3>Step 1: Navigate to Pages</h3>
<p>Click on <strong>Pages</strong> in the main navigation menu. This will show you a list of all your pages (which may be empty if you're just starting out).</p>

<h3>Step 2: Create a New Page</h3>
<p>Click the <strong>Create Page</strong> button in the top-right corner. You'll see a form with several fields:</p>

<ul>
    <li><strong>Title</strong> - The name of your page (required)</li>
    <li><strong>URL Path</strong> - The web address for this page (auto-generated from title)</li>
    <li><strong>Template</strong> - Choose a layout template</li>
</ul>

<h3>Step 3: Add Content</h3>
<p>After creating the page, you'll be taken to the page builder. Here you can:</p>
<ol>
    <li>Click <strong>Add Component</strong> to add content blocks</li>
    <li>Choose from available components (Hero, Text, Images, etc.)</li>
    <li>Drag and drop to rearrange components</li>
    <li>Click on any component to edit its content</li>
</ol>

<h3>Step 4: Configure SEO</h3>
<p>Switch to the <strong>SEO</strong> tab to set:</p>
<ul>
    <li><strong>Meta Description</strong> - A brief summary for search results</li>
    <li><strong>Open Graph Image</strong> - The image shown when shared on social media</li>
</ul>

<h3>Step 5: Preview and Publish</h3>
<ol>
    <li>Click <strong>Preview</strong> to see how your page looks</li>
    <li>Make any final adjustments</li>
    <li>Toggle <strong>Published</strong> to ON</li>
    <li>Click <strong>Save</strong></li>
</ol>

<p>Congratulations! Your page is now live on your website.</p>

<h3>Tips for Great Pages</h3>
<ul>
    <li>Keep your content focused and easy to scan</li>
    <li>Use headings to break up long text</li>
    <li>Include relevant images to make pages more engaging</li>
    <li>Always fill in the SEO description for better search visibility</li>
</ul>
HTML;
    }

    private function getManagingPagesContent(): string
    {
        return <<<'HTML'
<h2>Managing Pages</h2>
<p>Learn how to organize, edit, and maintain your website pages effectively.</p>

<h3>Viewing Your Pages</h3>
<p>Navigate to <strong>Pages</strong> in the main menu to see all your pages. The list shows:</p>
<ul>
    <li>Page title</li>
    <li>URL path</li>
    <li>Published status</li>
    <li>Last modified date</li>
</ul>

<h3>Editing Pages</h3>
<p>Click on any page title to open it for editing. You can:</p>
<ul>
    <li>Modify the page content using the page builder</li>
    <li>Change page settings like URL and template</li>
    <li>Update SEO information</li>
    <li>Toggle published/unpublished status</li>
</ul>

<h3>Duplicating Pages</h3>
<p>Need to create a similar page? Click the <strong>Duplicate</strong> button to create a copy. The duplicate will:</p>
<ul>
    <li>Copy all content and settings</li>
    <li>Be created as unpublished (draft)</li>
    <li>Have "(Copy)" added to the title</li>
    <li>Get a new unique URL</li>
</ul>

<h3>Deleting Pages</h3>
<p>To delete a page:</p>
<ol>
    <li>Click the <strong>Delete</strong> button on the page</li>
    <li>Confirm the deletion when prompted</li>
</ol>
<p><strong>Note:</strong> Deleted pages are moved to trash and can be restored. Only Super Admins can permanently delete pages.</p>

<h3>Publishing and Unpublishing</h3>
<p>Control page visibility with the publish toggle:</p>
<ul>
    <li><strong>Published</strong> - Page is visible to the public</li>
    <li><strong>Unpublished (Draft)</strong> - Page is only visible in admin</li>
</ul>

<h3>Page Limits</h3>
<p>Depending on your plan, there may be a limit on the number of public pages you can create. Admin pages (like this help section) don't count toward this limit.</p>
HTML;
    }

    private function getPageBuilderContent(): string
    {
        return <<<'HTML'
<h2>Using the Page Builder</h2>
<p>The page builder is a visual editor that lets you create beautiful pages without any coding knowledge.</p>

<h3>Adding Components</h3>
<p>Components are the building blocks of your pages. To add one:</p>
<ol>
    <li>Click <strong>Add Component</strong></li>
    <li>Browse available components by category</li>
    <li>Click on a component to add it to your page</li>
</ol>

<h3>Available Components</h3>
<p>Your page builder includes various component types:</p>

<h4>Layout Components</h4>
<ul>
    <li><strong>Hero Section</strong> - Large banner with headline and call-to-action</li>
    <li><strong>Feature Grid</strong> - Display features or services in a grid</li>
    <li><strong>Two Column</strong> - Side-by-side content layout</li>
</ul>

<h4>Content Components</h4>
<ul>
    <li><strong>Text Block</strong> - Rich text content</li>
    <li><strong>Image</strong> - Single image with optional caption</li>
    <li><strong>Gallery</strong> - Multiple images in a grid</li>
</ul>

<h4>Interactive Components</h4>
<ul>
    <li><strong>Contact Form</strong> - Collect visitor information</li>
    <li><strong>FAQ Accordion</strong> - Expandable questions and answers</li>
    <li><strong>Testimonials</strong> - Customer reviews carousel</li>
</ul>

<h3>Editing Components</h3>
<p>Click on any component to open its settings panel. Depending on the component, you can edit:</p>
<ul>
    <li>Text content</li>
    <li>Images and media</li>
    <li>Colors and styling</li>
    <li>Layout options</li>
    <li>Links and buttons</li>
</ul>

<h3>Reordering Components</h3>
<p>Drag components up or down to change their order on the page. Click and hold the drag handle on the left side of each component.</p>

<h3>Removing Components</h3>
<p>Click the trash icon on any component to remove it. You'll be asked to confirm before deletion.</p>

<h3>Tips for Better Pages</h3>
<ul>
    <li>Start with a clear hierarchy - headline, supporting info, call-to-action</li>
    <li>Use consistent styling throughout your pages</li>
    <li>Don't overcrowd pages - white space helps readability</li>
    <li>Preview often to see how your page looks on different devices</li>
</ul>
HTML;
    }

    private function getPageSettingsContent(): string
    {
        return <<<'HTML'
<h2>Page Settings</h2>
<p>Every page has settings that control its URL, template, and behavior. Here's how to configure them.</p>

<h3>Basic Settings</h3>

<h4>Title</h4>
<p>The page title appears in:</p>
<ul>
    <li>The browser tab</li>
    <li>Search engine results</li>
    <li>The admin pages list</li>
</ul>
<p>Keep titles concise (50-60 characters) and descriptive.</p>

<h4>URL Path</h4>
<p>The URL path determines your page's web address. For example:</p>
<ul>
    <li><code>/about</code> becomes yoursite.com/about</li>
    <li><code>/services/consulting</code> becomes yoursite.com/services/consulting</li>
</ul>

<p><strong>URL Best Practices:</strong></p>
<ul>
    <li>Use lowercase letters</li>
    <li>Separate words with hyphens (-)</li>
    <li>Keep URLs short and descriptive</li>
    <li>Avoid special characters</li>
</ul>

<h4>Template</h4>
<p>Templates control the overall layout structure:</p>
<ul>
    <li><strong>Default</strong> - Standard page layout with header and footer</li>
    <li><strong>Landing</strong> - Full-width layout for marketing pages</li>
    <li><strong>Blog</strong> - Optimized for article content</li>
</ul>

<h3>Visibility Settings</h3>

<h4>Published Status</h4>
<ul>
    <li><strong>Published</strong> - Page is live and visible to everyone</li>
    <li><strong>Draft</strong> - Page is only visible to logged-in admins</li>
</ul>

<h4>No Index</h4>
<p>When enabled, search engines won't include this page in their results. Use for:</p>
<ul>
    <li>Thank you pages</li>
    <li>Temporary landing pages</li>
    <li>Duplicate content</li>
</ul>

<h3>Reserved Paths</h3>
<p>Some URL paths are reserved and cannot be used:</p>
<ul>
    <li><code>/admin</code> - Admin area</li>
    <li><code>/api</code> - API endpoints</li>
    <li><code>/login</code>, <code>/register</code> - Authentication</li>
</ul>
HTML;
    }

    private function getSeoBasicsContent(): string
    {
        return <<<'HTML'
<h2>SEO Basics</h2>
<p>Search Engine Optimization (SEO) helps your website appear in search results when people look for your products or services. Here's what you need to know.</p>

<h3>Why SEO Matters</h3>
<p>Most website traffic comes from search engines like Google. Good SEO means:</p>
<ul>
    <li>More people find your website</li>
    <li>You reach customers actively searching for what you offer</li>
    <li>Free, sustainable traffic over time</li>
</ul>

<h3>How Search Engines Work</h3>
<ol>
    <li><strong>Crawling</strong> - Search engines discover your pages</li>
    <li><strong>Indexing</strong> - They analyze and store your content</li>
    <li><strong>Ranking</strong> - They decide which pages to show for each search</li>
</ol>

<h3>Key Ranking Factors</h3>

<h4>Content Quality</h4>
<ul>
    <li>Write helpful, original content</li>
    <li>Answer questions your customers have</li>
    <li>Keep content up to date</li>
</ul>

<h4>Keywords</h4>
<ul>
    <li>Include relevant terms naturally in your content</li>
    <li>Use keywords in titles and headings</li>
    <li>Don't overuse keywords (keyword stuffing)</li>
</ul>

<h4>Technical Factors</h4>
<ul>
    <li>Fast page loading speed</li>
    <li>Mobile-friendly design</li>
    <li>Secure HTTPS connection</li>
</ul>

<h3>Your Built-in SEO Tools</h3>
<p>Your CMS includes several SEO features:</p>
<ul>
    <li><strong>Meta Tags</strong> - Control how pages appear in search results</li>
    <li><strong>Sitemap</strong> - Automatically generated for search engines</li>
    <li><strong>Schema Markup</strong> - Structured data for rich results</li>
    <li><strong>Social Tags</strong> - Control social media previews</li>
</ul>

<p>Learn more in the <a href="#">Page SEO Settings</a> guide.</p>
HTML;
    }

    private function getPageSeoContent(): string
    {
        return <<<'HTML'
<h2>Page SEO Settings</h2>
<p>Each page has its own SEO settings that control how it appears in search results and when shared online.</p>

<h3>Meta Description</h3>
<p>The meta description is the snippet shown below your page title in search results.</p>

<p><strong>Best Practices:</strong></p>
<ul>
    <li>Write 150-160 characters</li>
    <li>Include your main keyword naturally</li>
    <li>Make it compelling to encourage clicks</li>
    <li>Each page should have a unique description</li>
</ul>

<p><strong>Example:</strong></p>
<blockquote>
"Professional web design services for small businesses. Get a stunning website that converts visitors into customers. Free consultation available."
</blockquote>

<h3>Meta Keywords</h3>
<p>While less important than they used to be, meta keywords can still help organize your content strategy. Add 5-10 relevant keywords separated by commas.</p>

<h3>Open Graph Settings</h3>
<p>Open Graph controls how your page looks when shared on social media.</p>

<ul>
    <li><strong>OG Title</strong> - Title shown in social shares (defaults to page title)</li>
    <li><strong>OG Description</strong> - Description for social shares</li>
    <li><strong>OG Image</strong> - Image shown in social previews (1200x630 pixels recommended)</li>
</ul>

<h3>Schema Type</h3>
<p>Schema markup helps search engines understand your content. Choose the type that best matches your page:</p>

<ul>
    <li><strong>WebPage</strong> - Generic pages (default)</li>
    <li><strong>Article</strong> - Blog posts and news</li>
    <li><strong>AboutPage</strong> - About us pages</li>
    <li><strong>ContactPage</strong> - Contact pages</li>
    <li><strong>FAQPage</strong> - FAQ pages (enables FAQ rich results)</li>
    <li><strong>Product</strong> - Product pages</li>
    <li><strong>Service</strong> - Service descriptions</li>
</ul>

<h3>Sitemap Priority</h3>
<p>Set how important this page is relative to others:</p>
<ul>
    <li><strong>1.0</strong> - Most important (homepage)</li>
    <li><strong>0.8</strong> - Very important (main service pages)</li>
    <li><strong>0.5</strong> - Normal importance (default)</li>
    <li><strong>0.3</strong> - Lower importance (legal pages)</li>
</ul>

<h3>Indexing Controls</h3>
<ul>
    <li><strong>No Index</strong> - Hide this page from search results</li>
    <li><strong>No Follow</strong> - Tell search engines not to follow links on this page</li>
</ul>
HTML;
    }

    private function getSocialSharingContent(): string
    {
        return <<<'HTML'
<h2>Social Media Sharing</h2>
<p>When someone shares your page on social media, the platform displays a preview card. You can control what appears in these previews.</p>

<h3>Understanding Social Previews</h3>
<p>When a link is shared, social platforms look for:</p>
<ol>
    <li>An image to display</li>
    <li>A title</li>
    <li>A description</li>
</ol>

<p>Without proper settings, the preview might show random content from your page or no image at all.</p>

<h3>Open Graph Tags</h3>
<p>Open Graph is the standard used by Facebook, LinkedIn, and most other platforms.</p>

<h4>OG Title</h4>
<p>The title shown in the preview. If not set, your page title is used.</p>

<h4>OG Description</h4>
<p>A brief description of the page content. If not set, your meta description is used.</p>

<h4>OG Image</h4>
<p>The image shown in the preview. This is the most important element.</p>

<p><strong>Image Requirements:</strong></p>
<ul>
    <li>Recommended size: 1200 x 630 pixels</li>
    <li>Minimum size: 600 x 315 pixels</li>
    <li>Formats: JPG or PNG</li>
    <li>File size: Under 8MB</li>
</ul>

<h3>X Cards</h3>
<p>X (formerly Twitter) has its own preview format. You can choose:</p>
<ul>
    <li><strong>Summary Large Image</strong> - Large image above title (recommended)</li>
    <li><strong>Summary</strong> - Small image to the left of title</li>
</ul>

<h3>Site-Wide Defaults</h3>
<p>In your SEO Settings, you can set default values that apply to all pages:</p>
<ul>
    <li>Default OG Image</li>
    <li>X Handle</li>
    <li>Default X Card Type</li>
</ul>

<h3>Testing Your Previews</h3>
<p>After setting up your social tags, test them:</p>
<ul>
    <li><strong>Facebook</strong> - Use the Sharing Debugger</li>
    <li><strong>X</strong> - Use the Card Validator</li>
    <li><strong>LinkedIn</strong> - Use the Post Inspector</li>
</ul>

<p>These tools show exactly what your preview will look like and can help identify issues.</p>
HTML;
    }

    private function getGeoContent(): string
    {
        return <<<'HTML'
<h2>AI Search Optimization (GEO)</h2>
<p>Generative Engine Optimization (GEO) helps AI assistants like ChatGPT, Claude, and Perplexity understand and cite your content correctly.</p>

<h3>What is GEO?</h3>
<p>When users ask AI assistants questions, those AIs may reference your content in their answers. GEO ensures:</p>
<ul>
    <li>AI assistants understand your content accurately</li>
    <li>Your content is cited and attributed properly</li>
    <li>Your business is represented correctly</li>
</ul>

<h3>LLMs.txt File</h3>
<p>Your site automatically generates an <code>/llms.txt</code> file that tells AI crawlers about your site. This file includes:</p>
<ul>
    <li>Information about your organization</li>
    <li>Content usage guidelines</li>
    <li>How to cite your content</li>
    <li>Links to key pages</li>
</ul>

<h4>Settings</h4>
<ul>
    <li><strong>Allow AI Training</strong> - Whether AI models can train on your content</li>
    <li><strong>Crawl Delay</strong> - Seconds between AI crawler requests</li>
    <li><strong>Custom Content</strong> - Write your own llms.txt content</li>
</ul>

<h3>FAQ Schema</h3>
<p>Adding FAQs to your pages helps AI assistants answer questions directly:</p>
<ol>
    <li>Go to a page's SEO settings</li>
    <li>Add FAQ items with questions and answers</li>
    <li>Save the page</li>
</ol>

<p>Benefits:</p>
<ul>
    <li>AI assistants may use your answers directly</li>
    <li>Google may show FAQ rich results</li>
    <li>Improves content structure</li>
</ul>

<h3>Speakable Content</h3>
<p>Mark content that works well for voice assistants like Siri and Alexa:</p>
<ol>
    <li>Go to a page's SEO settings</li>
    <li>Add CSS selectors for speakable content (e.g., <code>h1</code>, <code>.summary</code>)</li>
    <li>Focus on clear, concise text that sounds natural when spoken</li>
</ol>

<h3>Best Practices for AI-Friendly Content</h3>
<ul>
    <li>Write clear, factual content</li>
    <li>Structure content with headings</li>
    <li>Include relevant facts and statistics</li>
    <li>Answer common questions directly</li>
    <li>Keep information up to date</li>
</ul>
HTML;
    }

    private function getSiteBrandingContent(): string
    {
        return <<<'HTML'
<h2>Site Branding</h2>
<p>Your branding defines your site's identity. Configure these settings to make your website uniquely yours.</p>

<h3>Site Name</h3>
<p>Your site name appears in:</p>
<ul>
    <li>Browser tabs (after page titles)</li>
    <li>Search engine results</li>
    <li>Social media shares</li>
    <li>The header of your site</li>
</ul>

<h3>Tagline</h3>
<p>A short phrase that describes what your business does. Keep it:</p>
<ul>
    <li>Concise (under 60 characters)</li>
    <li>Descriptive of your value proposition</li>
    <li>Memorable and unique</li>
</ul>

<h3>Logo</h3>
<p>Your logo appears in the header of your website. For best results:</p>
<ul>
    <li>Use a transparent PNG</li>
    <li>Recommended dimensions: 200-300px wide</li>
    <li>Keep file size under 500KB</li>
    <li>Ensure it looks good on white and dark backgrounds</li>
</ul>

<h3>Favicon</h3>
<p>The favicon is the small icon shown in browser tabs. Requirements:</p>
<ul>
    <li>Size: 32x32 or 64x64 pixels</li>
    <li>Format: PNG or ICO</li>
    <li>Simple design that's recognizable at small sizes</li>
</ul>

<h3>Contact Information</h3>
<p>Add your contact details to make them available across your site:</p>
<ul>
    <li><strong>Email</strong> - Primary contact email</li>
    <li><strong>Phone</strong> - Business phone number</li>
    <li><strong>Address</strong> - Physical location (if applicable)</li>
</ul>

<h3>Social Links</h3>
<p>Connect your social media profiles. These may appear in:</p>
<ul>
    <li>Your website footer</li>
    <li>Search engine knowledge panels</li>
    <li>AI assistant responses</li>
</ul>
HTML;
    }

    private function getThemesColorsContent(): string
    {
        return <<<'HTML'
<h2>Themes & Colors</h2>
<p>Choose a theme and customize colors to match your brand identity.</p>

<h3>Selecting a Theme</h3>
<p>Your CMS includes three pre-designed themes:</p>

<h4>Default Theme</h4>
<p>A clean, professional look with blue accents. Great for:</p>
<ul>
    <li>Business and corporate sites</li>
    <li>Professional services</li>
    <li>Technology companies</li>
</ul>

<h4>Modern Theme</h4>
<p>Bold colors and contemporary design. Ideal for:</p>
<ul>
    <li>Creative agencies</li>
    <li>Startups</li>
    <li>Marketing sites</li>
</ul>

<h4>Classic Theme</h4>
<p>Warm, traditional styling. Perfect for:</p>
<ul>
    <li>Law firms and consultants</li>
    <li>Educational institutions</li>
    <li>Established businesses</li>
</ul>

<h3>Customizing Colors</h3>
<p>Each theme has customizable colors:</p>

<h4>Primary Color</h4>
<p>Used for main buttons, links, and key UI elements. Choose a color that:</p>
<ul>
    <li>Matches your brand</li>
    <li>Has good contrast with white text</li>
    <li>Stands out but isn't overwhelming</li>
</ul>

<h4>Secondary Color</h4>
<p>Used for secondary buttons and accents. Should complement your primary color.</p>

<h4>Accent Color</h4>
<p>Used sparingly for highlights and calls-to-action.</p>

<h4>Background Color</h4>
<p>The main background color of your site. Usually white or a very light color.</p>

<h4>Text Color</h4>
<p>The default color for body text. Should have high contrast with your background.</p>

<h3>Color Tips</h3>
<ul>
    <li>Use your brand's official colors</li>
    <li>Ensure sufficient contrast for readability</li>
    <li>Test colors on different screens</li>
    <li>Consider accessibility for colorblind users</li>
</ul>
HTML;
    }

    private function getTypographyContent(): string
    {
        return <<<'HTML'
<h2>Typography</h2>
<p>Typography affects readability and the overall feel of your website. Choose fonts that reflect your brand personality.</p>

<h3>Heading Font</h3>
<p>Used for page titles, section headings, and important callouts. Characteristics to consider:</p>
<ul>
    <li><strong>Impact</strong> - Should be attention-grabbing</li>
    <li><strong>Readability</strong> - Must be clear at larger sizes</li>
    <li><strong>Brand fit</strong> - Should match your brand personality</li>
</ul>

<h3>Body Font</h3>
<p>Used for paragraphs and general content. Priorities:</p>
<ul>
    <li><strong>Readability</strong> - Easy to read for extended periods</li>
    <li><strong>Screen optimization</strong> - Designed for digital displays</li>
    <li><strong>Versatility</strong> - Works at various sizes</li>
</ul>

<h3>Font Pairing Guidelines</h3>
<p>When choosing fonts, consider:</p>

<h4>Contrast</h4>
<p>Heading and body fonts should be noticeably different. A serif heading with a sans-serif body often works well.</p>

<h4>Harmony</h4>
<p>Despite being different, fonts should feel like they belong together. Similar proportions or design era help.</p>

<h4>Simplicity</h4>
<p>Limit yourself to 2 fonts maximum. Too many fonts look chaotic and unprofessional.</p>

<h3>Available Font Options</h3>
<p>Your CMS includes a curated selection of web fonts:</p>

<h4>Sans-Serif Fonts</h4>
<ul>
    <li><strong>Inter</strong> - Modern, highly readable (default)</li>
    <li><strong>Open Sans</strong> - Friendly and neutral</li>
    <li><strong>Roboto</strong> - Clean and contemporary</li>
</ul>

<h4>Serif Fonts</h4>
<ul>
    <li><strong>Merriweather</strong> - Elegant and readable</li>
    <li><strong>Playfair Display</strong> - Sophisticated headings</li>
    <li><strong>Georgia</strong> - Classic and reliable</li>
</ul>
HTML;
    }

    private function getUserRolesContent(): string
    {
        return <<<'HTML'
<h2>Understanding User Roles</h2>
<p>Your CMS uses a role-based permission system. Each role has different capabilities to help you manage your team effectively.</p>

<h3>Available Roles</h3>

<h4>Super Admin</h4>
<p>The highest level of access. Super Admins can:</p>
<ul>
    <li>Do everything other roles can do</li>
    <li>Permanently delete any content</li>
    <li>Access all system settings</li>
    <li>Manage all users including other admins</li>
</ul>
<p><em>Note: This role is typically reserved for your site developer.</em></p>

<h4>Admin</h4>
<p>Full content management capabilities:</p>
<ul>
    <li>Create, edit, and delete pages</li>
    <li>Manage site settings and branding</li>
    <li>Invite and manage users (except Super Admin)</li>
    <li>Configure SEO settings</li>
    <li>Access all content areas</li>
</ul>
<p><em>Best for: Business owners and primary site managers</em></p>

<h4>User</h4>
<p>Standard content management access:</p>
<ul>
    <li>Create and edit pages</li>
    <li>Delete their own content</li>
    <li>Access content areas</li>
    <li>Cannot manage users or site settings</li>
</ul>
<p><em>Best for: Content creators and team members</em></p>

<h4>Customer</h4>
<p>Limited access role:</p>
<ul>
    <li>View specific content</li>
    <li>Access customer-facing features</li>
    <li>Cannot modify site content</li>
</ul>
<p><em>Best for: External customers who need limited access</em></p>

<h3>Choosing the Right Role</h3>
<p>When assigning roles, consider:</p>
<ul>
    <li><strong>Responsibility</strong> - What tasks will this person perform?</li>
    <li><strong>Trust level</strong> - How much access do they need?</li>
    <li><strong>Risk</strong> - What could go wrong with more access?</li>
</ul>

<p><strong>Best Practice:</strong> Always assign the minimum role necessary for someone to do their job.</p>
HTML;
    }

    private function getInvitingUsersContent(): string
    {
        return <<<'HTML'
<h2>Inviting Users</h2>
<p>Grow your team by inviting new users to help manage your site.</p>

<h3>Who Can Invite Users?</h3>
<ul>
    <li><strong>Super Admins</strong> - Can invite anyone with any role</li>
    <li><strong>Admins</strong> - Can invite Users and Customers</li>
    <li><strong>Users</strong> - Cannot invite new users</li>
</ul>

<h3>How to Invite a User</h3>
<ol>
    <li>Navigate to <strong>Users</strong> in the admin menu</li>
    <li>Click <strong>Invite User</strong></li>
    <li>Enter their email address</li>
    <li>Select a role for them</li>
    <li>Click <strong>Send Invitation</strong></li>
</ol>

<h3>What Happens Next</h3>
<ol>
    <li>The invitee receives an email with a link</li>
    <li>They click the link to set up their account</li>
    <li>They create their password</li>
    <li>They can now log in with their assigned role</li>
</ol>

<h3>Invitation Expiration</h3>
<p>Invitations expire after 7 days. If someone misses the window:</p>
<ol>
    <li>Delete the expired invitation</li>
    <li>Send a new invitation</li>
</ol>

<h3>Pending Invitations</h3>
<p>View all pending invitations in the Users section. You can:</p>
<ul>
    <li>See when each invitation was sent</li>
    <li>Resend invitation emails</li>
    <li>Cancel invitations</li>
</ul>

<h3>Tips for Smooth Onboarding</h3>
<ul>
    <li>Let people know to expect the invitation email</li>
    <li>Ask them to check spam folders</li>
    <li>Start new users with the User role, upgrade later if needed</li>
    <li>Point them to this Help Center for documentation</li>
</ul>
HTML;
    }

    private function getManagingUsersContent(): string
    {
        return <<<'HTML'
<h2>Managing Users</h2>
<p>Keep your team organized by managing user accounts, roles, and access.</p>

<h3>Viewing Users</h3>
<p>Navigate to <strong>Users</strong> to see everyone with access to your site. The list shows:</p>
<ul>
    <li>Name and email</li>
    <li>Assigned role</li>
    <li>Account status</li>
    <li>Last activity</li>
</ul>

<h3>Editing User Details</h3>
<p>Click on a user to modify their account:</p>
<ul>
    <li><strong>Name</strong> - Update their display name</li>
    <li><strong>Email</strong> - Change their login email</li>
    <li><strong>Role</strong> - Upgrade or downgrade their permissions</li>
</ul>

<h3>Changing User Roles</h3>
<p>To change someone's role:</p>
<ol>
    <li>Click on the user's name</li>
    <li>Select a new role from the dropdown</li>
    <li>Save changes</li>
</ol>

<p><strong>Important:</strong> Role changes take effect immediately.</p>

<h3>Removing Users</h3>
<p>To remove someone's access:</p>
<ol>
    <li>Click on the user's name</li>
    <li>Click <strong>Remove User</strong></li>
    <li>Confirm the removal</li>
</ol>

<p>When a user is removed:</p>
<ul>
    <li>They can no longer log in</li>
    <li>Their content remains (associated with their account)</li>
    <li>You can re-invite them later if needed</li>
</ul>

<h3>User Account Security</h3>
<p>Best practices for keeping accounts secure:</p>
<ul>
    <li>Require strong passwords</li>
    <li>Remove access promptly when someone leaves</li>
    <li>Regularly review who has Admin access</li>
    <li>Use role-appropriate permissions</li>
</ul>

<h3>Your Own Account</h3>
<p>To update your own account:</p>
<ol>
    <li>Click your name in the top-right corner</li>
    <li>Select <strong>Profile</strong></li>
    <li>Update your name, email, or password</li>
</ol>
HTML;
    }

    private function getCreatingBlogPostsContent(): string
    {
        return <<<'HTML'
<h2>Creating Blog Posts</h2>
<p>Blog posts help you share news, insights, and stories with your audience. They're also great for SEO.</p>

<h3>Creating a New Post</h3>
<ol>
    <li>Navigate to <strong>Blog</strong> in the admin menu</li>
    <li>Click <strong>New Post</strong></li>
    <li>Enter your post details</li>
</ol>

<h3>Post Fields</h3>

<h4>Title</h4>
<p>Your headline. Make it compelling and descriptive. Good titles:</p>
<ul>
    <li>Clearly convey the topic</li>
    <li>Include relevant keywords</li>
    <li>Create curiosity or promise value</li>
</ul>

<h4>Slug</h4>
<p>The URL-friendly version of your title. Auto-generated but can be customized.</p>

<h4>Content</h4>
<p>Use the rich text editor to write your post. You can:</p>
<ul>
    <li>Format text (bold, italic, headings)</li>
    <li>Add links</li>
    <li>Insert images</li>
    <li>Create lists</li>
    <li>Embed videos</li>
</ul>

<h4>Excerpt</h4>
<p>A short summary shown in blog listings and social shares. If not provided, the first paragraph is used.</p>

<h4>Category</h4>
<p>Select a category to organize your post. Categories help readers find related content.</p>

<h3>Publishing Options</h3>
<ul>
    <li><strong>Draft</strong> - Save without publishing</li>
    <li><strong>Publish</strong> - Make the post live immediately</li>
    <li><strong>Schedule</strong> - Set a future publish date</li>
</ul>

<h3>Tips for Great Blog Posts</h3>
<ul>
    <li>Write for your audience, not search engines</li>
    <li>Use headings to break up content</li>
    <li>Include relevant images</li>
    <li>End with a call-to-action</li>
    <li>Aim for 800-1500 words for SEO</li>
</ul>
HTML;
    }

    private function getBlogCategoriesContent(): string
    {
        return <<<'HTML'
<h2>Blog Categories</h2>
<p>Categories help organize your blog posts and make it easier for readers to find content they're interested in.</p>

<h3>Managing Categories</h3>
<ol>
    <li>Go to <strong>Blog</strong> in the admin menu</li>
    <li>Click <strong>Manage Categories</strong></li>
</ol>

<h3>Creating a Category</h3>
<ol>
    <li>Click <strong>New Category</strong></li>
    <li>Enter a name (e.g., "News", "Tutorials", "Case Studies")</li>
    <li>Add an optional description</li>
    <li>Set the display order</li>
    <li>Click <strong>Save</strong></li>
</ol>

<h3>Category Settings</h3>
<ul>
    <li><strong>Name</strong> - The display name for the category</li>
    <li><strong>Slug</strong> - URL-friendly version (auto-generated)</li>
    <li><strong>Description</strong> - Shown on category archive pages</li>
    <li><strong>Sort Order</strong> - Controls display order in lists</li>
</ul>

<h3>Best Practices</h3>
<ul>
    <li>Keep the number of categories manageable (5-10 max)</li>
    <li>Use clear, descriptive names</li>
    <li>Ensure categories don't overlap in meaning</li>
    <li>Each post should fit in one primary category</li>
</ul>

<h3>Deleting Categories</h3>
<p>When you delete a category:</p>
<ul>
    <li>Posts in that category become uncategorized</li>
    <li>The category URL stops working</li>
    <li>This action cannot be undone</li>
</ul>
HTML;
    }

    private function getBlogSeoContent(): string
    {
        return <<<'HTML'
<h2>Blog SEO & Featured Images</h2>
<p>Optimize your blog posts to rank better in search results and look great when shared.</p>

<h3>Featured Images</h3>
<p>The featured image appears:</p>
<ul>
    <li>At the top of your blog post</li>
    <li>In blog listing pages</li>
    <li>When shared on social media</li>
    <li>In search results (sometimes)</li>
</ul>

<h4>Image Requirements</h4>
<ul>
    <li><strong>Size:</strong> 1200 x 630 pixels recommended</li>
    <li><strong>Format:</strong> JPG or PNG</li>
    <li><strong>File size:</strong> Under 500KB for fast loading</li>
</ul>

<h3>SEO Settings for Posts</h3>
<p>Each blog post has its own SEO settings:</p>

<h4>Meta Description</h4>
<p>Write a compelling 150-160 character summary. This appears in search results.</p>

<h4>Focus Keywords</h4>
<p>Include relevant keywords naturally in:</p>
<ul>
    <li>The title</li>
    <li>The first paragraph</li>
    <li>Headings throughout the post</li>
    <li>The URL slug</li>
</ul>

<h4>Open Graph Settings</h4>
<p>Control how your post appears on social media:</p>
<ul>
    <li><strong>OG Title</strong> - Social media title (defaults to post title)</li>
    <li><strong>OG Description</strong> - Social media description</li>
    <li><strong>OG Image</strong> - Defaults to featured image</li>
</ul>

<h3>Blog Post Structure for SEO</h3>
<ol>
    <li>Compelling title with primary keyword</li>
    <li>Strong opening paragraph</li>
    <li>Clear headings (H2, H3) throughout</li>
    <li>Internal links to related content</li>
    <li>Call-to-action at the end</li>
</ol>
HTML;
    }

    private function getManagingNavigationContent(): string
    {
        return <<<'HTML'
<h2>Managing Navigation</h2>
<p>Your site's navigation helps visitors find what they're looking for. A clear navigation structure improves user experience and SEO.</p>

<h3>Accessing Navigation Settings</h3>
<ol>
    <li>Go to <strong>Navigation</strong> in the admin menu</li>
    <li>You'll see your current menu structure</li>
</ol>

<h3>Adding Menu Items</h3>
<ol>
    <li>Click <strong>Add Menu Item</strong></li>
    <li>Choose the item type:
        <ul>
            <li><strong>Page Link</strong> - Link to an existing page</li>
            <li><strong>Custom Link</strong> - Any URL</li>
            <li><strong>Dropdown</strong> - Group of related links</li>
        </ul>
    </li>
    <li>Enter the label (text shown in menu)</li>
    <li>Select or enter the URL</li>
    <li>Click <strong>Save</strong></li>
</ol>

<h3>Organizing Menu Items</h3>
<ul>
    <li><strong>Drag and drop</strong> to reorder items</li>
    <li><strong>Nest items</strong> under parents to create dropdowns</li>
    <li><strong>Edit</strong> any item to change its label or URL</li>
    <li><strong>Delete</strong> items you no longer need</li>
</ul>

<h3>Menu Item Options</h3>
<ul>
    <li><strong>Label</strong> - The text displayed in the menu</li>
    <li><strong>URL</strong> - Where the link goes</li>
    <li><strong>Open in new tab</strong> - For external links</li>
    <li><strong>Icon</strong> - Optional icon next to the label</li>
</ul>

<h3>Footer Navigation</h3>
<p>Your footer can have its own navigation with links like:</p>
<ul>
    <li>Privacy Policy</li>
    <li>Terms of Service</li>
    <li>Contact</li>
    <li>Social media links</li>
</ul>
HTML;
    }

    private function getNavigationBestPracticesContent(): string
    {
        return <<<'HTML'
<h2>Navigation Best Practices</h2>
<p>Good navigation makes your site easier to use and helps search engines understand your content structure.</p>

<h3>Keep It Simple</h3>
<ul>
    <li>Limit main menu to 5-7 items</li>
    <li>Use clear, descriptive labels</li>
    <li>Avoid jargon or clever wordplay</li>
    <li>Most important items should come first</li>
</ul>

<h3>Logical Structure</h3>
<p>Organize menu items by:</p>
<ul>
    <li>User intent (what are they looking for?)</li>
    <li>Content relationship (group related pages)</li>
    <li>Priority (important items first)</li>
</ul>

<h3>Dropdown Menus</h3>
<p>Use dropdowns sparingly:</p>
<ul>
    <li>Limit to one level of nesting</li>
    <li>Keep 3-7 items per dropdown</li>
    <li>Parent item should be clickable too</li>
    <li>Ensure dropdowns work on mobile</li>
</ul>

<h3>Mobile Navigation</h3>
<p>Your navigation automatically adapts to mobile, but consider:</p>
<ul>
    <li>Touch-friendly tap targets</li>
    <li>Easily collapsible menus</li>
    <li>Critical pages accessible quickly</li>
</ul>

<h3>Call-to-Action in Navigation</h3>
<p>Consider adding a standout button for your primary action:</p>
<ul>
    <li>"Contact Us"</li>
    <li>"Get Started"</li>
    <li>"Book Now"</li>
</ul>

<h3>Common Navigation Patterns</h3>
<h4>Service Business</h4>
<p>Home | Services | About | Blog | Contact</p>

<h4>E-commerce</h4>
<p>Shop | Categories | About | Support | Cart</p>

<h4>Portfolio</h4>
<p>Work | Services | About | Contact</p>
HTML;
    }

    private function getCreatingEventsContent(): string
    {
        return <<<'HTML'
<h2>Creating Events</h2>
<p>Use the calendar to manage events, appointments, and important dates.</p>

<h3>Adding a New Event</h3>
<ol>
    <li>Go to <strong>Calendar</strong> in the admin menu</li>
    <li>Click <strong>New Event</strong> or click on a date</li>
    <li>Fill in the event details</li>
    <li>Click <strong>Save</strong></li>
</ol>

<h3>Event Details</h3>

<h4>Basic Information</h4>
<ul>
    <li><strong>Title</strong> - Name of the event</li>
    <li><strong>Description</strong> - Details about the event</li>
    <li><strong>Location</strong> - Where the event takes place</li>
</ul>

<h4>Date & Time</h4>
<ul>
    <li><strong>Start Date/Time</strong> - When the event begins</li>
    <li><strong>End Date/Time</strong> - When the event ends</li>
    <li><strong>All Day</strong> - Toggle for full-day events</li>
</ul>

<h4>Display Options</h4>
<ul>
    <li><strong>Color</strong> - Visual category indicator</li>
    <li><strong>Published</strong> - Whether it's visible on the site</li>
</ul>

<h3>Recurring Events</h3>
<p>For events that repeat:</p>
<ul>
    <li>Daily, weekly, monthly, or yearly</li>
    <li>Set end date or number of occurrences</li>
    <li>Edit single or all occurrences</li>
</ul>

<h3>Event Types</h3>
<p>Common uses for the calendar:</p>
<ul>
    <li>Business hours or availability</li>
    <li>Appointments and meetings</li>
    <li>Workshops or webinars</li>
    <li>Holidays and closures</li>
    <li>Product launches or sales</li>
</ul>
HTML;
    }

    private function getManagingCalendarContent(): string
    {
        return <<<'HTML'
<h2>Managing Your Calendar</h2>
<p>Keep your calendar organized and up-to-date.</p>

<h3>Calendar Views</h3>
<p>Switch between different views to see your events:</p>
<ul>
    <li><strong>Month View</strong> - Overview of the entire month</li>
    <li><strong>Week View</strong> - Detailed weekly schedule</li>
    <li><strong>Day View</strong> - Hour-by-hour breakdown</li>
    <li><strong>List View</strong> - Upcoming events in a list</li>
</ul>

<h3>Editing Events</h3>
<ol>
    <li>Click on an event to open it</li>
    <li>Make your changes</li>
    <li>Click <strong>Save</strong></li>
</ol>

<h3>Moving Events</h3>
<p>To reschedule an event:</p>
<ul>
    <li><strong>Drag and drop</strong> the event to a new date/time</li>
    <li>Or edit the event and change the date fields</li>
</ul>

<h3>Deleting Events</h3>
<ol>
    <li>Click on the event</li>
    <li>Click <strong>Delete</strong></li>
    <li>For recurring events, choose to delete one or all occurrences</li>
</ol>

<h3>Filtering Events</h3>
<p>Use filters to find specific events:</p>
<ul>
    <li>By color/category</li>
    <li>By date range</li>
    <li>By status (published/draft)</li>
</ul>

<h3>Calendar on Your Website</h3>
<p>Display your calendar on the public site:</p>
<ol>
    <li>Create a new page</li>
    <li>Add a Calendar component</li>
    <li>Configure display options</li>
    <li>Publish the page</li>
</ol>
HTML;
    }

    private function getCreatingCampaignsContent(): string
    {
        return <<<'HTML'
<h2>Creating Email Campaigns</h2>
<p>Email campaigns help you reach your audience directly with newsletters, announcements, and promotions.</p>

<h3>Creating a New Campaign</h3>
<ol>
    <li>Go to <strong>Campaigns</strong> in the admin menu</li>
    <li>Click <strong>New Campaign</strong></li>
    <li>Choose a campaign type</li>
</ol>

<h3>Campaign Settings</h3>

<h4>Basic Information</h4>
<ul>
    <li><strong>Campaign Name</strong> - Internal name for reference</li>
    <li><strong>Subject Line</strong> - What recipients see in their inbox</li>
    <li><strong>Preview Text</strong> - Text shown after the subject</li>
</ul>

<h4>From Details</h4>
<ul>
    <li><strong>From Name</strong> - Your business or sender name</li>
    <li><strong>Reply-To</strong> - Where responses go</li>
</ul>

<h3>Designing Your Email</h3>
<p>Use the email builder to create your content:</p>
<ul>
    <li>Drag and drop content blocks</li>
    <li>Add text, images, and buttons</li>
    <li>Customize colors and fonts</li>
    <li>Preview on desktop and mobile</li>
</ul>

<h3>Subject Line Tips</h3>
<ul>
    <li>Keep it under 50 characters</li>
    <li>Create urgency or curiosity</li>
    <li>Avoid spam trigger words</li>
    <li>Personalize when possible</li>
    <li>Test different approaches</li>
</ul>

<h3>Sending Options</h3>
<ul>
    <li><strong>Send Now</strong> - Immediately deliver to all subscribers</li>
    <li><strong>Schedule</strong> - Set a specific date and time</li>
    <li><strong>Save as Draft</strong> - Continue editing later</li>
</ul>

<h3>Before You Send</h3>
<ol>
    <li>Send a test email to yourself</li>
    <li>Check all links work correctly</li>
    <li>Review on mobile devices</li>
    <li>Proofread for errors</li>
</ol>
HTML;
    }

    private function getManagingSubscribersContent(): string
    {
        return <<<'HTML'
<h2>Managing Subscribers</h2>
<p>Build and maintain a healthy email list for effective campaigns.</p>

<h3>Viewing Subscribers</h3>
<p>Access your subscriber list from the Campaigns section. You can see:</p>
<ul>
    <li>Email addresses</li>
    <li>Subscription date</li>
    <li>Status (active, unsubscribed)</li>
    <li>Engagement metrics</li>
</ul>

<h3>Adding Subscribers</h3>
<p>People can join your list through:</p>
<ul>
    <li>Sign-up forms on your website</li>
    <li>Manual addition in the admin</li>
    <li>Import from CSV file</li>
</ul>

<h3>Subscriber Status</h3>
<ul>
    <li><strong>Active</strong> - Will receive campaigns</li>
    <li><strong>Unsubscribed</strong> - Opted out, won't receive emails</li>
    <li><strong>Bounced</strong> - Email address is invalid</li>
</ul>

<h3>List Hygiene</h3>
<p>Maintain a healthy list by:</p>
<ul>
    <li>Removing bounced addresses</li>
    <li>Honoring unsubscribe requests immediately</li>
    <li>Re-engaging inactive subscribers</li>
    <li>Never buying email lists</li>
</ul>

<h3>Compliance</h3>
<p>Email marketing requires following regulations:</p>
<ul>
    <li><strong>Include unsubscribe link</strong> in every email</li>
    <li><strong>Honor opt-outs</strong> within 10 days</li>
    <li><strong>Include physical address</strong></li>
    <li><strong>Don't use misleading subjects</strong></li>
</ul>

<h3>Growing Your List</h3>
<ul>
    <li>Add sign-up forms to key pages</li>
    <li>Offer incentives (discounts, free content)</li>
    <li>Promote on social media</li>
    <li>Make sign-up easy (just email required)</li>
</ul>
HTML;
    }

    private function getCampaignAnalyticsContent(): string
    {
        return <<<'HTML'
<h2>Campaign Analytics</h2>
<p>Track the performance of your email campaigns to improve future results.</p>

<h3>Key Metrics</h3>

<h4>Open Rate</h4>
<p>Percentage of recipients who opened your email.</p>
<ul>
    <li><strong>Good:</strong> 20-25%</li>
    <li><strong>Excellent:</strong> 25%+</li>
</ul>
<p>Low open rates? Improve your subject lines.</p>

<h4>Click Rate</h4>
<p>Percentage who clicked a link in your email.</p>
<ul>
    <li><strong>Good:</strong> 2-5%</li>
    <li><strong>Excellent:</strong> 5%+</li>
</ul>
<p>Low clicks? Make your CTAs more compelling.</p>

<h4>Bounce Rate</h4>
<p>Emails that couldn't be delivered.</p>
<ul>
    <li><strong>Soft bounce:</strong> Temporary issue (full inbox)</li>
    <li><strong>Hard bounce:</strong> Invalid address (remove these)</li>
</ul>

<h4>Unsubscribe Rate</h4>
<p>People who opted out after receiving your email.</p>
<ul>
    <li><strong>Normal:</strong> Under 0.5%</li>
    <li><strong>Concerning:</strong> Over 1%</li>
</ul>

<h3>Viewing Campaign Reports</h3>
<ol>
    <li>Go to <strong>Campaigns</strong></li>
    <li>Click on a sent campaign</li>
    <li>View the analytics dashboard</li>
</ol>

<h3>What to Track</h3>
<ul>
    <li>Which subject lines perform best</li>
    <li>Best sending times for your audience</li>
    <li>Most clicked links</li>
    <li>Content that drives engagement</li>
</ul>

<h3>Improving Results</h3>
<ul>
    <li>Test different subject lines</li>
    <li>Segment your audience</li>
    <li>Optimize send times</li>
    <li>Create valuable content</li>
    <li>Keep emails concise</li>
</ul>
HTML;
    }

    private function getUploadingImagesContent(): string
    {
        return <<<'HTML'
<h2>Uploading Images</h2>
<p>Add images to your media library for use across your website.</p>

<h3>How to Upload</h3>
<p>You can upload images in several places:</p>
<ul>
    <li>Directly in the page builder when adding an image</li>
    <li>In blog post editor</li>
    <li>In component settings</li>
</ul>

<h3>Upload Methods</h3>
<ul>
    <li><strong>Click to upload</strong> - Browse your computer</li>
    <li><strong>Drag and drop</strong> - Drop files onto the upload area</li>
    <li><strong>Paste URL</strong> - Use an image from the web</li>
</ul>

<h3>Supported Formats</h3>
<ul>
    <li><strong>JPG/JPEG</strong> - Best for photographs</li>
    <li><strong>PNG</strong> - Best for graphics with transparency</li>
    <li><strong>GIF</strong> - For animations</li>
    <li><strong>SVG</strong> - For logos and icons</li>
    <li><strong>WebP</strong> - Modern format with better compression</li>
</ul>

<h3>File Size Limits</h3>
<ul>
    <li>Maximum file size: 10MB</li>
    <li>Recommended: Under 500KB for web</li>
    <li>Large images are automatically optimized</li>
</ul>

<h3>Image Alt Text</h3>
<p>Always add alt text to describe your images:</p>
<ul>
    <li>Helps visually impaired users</li>
    <li>Improves SEO</li>
    <li>Shows if image fails to load</li>
</ul>

<h3>Organizing Images</h3>
<ul>
    <li>Use descriptive file names</li>
    <li>Group related images in folders</li>
    <li>Delete unused images periodically</li>
</ul>
HTML;
    }

    private function getImageOptimizationContent(): string
    {
        return <<<'HTML'
<h2>Image Optimization</h2>
<p>Optimized images load faster, improving user experience and SEO.</p>

<h3>Why Optimization Matters</h3>
<ul>
    <li>Faster page load times</li>
    <li>Better mobile experience</li>
    <li>Lower bandwidth costs</li>
    <li>Improved search rankings</li>
</ul>

<h3>Recommended Sizes</h3>

<h4>Hero Images</h4>
<p>1920 x 1080 pixels, under 300KB</p>

<h4>Feature Images</h4>
<p>800 x 600 pixels, under 150KB</p>

<h4>Blog Featured Images</h4>
<p>1200 x 630 pixels, under 200KB</p>

<h4>Thumbnails</h4>
<p>400 x 300 pixels, under 50KB</p>

<h3>Optimization Tips</h3>

<h4>Before Uploading</h4>
<ul>
    <li>Resize to the dimensions you need</li>
    <li>Use compression tools (TinyPNG, ImageOptim)</li>
    <li>Choose the right format (JPG for photos, PNG for graphics)</li>
</ul>

<h4>File Names</h4>
<ul>
    <li>Use descriptive names: "red-running-shoes.jpg"</li>
    <li>Separate words with hyphens</li>
    <li>Avoid special characters</li>
</ul>

<h3>Automatic Optimization</h3>
<p>Your CMS automatically:</p>
<ul>
    <li>Creates responsive versions</li>
    <li>Compresses uploaded images</li>
    <li>Generates WebP versions</li>
    <li>Lazy loads images below the fold</li>
</ul>

<h3>Common Mistakes</h3>
<ul>
    <li>Uploading directly from camera (too large)</li>
    <li>Using PNG for photographs</li>
    <li>Skipping alt text</li>
    <li>Using the same image for all sizes</li>
</ul>
HTML;
    }
}
