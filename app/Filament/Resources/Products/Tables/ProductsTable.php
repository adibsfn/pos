<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
// use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('row')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->searchable(['nama', 'barcode'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.nama')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('satuan.nama')
                    ->label('Satuan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_jual')
                    ->label('Harga Jual')
                    ->money('idr', true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) =>
                        $record->stock < $record->minimum_stock ? 'danger' : 'success'
                    ),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                // 📂 KATEGORI
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'nama'),

                // 📊 STATUS STOK
                Tables\Filters\SelectFilter::make('stock_status')
                    ->label('Status Stok')
                    ->options([
                        'habis' => 'Habis',
                        'menipis' => 'Menipis',
                        'aman' => 'Aman',
                    ])
                    ->query(function ($query, $data) {

                        $value = $data['value'] ?? null;

                        if ($value === 'habis') {
                            $query->where('stock', '<=', 0);
                        }

                        if ($value === 'menipis') {
                            $query->whereColumn('stock', '<=', 'minimum_stock')
                                ->where('stock', '>', 0);
                        }

                        if ($value === 'aman') {
                            $query->whereColumn('stock', '>', 'minimum_stock');
                        }

                        return $query;
                    }),


                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All') // 🔥 INI KUNCINYA
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
