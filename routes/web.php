<?php

use App\Http\Controllers\Admin\AiGenerationController;
use App\Http\Controllers\Admin\SetupWizardController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\Blog\BlogCategoryController;
use App\Http\Controllers\Admin\Blog\BlogPostController;
use App\Http\Controllers\Admin\Blog\PublishBlogPostController;
use App\Http\Controllers\Admin\Blog\UnpublishBlogPostController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\UpdatesController;
use App\Http\Controllers\Admin\ResendInvitationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DuplicatePageController;
use App\Http\Controllers\Admin\HelpController;
use App\Http\Controllers\Admin\MediaUploadController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\Products\ProductCategoryController;
use App\Http\Controllers\Admin\PublishPageController;
use App\Http\Controllers\Admin\ReorderPagesController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\UnpublishPageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactSubmissionController;
use App\Http\Controllers\Seo\LlmsController;
use App\Http\Controllers\Seo\RobotsController;
use App\Http\Controllers\Seo\SitemapController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

// Homepage - served from CMS
Route::get('/', PublicPageController::class)->defaults('path', '/');

// Redirect old dashboard to admin dashboard
Route::redirect('/dashboard', '/admin/dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('verified')->group(function () {
        // Dashboard
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        // Setup Wizard
        Route::get('setup', [SetupWizardController::class, 'show'])->name('setup');
        Route::post('setup/generate', [SetupWizardController::class, 'generate'])->name('setup.generate');
        Route::get('setup/status/{jobId}', [SetupWizardController::class, 'status'])->name('setup.status');

        // Pages
        Route::resource('pages', PageController::class);
        Route::post('pages/{page}/publish', PublishPageController::class)->name('pages.publish');
        Route::post('pages/{page}/unpublish', UnpublishPageController::class)->name('pages.unpublish');
        Route::post('pages/{page}/duplicate', DuplicatePageController::class)->name('pages.duplicate');
        Route::post('pages/reorder', ReorderPagesController::class)->name('pages.reorder');

        // Navigation
        Route::get('navigation', [NavigationController::class, 'edit'])->name('navigation.edit');
        Route::patch('navigation', [NavigationController::class, 'update'])->name('navigation.update');

        // Site Settings
        Route::get('settings', [SiteSettingsController::class, 'edit'])->name('settings.edit');
        Route::patch('settings', [SiteSettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/test-stripe', [SiteSettingsController::class, 'testStripeConnection'])->name('settings.test-stripe');

        // AI Generation
        Route::prefix('ai')->name('ai.')->group(function () {
            Route::post('generate-llms-txt', [AiGenerationController::class, 'generateLlmsTxt'])->name('generate-llms-txt');
            Route::post('generate-robots-txt', [AiGenerationController::class, 'generateRobotsTxt'])->name('generate-robots-txt');
            Route::post('generate-meta-description', [AiGenerationController::class, 'generateMetaDescription'])->name('generate-meta-description');
            Route::post('generate-meta-keywords', [AiGenerationController::class, 'generateMetaKeywords'])->name('generate-meta-keywords');
        });

        // Media Upload
        Route::post('media/upload', MediaUploadController::class)->name('media.upload');

        // Calendar / Appointments
        Route::resource('calendar', AppointmentController::class)->parameters(['calendar' => 'appointment']);

        // User Management
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/invite', [InvitationController::class, 'store'])->name('users.invite');
        Route::post('users/invitations/{invitation}/resend', ResendInvitationController::class)->name('users.invitations.resend');
        Route::delete('users/invitations/{invitation}', [InvitationController::class, 'destroy'])->name('users.invitations.destroy');

        // Email Campaigns
        Route::resource('campaigns', CampaignController::class)->except(['show']);

        // Blog
        Route::prefix('blog')->name('blog.')->group(function () {
            Route::resource('posts', BlogPostController::class);
            Route::post('posts/{post}/publish', PublishBlogPostController::class)->name('posts.publish');
            Route::post('posts/{post}/unpublish', UnpublishBlogPostController::class)->name('posts.unpublish');
            Route::patch('settings', [BlogPostController::class, 'updateSettings'])->name('settings.update');
            Route::resource('categories', BlogCategoryController::class);
        });

        // Products
        Route::prefix('products')->name('products.')->group(function () {
            Route::patch('settings', [ProductController::class, 'updateSettings'])->name('settings.update');
            Route::resource('categories', ProductCategoryController::class);
        });
        Route::resource('products', ProductController::class);

        // Help Center
        Route::prefix('help')->name('help.')->group(function () {
            Route::get('/', [HelpController::class, 'index'])->name('index');
            Route::get('/search', [HelpController::class, 'search'])->name('search');
            Route::get('/category/{slug}', [HelpController::class, 'category'])->name('category');
            Route::get('/{slug}', [HelpController::class, 'show'])->name('show');
        });

        // System Updates
        Route::get('updates', [UpdatesController::class, 'index'])->name('updates.index');
        Route::post('updates/check', [UpdatesController::class, 'check'])->name('updates.check');
    });
});

// SEO Routes
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/llms.txt', LlmsController::class)->name('llms');

require __DIR__.'/auth.php';

// Public Blog Routes (must be before the catch-all)
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

// Public Products Routes (must be before the catch-all)
Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products/category/{category:slug}', [ProductsController::class, 'category'])->name('products.category');
Route::get('/products/{product:slug}', [ProductsController::class, 'show'])->name('products.show');

// Stripe Checkout Routes (feature-flagged)
Route::middleware('stripe.enabled')->group(function () {
    Route::post('/checkout/create-session', [CheckoutController::class, 'createSession'])->name('checkout.create-session');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
});

// Stripe Webhook (CSRF exempt, handled in controller)
Route::post('/stripe/webhook', StripeWebhookController::class)
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('stripe.webhook');

// Contact Form Submission
Route::post('/contact/submit', ContactSubmissionController::class)->name('contact.submit');

// CMS Pages - catch-all route (must be last)
Route::get('/{path}', PublicPageController::class)
    ->where('path', '.*')
    ->name('page.show');
