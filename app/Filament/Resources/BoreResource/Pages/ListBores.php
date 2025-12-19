<?php

namespace App\Filament\Resources\BoreResource\Pages;

use App\Filament\Resources\BoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBores extends ListRecords
{
    protected static string $resource = BoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->can('create bores') ?? false),
        ];
    }
}
