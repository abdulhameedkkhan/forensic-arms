<?php

namespace App\Filament\Resources\ArmDealerResource\Pages;

use App\Filament\Resources\ArmDealerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewArmDealer extends ViewRecord
{
    protected static string $resource = ArmDealerResource::class;
    
    protected string $view = 'filament.resources.arm-dealer-resource.pages.view-arm-dealer';

    public function getHeading(): string
    {
        return 'Arm Dealer Details';
    }

    public function getSubheading(): ?string
    {
        return sprintf('%s • %s', $this->record->name, $this->record->shop_name);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => auth()->user()?->can('edit arm dealers') ?? false),
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete arm dealers') ?? false),
        ];
    }
}