<?php

namespace App\Filament\Resources\BoreResource\Pages;

use App\Filament\Resources\BoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBore extends EditRecord
{
    protected static string $resource = BoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete bores') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('edit bores') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete bores') ?? false),
        ];
    }
}
