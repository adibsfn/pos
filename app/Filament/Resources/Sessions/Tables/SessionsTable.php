<?php

namespace App\Filament\Resources\Sessions\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll(20)
            ->columns([
                Tables\Columns\TextColumn::make('row')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->color(fn ($record) =>
                        $record->id === session()->getId() ? 'primary' : null
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Device')
                    ->formatStateUsing(function ($state) {

                        $agent = strtolower($state);

                        if (str_contains($agent, 'mobile')) {
                            $device = '📱 Mobile';
                        } elseif (str_contains($agent, 'tablet')) {
                            $device = '📲 Tablet';
                        } else {
                            $device = '💻 Desktop';
                        }

                        return "$device";
                    }),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Alamat IP')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Aktivitas Terakhir')
                    ->formatStateUsing(fn ($state) =>
                        \Carbon\Carbon::createFromTimestamp($state)->diffForHumans()
                    ),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {

                        $lastActivity = \Carbon\Carbon::createFromTimestamp($record->last_activity);

                        return $lastActivity->lt(now()->subMinutes(5))
                            ? 'Offline'
                            : 'Online';
                    })
                    ->badge()
                    ->color(fn ($state) => $state === 'Online' ? 'success' : 'danger'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record) => $record->id !== session()->getId())
                    ->action(function ($record) {

                        // log manual (AMAN)
                        activity()
                            ->causedBy(Auth::user())
                            ->withProperties([
                                'session_id' => $record->id,
                                'ip' => $record->ip_address,
                            ])
                            ->log('Session deleted');

                        // delete session
                        DB::table('sessions')->where('id', $record->id)->delete();
                    })
            ])
            ->toolbarActions([
            ]);
    }
}
