<?php

namespace App\Filament\Resources\Satuans;

use App\Filament\Resources\Satuans\Pages\CreateSatuan;
use App\Filament\Resources\Satuans\Pages\EditSatuan;
use App\Filament\Resources\Satuans\Pages\ListSatuans;
use App\Filament\Resources\Satuans\Schemas\SatuanForm;
use App\Filament\Resources\Satuans\Tables\SatuansTable;
use App\Models\Satuan;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SatuanResource extends Resource
{
    protected static ?string $model = Satuan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;
    protected static UnitEnum|string|null $navigationGroup = 'Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Satuan';
    protected static ?string $recordTitleAttribute = 'satuan';

    public static function form(Schema $schema): Schema
    {
        return SatuanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SatuansTable::configure($table);
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
            'index' => ListSatuans::route('/'),
            'create' => CreateSatuan::route('/create'),
            'edit' => EditSatuan::route('/{record}/edit'),
        ];
    }
}
