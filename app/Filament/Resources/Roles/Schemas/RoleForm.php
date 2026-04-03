<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // 🔹 ROLE
                Section::make('Role')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Role')
                            ->required(),
                    ])
                    ->columns(1),

                // 🔹 PERMISSIONS (COLLAPSIBLE PER GROUP)
                Section::make('Permissions')
                    ->schema(function () {

                        $permissions = Permission::all()
                            ->groupBy(function ($p) {
                                return last(explode('_', $p->name)); // permission / role / user
                            });

                        return collect($permissions)->map(function ($group, $groupName) {

                            return Section::make(ucfirst($groupName))
                                ->schema([

                                    CheckboxList::make("permissions_{$groupName}")
                                        ->label('')
                                        ->options(
                                            $group->mapWithKeys(function ($p) {

                                                $parts = explode('_', $p->name);

                                                $action = ucfirst($parts[0]);
                                                $isAny = isset($parts[1]) && $parts[1] === 'any';

                                                return [
                                                    $p->name => $isAny
                                                        ? "{$action} All"
                                                        : $action
                                                ];
                                            })->toArray()
                                        )
                                        ->columns(2),

                                ])
                                ->collapsible(); // 🔥 collapse aktif
                        })->toArray();
                    })
                    ->columnSpanFull(),

            ]);
    }
}
