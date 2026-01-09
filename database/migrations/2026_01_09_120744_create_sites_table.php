<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->boolean('is_up')->nullable();
            $table->integer('status_code')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }
};
