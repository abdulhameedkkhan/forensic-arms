<?php

namespace App\Filament\Resources\WeaponTypeResource\Pages;

use App\Filament\Resources\WeaponTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeaponType extends EditRecord
{
    protected static string $resource = WeaponTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete weapon types') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('edit weapon types') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete weapon types') ?? false),
        ];
    }
}
