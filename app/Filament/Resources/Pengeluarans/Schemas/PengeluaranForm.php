<?php

namespace App\Filament\Resources\Pengeluarans\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;

class PengeluaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama/Keterangan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->date(),
                Forms\Components\TextInput::make('jumlah_pengeluaran')
                    ->label('Jumlah Pengeluaran')
                    ->required()
                    ->numeric(),
            ]);
    }
}
