<?php

namespace App\Filament\Resources\LicenseIssuerResource\Pages;

use App\Filament\Resources\LicenseIssuerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLicenseIssuer extends EditRecord
{
    protected static string $resource = LicenseIssuerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete license issuers') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('edit license issuers') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete license issuers') ?? false),
        ];
    }
}
