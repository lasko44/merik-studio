<?php

namespace App\Console\Commands;

use App\Services\InvitationService;
use Illuminate\Console\Command;

/**
 * Marks expired invitations in the database.
 */
class ExpireInvitationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitations:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark expired invitations as expired';

    /**
     * Execute the console command.
     */
    public function handle(InvitationService $service): int
    {
        $count = $service->markExpiredInvitations();

        $this->info("Marked {$count} invitation(s) as expired.");

        return Command::SUCCESS;
    }
}
