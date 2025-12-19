<?php

namespace App\Filament\Resources\MakeResource\Pages;

use App\Filament\Resources\MakeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMake extends EditRecord
{
    protected static string $resource = MakeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete makes') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('edit makes') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete makes') ?? false),
        ];
    }
}
