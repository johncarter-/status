<?php

declare(strict_types=1);

use App\Models\Site;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::get('/email-preview/site-down', function () {
    $site = Site::first() ?? new Site([
        'name' => 'Example Site',
        'address' => 'https://example.com',
        'status_code' => 500,
        'error_message' => 'Status code: 500',
        'last_checked_at' => now(),
    ]);
    
    return view('emails.site-down', ['site' => $site]);
});

Route::get('/email-preview/site-up', function () {
    $site = Site::first() ?? new Site([
        'name' => 'Example Site',
        'address' => 'https://example.com',
        'status_code' => 200,
        'last_checked_at' => now(),
    ]);
    
    return view('emails.site-up', ['site' => $site]);
});
