<?php

namespace App\Filament\Resources\WeaponResource\Pages;

use App\Filament\Resources\WeaponResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListWeapons extends ListRecords
{
    protected static string $resource = WeaponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
            //     ->visible(fn () => auth()->user()?->can('create weapons') ?? false),
        ];
    }

    protected function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        
        if ($user) {
            if ($user->range_id) {
                // Range users see only their range's data
                $query->where('range_id', (int) $user->range_id);
            } elseif (!$user->hasRole('admin')) {
                // Non-admin users without range_id see nothing
                $query->whereRaw('1 = 0');
            }
            // Admin users see all data (no filter applied)
        }
        
        return $query;
    }
}

