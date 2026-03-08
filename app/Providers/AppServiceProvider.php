<?php

namespace App\Providers;

use App\Services\ContentRAGService;
use App\Services\EmbeddingService;
use App\Services\HelpService;
use App\Services\LlmsService;
use App\Services\PageContentBuilder;
use App\Services\PageService;
use App\Services\RobotsService;
use App\Services\SeoService;
use App\Services\SitemapService;
use App\Services\SiteSettingsService;
use App\Services\StructuredDataService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StructuredDataService::class);
        $this->app->singleton(SeoService::class);
        $this->app->singleton(SitemapService::class);
        $this->app->singleton(RobotsService::class);
        $this->app->singleton(LlmsService::class);
        $this->app->singleton(HelpService::class);
        $this->app->singleton(PageService::class);
        $this->app->singleton(SiteSettingsService::class);

        // RAG services for content generation
        $this->app->singleton(EmbeddingService::class);
        $this->app->singleton(PageContentBuilder::class);
        $this->app->singleton(ContentRAGService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
