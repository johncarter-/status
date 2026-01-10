<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'name',
        'address',
        'notifications_enabled',
        'is_up',
        'status_code',
        'last_checked_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'is_up' => 'boolean',
            'notifications_enabled' => 'boolean',
            'last_checked_at' => 'datetime',
        ];
    }
}
