<?php

namespace App\Filament\Resources\LicenseIssuerResource\Pages;

use App\Filament\Resources\LicenseIssuerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLicenseIssuers extends ListRecords
{
    protected static string $resource = LicenseIssuerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->can('create license issuers') ?? false),
        ];
    }
}
