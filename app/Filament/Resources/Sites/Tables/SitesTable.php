<?php

namespace App\Filament\Resources\Sites\Tables;

use App\Models\Site;
use App\Services\SiteCheckerService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->searchable()
                    ->limit(50),
                IconColumn::make('notifications_enabled')
                    ->boolean()
                    ->label('Notify')
                    ->sortable(),
                IconColumn::make('is_up')
                    ->boolean()
                    ->label('Status')
                    ->sortable(),
                TextColumn::make('status_code')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 300 && $state < 400 => 'info',
                        $state >= 400 && $state < 500 => 'warning',
                        $state >= 500 => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('last_checked_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                TextColumn::make('error_message')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('check_now')
                    ->label('Check now')
                    ->icon('heroicon-m-arrow-path')
                    ->action(function (Site $record, SiteCheckerService $checker): void {
                        $updated = $checker->check($record);

                        if ($updated->is_up) {
                            Notification::make()
                                ->success()
                                ->title('Site is up')
                                ->body("")
                                ->send();

                            return;
                        }

                        $reason = filled($updated->status_code)
                            ? "Status: {$updated->status_code}"
                            : ($updated->error_message ?? 'Unknown error');

                        Notification::make()
                            ->danger()
                            ->title('Site is down')
                            ->body($reason)
                            ->send();
                    }),
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('last_checked_at', 'desc');
    }
}
