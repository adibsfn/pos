<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([

                Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Aktivitas')
                    ->badge()
                    ->color(function ($state) {

                        $state = strtolower($state);

                        return match (true) {
                            str_contains($state, 'created') => 'success',
                            str_contains($state, 'updated') => 'warning',
                            str_contains($state, 'deleted') => 'danger',
                            default => 'info',
                        };
                    }),

                Tables\Columns\TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since(),
            ])

            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Activity')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

            ]);
    }
}
