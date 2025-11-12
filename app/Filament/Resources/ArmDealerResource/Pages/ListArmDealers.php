<?php

namespace App\Filament\Resources\ArmDealerResource\Pages;

use App\Filament\Resources\ArmDealerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArmDealers extends ListRecords
{
    protected static string $resource = ArmDealerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->can('create arm dealers') ?? false),
        ];
    }
}

