<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\TestEmailWidget;
use BackedEnum;
use Filament\Pages\Page;

class Tools extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Tools';

    protected static ?int $navigationSort = 100;

    /**
     * @var view-string
     */
    protected string $view = 'filament.pages.tools';

    public function getHeaderWidgets(): array
    {
        return [
            TestEmailWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}

