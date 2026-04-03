<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([

            // 🔹 SECTION 1: INFORMASI PRODUK
            Section::make('Informasi Produk')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama')
                            ->required(),

                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'nama')
                            ->required(),

                        Forms\Components\Select::make('satuan_id')
                            ->label('Satuan')
                            ->relationship('satuan', 'nama')
                            ->required(),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ]),
                ]),

            // 🔹 SECTION 2: BARCODE & IMAGE
            Section::make('Barcode & Gambar')
                ->schema([
                    Grid::make(2)->schema([

                        Forms\Components\TextInput::make('barcode')
                            ->label('Barcode')
                            ->suffix(
                                new \Illuminate\Support\HtmlString('
                                    <button type="button"
                                        onclick="startZXingScanner()"
                                        style="background:none;border:none;cursor:pointer;">
                                        📷
                                    </button>
                                ')
                            ),

                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar')
                            ->disk('public')
                            ->directory('products')
                            ->image()
                            ->imagePreviewHeight('120'),
                    ]),

                    View::make('filament.components.zxing-scanner'),
                ]),

            // 🔹 SECTION 3: HARGA & STOK
            Section::make('Harga & Stok')
                ->schema([
                    Grid::make(3)->schema([

                        Forms\Components\TextInput::make('harga_beli')
                            ->label('Harga Beli')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('harga_jual')
                            ->label('Harga Jual')
                            ->numeric()
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),

                        Forms\Components\TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('minimum_stock')
                            ->label('Min Stok')
                            ->numeric()
                            ->required(),
                    ]),
                ]),
        ]);


    }
}
