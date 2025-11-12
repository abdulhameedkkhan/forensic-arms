<?php

namespace App\Filament\Resources\WeaponResource\Pages;

use App\Filament\Resources\WeaponResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeapon extends EditRecord
{
    protected static string $resource = WeaponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete weapons') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('edit weapons') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete weapons') ?? false),
        ];
    }
}

