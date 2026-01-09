<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\SiteCheckerService;
use Illuminate\Console\Command;

class CheckSites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-sites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all sites to ensure they are returning a 200 status code';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /** @var SiteCheckerService $checker */
        $checker = app(SiteCheckerService::class);

        $sites = Site::all();

        if ($sites->isEmpty()) {
            $this->info('No sites to check.');

            return self::SUCCESS;
        }

        $this->info("Checking {$sites->count()} site(s)...");

        foreach ($sites as $site) {
            $this->line("Checking: {$site->name} ({$site->address})");

            $checker->check($site);

            $site->refresh();

            if ($site->is_up) {
                $this->info("✓ {$site->name} is up (status: {$site->status_code})");
            } else {
                $reason = filled($site->status_code)
                    ? "status: {$site->status_code}"
                    : ($site->error_message ?? 'Unknown error');

                $this->error("✗ {$site->name} is down ({$reason})");
            }
        }

        $this->info('All sites checked!');

        return self::SUCCESS;
    }
}
