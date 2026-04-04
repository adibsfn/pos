<?php

namespace App\Filament\Resources\Pengeluarans\Tables;

use Filament\Actions\BulkActionGroup;
// use Filament\Actions\DeleteBulkAction;

use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables;

class PengeluaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('row')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Keterangan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_pengeluaran')
                    ->label('Jumlah Pengeluaran')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver()
                    ->modalHeading('Tambah Satuan')
                    ->modalWidth('lg'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->slideOver()
                    ->modalHeading('Edit Satuan')
                    ->modalWidth('lg'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
