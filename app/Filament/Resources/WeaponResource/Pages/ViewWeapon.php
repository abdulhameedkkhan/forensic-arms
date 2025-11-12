<?php

namespace App\Filament\Resources\WeaponResource\Pages;

use App\Filament\Resources\WeaponResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWeapon extends ViewRecord
{
    protected static string $resource = WeaponResource::class;
    protected string $view = 'filament.resources.weapon-resource.pages.view-weapon';

    public function getHeading(): string
    {
        return 'Weapon Details';
    }

    public function getSubheading(): ?string
    {
        return sprintf('Weapon #%s • %s', $this->record->weapon_no, $this->record->cnic);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => auth()->user()?->can('edit weapons') ?? false),
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete weapons') ?? false),
        ];
    }
}
