<?php

namespace App\Filament\Resources\Sessions;

use App\Filament\Resources\Sessions\Pages\ListSessions;
use App\Filament\Resources\Sessions\Schemas\SessionForm;
use App\Filament\Resources\Sessions\Tables\SessionsTable;
use App\Models\Session;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SessionResource extends Resource
{
    protected static ?string $model = Session::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $recordTitleAttribute = 'session';
    protected static UnitEnum|string|null $navigationGroup = 'Security';
    protected static ?string $navigationLabel = 'Sesion';
    protected static ?int $navigationSort = 3;
    public static function getPluralModelLabel(): string
    {
        return 'Sesi Pengguna';
    }
    public static function form(Schema $schema): Schema
    {
        return SessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SessionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSessions::route('/'),
        ];
    }
}
