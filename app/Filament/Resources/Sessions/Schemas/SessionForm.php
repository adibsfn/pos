<?php

namespace App\Filament\Resources\Sessions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;

class SessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Placeholder::make('user_name')
                    ->label('Nama Pengguna')
                    ->content(fn ($record) => $record->user?->name ?? '-'),

                Placeholder::make('user_agent')
                    ->label('Device')
                    ->content(fn ($record) => $record->user_agent),

                Placeholder::make('ip_address')
                    ->label('Alamat IP')
                    ->content(fn ($record) => $record->ip_address),

                Placeholder::make('last_activity')
                    ->label('Aktivitas Terakhir')
                    ->content(fn ($record) =>
                        \Carbon\Carbon::createFromTimestamp($record->last_activity)->diffForHumans()
                    ),
            ]);
    }
}
