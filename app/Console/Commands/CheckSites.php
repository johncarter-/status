<?php

namespace App\Console\Commands;

use App\Mail\SiteDownNotification;
use App\Mail\SiteUpNotification;
use App\Models\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

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
        $sites = Site::all();

        if ($sites->isEmpty()) {
            $this->info('No sites to check.');

            return self::SUCCESS;
        }

        $this->info("Checking {$sites->count()} site(s)...");

        foreach ($sites as $site) {
            $this->checkSite($site);
        }

        $this->info('All sites checked!');

        return self::SUCCESS;
    }

    private function checkSite(Site $site): void
    {
        $this->line("Checking: {$site->name} ({$site->address})");

        // Store previous status to detect changes
        $wasUp = $site->is_up;

        try {
            $response = Http::timeout(10)->get($site->address);
            $isUp = $response->successful();

            $site->update([
                'is_up' => $isUp,
                'status_code' => $response->status(),
                'last_checked_at' => now(),
                'error_message' => $isUp ? null : "Status code: {$response->status()}",
            ]);

            if ($isUp) {
                $this->info("✓ {$site->name} is up (status: {$response->status()})");
                
                // Send email notification if site went from down to up
                if ($wasUp === false) {
                    $this->sendUpNotification($site);
                }
            } else {
                $this->error("✗ {$site->name} is down (status: {$response->status()})");
                
                // Send email notification if site went from up to down
                if ($wasUp) {
                    $this->sendDownNotification($site);
                }
            }
        } catch (\Exception $e) {
            $site->update([
                'is_up' => false,
                'status_code' => null,
                'last_checked_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            $this->error("✗ {$site->name} failed: {$e->getMessage()}");
            
            // Send email notification if site went from up to down
            if ($wasUp) {
                $this->sendDownNotification($site);
            }
        }
    }

    private function sendDownNotification(Site $site): void
    {
        try {
            Mail::to('john@johncarter.co.uk')->send(new SiteDownNotification($site));
            $this->warn("📧 Site down email notification sent to john@johncarter.co.uk");
        } catch (\Exception $e) {
            $this->error("Failed to send email notification: {$e->getMessage()}");
        }
    }

    private function sendUpNotification(Site $site): void
    {
        try {
            Mail::to('john@johncarter.co.uk')->send(new SiteUpNotification($site));
            $this->warn("📧 Site recovered email notification sent to john@johncarter.co.uk");
        } catch (\Exception $e) {
            $this->error("Failed to send email notification: {$e->getMessage()}");
        }
    }
}
