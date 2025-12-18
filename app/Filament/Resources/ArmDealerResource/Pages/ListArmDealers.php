<?php

namespace App\Filament\Resources\ArmDealerResource\Pages;

use App\Filament\Resources\ArmDealerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

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

    protected function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        
        if ($user) {
            if ($user->range_id) {
                // Users with range_id see only their range's data
                $query->where('range_id', (int) $user->range_id);
            } elseif (!$user->hasRole('admin')) {
                // Non-admin users without range_id see nothing
                $query->whereRaw('1 = 0');
            }
            // Admin users without range_id see all data (no filter applied)
        }
        
        return $query;
    }
}

