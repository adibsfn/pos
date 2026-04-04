<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('invoice')
                    ->default(fn () => 'INV-' . now()->timestamp)
                    ->disabled(),

                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),

                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $product = \App\Models\Product::find($state);
                                if ($product) {
                                    $set('price', $product->price);
                                }
                            })
                            ->required(),

                        Forms\Components\TextInput::make('qty')
                            ->numeric()
                            ->default(1)
                            ->reactive(),

                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(true)
                            ->afterStateHydrated(function ($state, $record, callable $set) {
                                $set('total', $record?->qty * $record?->price);
                            }),
                    ])
            ]);
    }
}
