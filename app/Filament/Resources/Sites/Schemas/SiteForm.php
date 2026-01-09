<?php

namespace App\Filament\Resources\Sites\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('address')
                    ->url()
                    ->required()
                    ->placeholder('https://example.com'),
                Toggle::make('is_up')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('status_code')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('last_checked_at')
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('error_message')
                    ->columnSpanFull()
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
