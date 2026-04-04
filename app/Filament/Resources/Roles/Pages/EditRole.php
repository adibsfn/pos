<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $permissions = $this->record->permissions->pluck('name')->toArray();

        $grouped = \Spatie\Permission\Models\Permission::all()
            ->groupBy(fn ($p) => last(explode('_', $p->name)));

        foreach ($grouped as $groupName => $group) {
            $data["permissions_{$groupName}"] = collect($permissions)
                ->intersect($group->pluck('name'))
                ->values()
                ->toArray();
        }

        return $data;
    }
    protected function afterSave(): void
    {
        $permissions = collect($this->form->getState())
            ->filter(fn ($v, $k) => str_starts_with($k, 'permissions_'))
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        $this->record->syncPermissions($permissions);
    }
}
