<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_person')
                    ->label('Contact Person')
                    ->maxLength(255),
                Forms\Components\TextInput::make('telepon')
                    ->label('Telepon')
                    ->maxLength(20),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->maxLength(255),
                Forms\Components\Textarea::make('alamat')
                    ->label('Alamat')
                    ->maxLength(65535),
            ]);
    }
}
