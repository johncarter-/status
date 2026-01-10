<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\SiteDownNotification;
use App\Mail\SiteUpNotification;
use App\Models\Site;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class SiteCheckerService
{
    public function check(Site $site): Site
    {
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
                // Send email notification if site went from down to up
                if ($wasUp === false) {
                    $this->sendUpNotification($site);
                }
            } else {
                // Send email notification if site went from up to down
                if ($wasUp) {
                    $this->sendDownNotification($site);
                }
            }
        } catch (Throwable $e) {
            $site->update([
                'is_up' => false,
                'status_code' => null,
                'last_checked_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            // Send email notification if site went from up to down
            if ($wasUp) {
                $this->sendDownNotification($site);
            }
        }

        return $site->refresh();
    }

    private function sendDownNotification(Site $site): void
    {
        if (! $site->notifications_enabled) {
            return;
        }

        Mail::to('john@johncarter.co.uk')->send(new SiteDownNotification($site));
    }

    private function sendUpNotification(Site $site): void
    {
        if (! $site->notifications_enabled) {
            return;
        }

        Mail::to('john@johncarter.co.uk')->send(new SiteUpNotification($site));
    }
}



