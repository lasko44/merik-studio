<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\SetupWizardService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Handles async site generation from setup wizard data.
 */
class GenerateSiteJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $userId,
        protected array $wizardData,
        protected string $jobId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SetupWizardService $service): void
    {
        $user = User::findOrFail($this->userId);

        try {
            // Update status to processing
            $this->updateStatus('processing', 'Generating your site content...');

            $result = $service->generate($user, $this->wizardData);

            // Update status to completed
            $this->updateStatus('completed', 'Site generated successfully!', $result);

            Log::info('Site generation completed', [
                'user_id' => $this->userId,
                'job_id' => $this->jobId,
                'pages_created' => $result['pages_created'] ?? 0,
            ]);
        } catch (\Exception $e) {
            $this->updateStatus('failed', 'Failed to generate site: ' . $e->getMessage());

            Log::error('Site generation failed', [
                'user_id' => $this->userId,
                'job_id' => $this->jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Update the job status in cache.
     */
    protected function updateStatus(string $status, string $message, ?array $result = null): void
    {
        Cache::put("site_generation:{$this->jobId}", [
            'status' => $status,
            'message' => $message,
            'result' => $result,
            'updated_at' => now()->toIso8601String(),
        ], now()->addHours(1));
    }

    /**
     * Get the cache key for this job.
     */
    public static function getCacheKey(string $jobId): string
    {
        return "site_generation:{$jobId}";
    }
}
