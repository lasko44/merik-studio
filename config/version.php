<?php

/**
 * Version and Update Configuration.
 *
 * This file manages version tracking and update checking for the CMS.
 * Client installations use this to check for updates from the main repository.
 */

$versionFile = base_path('version.json');
$versionData = file_exists($versionFile)
    ? json_decode(file_get_contents($versionFile), true)
    : [];

return [
    /*
    |--------------------------------------------------------------------------
    | Current Version
    |--------------------------------------------------------------------------
    |
    | The current version of this CMS installation. This is read from the
    | version.json file in the project root.
    |
    */
    'current' => $versionData['version'] ?? '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Release Date
    |--------------------------------------------------------------------------
    |
    | The release date of the current version.
    |
    */
    'release_date' => $versionData['release_date'] ?? null,

    /*
    |--------------------------------------------------------------------------
    | GitHub Repository
    |--------------------------------------------------------------------------
    |
    | The GitHub repository to check for updates. This should be in the
    | format "owner/repo".
    |
    */
    'github_repo' => env('CMS_GITHUB_REPO', 'lasko44/merik-studio'),

    /*
    |--------------------------------------------------------------------------
    | GitHub Token (Optional)
    |--------------------------------------------------------------------------
    |
    | A GitHub personal access token for authenticated API requests.
    | This increases the rate limit from 60 to 5000 requests per hour.
    | Required for private repositories.
    |
    */
    'github_token' => env('CMS_GITHUB_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Update Check Enabled
    |--------------------------------------------------------------------------
    |
    | Whether to automatically check for updates. Can be disabled for
    | air-gapped or offline installations.
    |
    */
    'check_enabled' => env('CMS_UPDATE_CHECK_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Update Check Interval
    |--------------------------------------------------------------------------
    |
    | How often to check for updates, in hours. Default is every 24 hours.
    |
    */
    'check_interval_hours' => env('CMS_UPDATE_CHECK_INTERVAL', 24),

    /*
    |--------------------------------------------------------------------------
    | Notify Roles
    |--------------------------------------------------------------------------
    |
    | Which roles should see update notifications in the admin panel.
    |
    */
    'notify_roles' => ['Super Admin', 'Admin'],
];
