<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Handles version checking and update management.
 */
class UpdateService
{
    protected string $githubRepo;
    protected ?string $githubToken;
    protected string $currentVersion;
    protected int $cacheMinutes = 60;

    public function __construct()
    {
        $this->githubRepo = config('version.github_repo');
        $this->githubToken = config('version.github_token');
        $this->currentVersion = config('version.current');
    }

    /**
     * Get the current installed version.
     */
    public function getCurrentVersion(): string
    {
        return $this->currentVersion;
    }

    /**
     * Check if updates are available.
     */
    public function checkForUpdates(): array
    {
        if (!config('version.check_enabled')) {
            return [
                'available' => false,
                'current_version' => $this->currentVersion,
                'latest_version' => $this->currentVersion,
                'checked_at' => now()->toISOString(),
            ];
        }

        $cacheKey = 'cms_update_check';

        return Cache::remember($cacheKey, $this->cacheMinutes * 60, function () {
            return $this->fetchLatestRelease();
        });
    }

    /**
     * Force a fresh update check (bypasses cache).
     */
    public function forceCheck(): array
    {
        Cache::forget('cms_update_check');
        Cache::forget('cms_releases');

        return $this->checkForUpdates();
    }

    /**
     * Fetch the latest release from GitHub.
     */
    protected function fetchLatestRelease(): array
    {
        try {
            $response = $this->makeGitHubRequest("/repos/{$this->githubRepo}/releases/latest");

            if (!$response->successful()) {
                Log::warning('Failed to fetch latest release from GitHub', [
                    'status' => $response->status(),
                    'repo' => $this->githubRepo,
                ]);

                return $this->errorResponse('Failed to check for updates');
            }

            $release = $response->json();
            $latestVersion = ltrim(Arr::get($release, 'tag_name', ''), 'v');

            return [
                'available' => version_compare($latestVersion, $this->currentVersion, '>'),
                'current_version' => $this->currentVersion,
                'latest_version' => $latestVersion,
                'release_name' => Arr::get($release, 'name'),
                'release_notes' => Arr::get($release, 'body'),
                'release_url' => Arr::get($release, 'html_url'),
                'published_at' => Arr::get($release, 'published_at'),
                'checked_at' => now()->toISOString(),
            ];
        } catch (\Exception $e) {
            Log::error('Error checking for updates', [
                'error' => $e->getMessage(),
                'repo' => $this->githubRepo,
            ]);

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get all releases (for changelog).
     */
    public function getReleases(int $limit = 10): array
    {
        $cacheKey = 'cms_releases';

        return Cache::remember($cacheKey, $this->cacheMinutes * 60, function () use ($limit) {
            return $this->fetchReleases($limit);
        });
    }

    /**
     * Fetch releases from GitHub.
     */
    protected function fetchReleases(int $limit): array
    {
        try {
            $response = $this->makeGitHubRequest("/repos/{$this->githubRepo}/releases", [
                'per_page' => $limit,
            ]);

            if (!$response->successful()) {
                return [];
            }

            $releases = $response->json();

            return array_map(function ($release) {
                $version = ltrim(Arr::get($release, 'tag_name', ''), 'v');

                return [
                    'version' => $version,
                    'name' => Arr::get($release, 'name'),
                    'notes' => Arr::get($release, 'body'),
                    'url' => Arr::get($release, 'html_url'),
                    'published_at' => Arr::get($release, 'published_at'),
                    'is_current' => $version === $this->currentVersion,
                    'is_newer' => version_compare($version, $this->currentVersion, '>'),
                ];
            }, $releases);
        } catch (\Exception $e) {
            Log::error('Error fetching releases', ['error' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Get version comparison details.
     */
    public function getVersionInfo(): array
    {
        $updateInfo = $this->checkForUpdates();
        $releases = $this->getReleases();

        $versionsBeind = 0;
        foreach ($releases as $release) {
            if (version_compare($release['version'], $this->currentVersion, '>')) {
                $versionsBeind++;
            }
        }

        return [
            'current_version' => $this->currentVersion,
            'latest_version' => Arr::get($updateInfo, 'latest_version', $this->currentVersion),
            'update_available' => Arr::get($updateInfo, 'available', false),
            'versions_behind' => $versionsBeind,
            'releases' => $releases,
            'checked_at' => Arr::get($updateInfo, 'checked_at'),
            'release_date' => config('version.release_date'),
        ];
    }

    /**
     * Make a request to the GitHub API.
     */
    protected function makeGitHubRequest(string $endpoint, array $query = []): \Illuminate\Http\Client\Response
    {
        $request = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'Merik-CMS-Update-Checker',
        ]);

        if ($this->githubToken) {
            $request->withToken($this->githubToken);
        }

        return $request->get("https://api.github.com{$endpoint}", $query);
    }

    /**
     * Return an error response structure.
     */
    protected function errorResponse(string $message): array
    {
        return [
            'available' => false,
            'current_version' => $this->currentVersion,
            'latest_version' => $this->currentVersion,
            'error' => $message,
            'checked_at' => now()->toISOString(),
        ];
    }

    /**
     * Clear all update-related caches.
     */
    public function clearCache(): void
    {
        Cache::forget('cms_update_check');
        Cache::forget('cms_releases');
    }
}
