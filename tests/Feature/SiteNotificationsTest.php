<?php

declare(strict_types=1);

use App\Mail\SiteDownNotification;
use App\Models\Site;
use App\Services\SiteCheckerService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

test('does not send notifications when disabled for a site', function (): void {
    Mail::fake();
    Http::fake([
        '*' => Http::response('Down', 500),
    ]);

    $site = Site::create([
        'name' => 'Example',
        'address' => 'https://example.com',
        'notifications_enabled' => false,
        'is_up' => true,
    ]);

    $updated = app(SiteCheckerService::class)->check($site);

    expect($updated->is_up)->toBeFalse();
    Mail::assertNothingSent();
});

test('sends a down notification when enabled for a site', function (): void {
    Mail::fake();
    Http::fake([
        '*' => Http::response('Down', 500),
    ]);

    $site = Site::create([
        'name' => 'Example',
        'address' => 'https://example.com',
        'notifications_enabled' => true,
        'is_up' => true,
    ]);

    $updated = app(SiteCheckerService::class)->check($site);

    expect($updated->is_up)->toBeFalse();
    Mail::assertSent(SiteDownNotification::class, fn (SiteDownNotification $mailable) => $mailable->site->is($site));
});


