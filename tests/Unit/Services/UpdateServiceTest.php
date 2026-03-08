<?php

use App\Services\UpdateService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'version.current' => '1.0.0',
        'version.github_repo' => 'test/repo',
        'version.github_token' => null,
        'version.check_enabled' => true,
        'version.release_date' => '2026-03-01',
    ]);

    Cache::flush();
});

describe('getCurrentVersion', function () {
    it('returns the configured current version', function () {
        $service = new UpdateService();

        expect($service->getCurrentVersion())->toBe('1.0.0');
    });
});

describe('checkForUpdates', function () {
    it('returns no update available when disabled', function () {
        config(['version.check_enabled' => false]);
        $service = new UpdateService();

        $result = $service->checkForUpdates();

        expect($result)
            ->available->toBeFalse()
            ->current_version->toBe('1.0.0')
            ->latest_version->toBe('1.0.0');
    });

    it('detects when update is available', function () {
        Http::fake([
            'api.github.com/repos/test/repo/releases/latest' => Http::response([
                'tag_name' => 'v2.0.0',
                'name' => 'Version 2.0.0',
                'body' => 'Release notes here',
                'html_url' => 'https://github.com/test/repo/releases/tag/v2.0.0',
                'published_at' => '2026-03-05T12:00:00Z',
            ]),
        ]);

        $service = new UpdateService();
        $result = $service->checkForUpdates();

        expect($result)
            ->available->toBeTrue()
            ->current_version->toBe('1.0.0')
            ->latest_version->toBe('2.0.0')
            ->release_name->toBe('Version 2.0.0');
    });

    it('reports no update when on latest version', function () {
        Http::fake([
            'api.github.com/repos/test/repo/releases/latest' => Http::response([
                'tag_name' => 'v1.0.0',
                'name' => 'Version 1.0.0',
                'body' => 'Current version',
                'html_url' => 'https://github.com/test/repo/releases/tag/v1.0.0',
                'published_at' => '2026-03-01T12:00:00Z',
            ]),
        ]);

        $service = new UpdateService();
        $result = $service->checkForUpdates();

        expect($result)
            ->available->toBeFalse()
            ->current_version->toBe('1.0.0')
            ->latest_version->toBe('1.0.0');
    });

    it('caches the result', function () {
        Http::fake([
            'api.github.com/repos/test/repo/releases/latest' => Http::response([
                'tag_name' => 'v2.0.0',
                'name' => 'Version 2.0.0',
            ]),
        ]);

        $service = new UpdateService();

        // First call fetches from API
        $service->checkForUpdates();

        // Second call uses cache
        $service->checkForUpdates();

        Http::assertSentCount(1);
    });

    it('handles API errors gracefully', function () {
        Http::fake([
            'api.github.com/repos/test/repo/releases/latest' => Http::response([], 500),
        ]);

        $service = new UpdateService();
        $result = $service->checkForUpdates();

        expect($result)
            ->available->toBeFalse()
            ->error->not->toBeNull();
    });
});

describe('forceCheck', function () {
    it('bypasses cache', function () {
        Http::fake([
            'api.github.com/repos/test/repo/releases/latest' => Http::response([
                'tag_name' => 'v2.0.0',
            ]),
        ]);

        $service = new UpdateService();

        $service->checkForUpdates();
        $service->forceCheck();

        Http::assertSentCount(2);
    });
});

describe('getReleases', function () {
    it('fetches list of releases', function () {
        Http::fake([
            'api.github.com/repos/test/repo/releases*' => Http::response([
                [
                    'tag_name' => 'v2.0.0',
                    'name' => 'Version 2.0.0',
                    'body' => 'New features',
                    'html_url' => 'https://github.com/test/repo/releases/tag/v2.0.0',
                    'published_at' => '2026-03-05T12:00:00Z',
                ],
                [
                    'tag_name' => 'v1.0.0',
                    'name' => 'Version 1.0.0',
                    'body' => 'Initial release',
                    'html_url' => 'https://github.com/test/repo/releases/tag/v1.0.0',
                    'published_at' => '2026-03-01T12:00:00Z',
                ],
            ]),
        ]);

        $service = new UpdateService();
        $releases = $service->getReleases();

        expect($releases)->toHaveCount(2);
        expect($releases[0])
            ->version->toBe('2.0.0')
            ->name->toBe('Version 2.0.0')
            ->is_newer->toBeTrue();
        expect($releases[1])
            ->version->toBe('1.0.0')
            ->is_current->toBeTrue();
    });

    it('returns empty array on API error', function () {
        Http::fake([
            'api.github.com/repos/test/repo/releases*' => Http::response([], 500),
        ]);

        $service = new UpdateService();
        $releases = $service->getReleases();

        expect($releases)->toBeEmpty();
    });
});

describe('getVersionInfo', function () {
    it('returns comprehensive version information', function () {
        Http::fake([
            'api.github.com/repos/test/repo/releases/latest' => Http::response([
                'tag_name' => 'v2.0.0',
                'name' => 'Version 2.0.0',
            ]),
            'api.github.com/repos/test/repo/releases*' => Http::response([
                ['tag_name' => 'v2.0.0', 'name' => 'Version 2.0.0'],
                ['tag_name' => 'v1.5.0', 'name' => 'Version 1.5.0'],
                ['tag_name' => 'v1.0.0', 'name' => 'Version 1.0.0'],
            ]),
        ]);

        $service = new UpdateService();
        $info = $service->getVersionInfo();

        expect($info)
            ->current_version->toBe('1.0.0')
            ->latest_version->toBe('2.0.0')
            ->update_available->toBeTrue()
            ->versions_behind->toBe(2);
        expect($info['releases'])->toHaveCount(3);
    });
});

describe('clearCache', function () {
    it('clears update-related caches', function () {
        Cache::put('cms_update_check', ['test' => true]);
        Cache::put('cms_releases', ['test' => true]);

        $service = new UpdateService();
        $service->clearCache();

        expect(Cache::has('cms_update_check'))->toBeFalse();
        expect(Cache::has('cms_releases'))->toBeFalse();
    });
});
