<?php

namespace App\Console\Commands;

use App\Services\UpdateService;
use Illuminate\Console\Command;

/**
 * Checks for available CMS updates from GitHub.
 */
class CheckForUpdatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:check-updates {--force : Force a fresh check, bypassing cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for available CMS updates';

    /**
     * Execute the console command.
     */
    public function handle(UpdateService $service): int
    {
        $this->info('Checking for updates...');

        $result = $this->option('force')
            ? $service->forceCheck()
            : $service->checkForUpdates();

        if (isset($result['error'])) {
            $this->error("Error: {$result['error']}");

            return Command::FAILURE;
        }

        $this->table(
            ['Property', 'Value'],
            [
                ['Current Version', $result['current_version']],
                ['Latest Version', $result['latest_version']],
                ['Update Available', $result['available'] ? 'Yes' : 'No'],
                ['Checked At', $result['checked_at']],
            ]
        );

        if ($result['available']) {
            $this->newLine();
            $this->warn("A new version ({$result['latest_version']}) is available!");

            if (isset($result['release_name'])) {
                $this->info("Release: {$result['release_name']}");
            }

            if (isset($result['release_url'])) {
                $this->info("URL: {$result['release_url']}");
            }
        } else {
            $this->newLine();
            $this->info('You are running the latest version.');
        }

        return Command::SUCCESS;
    }
}
