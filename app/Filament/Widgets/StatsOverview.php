<?php

namespace App\Filament\Widgets;

use App\Models\ArmDealer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Weapon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        
        // Helper function to get filtered query based on user's range_id
        $getFilteredQuery = function ($modelClass) use ($user) {
            $query = $modelClass::query();
            
            if ($user) {
                if ($user->range_id) {
                    // Range users see only their range's data
                    return $query->where('range_id', (int) $user->range_id);
                } elseif (!$user->hasRole('admin')) {
                    // Non-admin users without range_id see nothing
                    return $query->whereRaw('1 = 0');
                }
            }
            
            // Admin users see all data
            return $query;
        };

        return [
            Stat::make('Total Users', $getFilteredQuery(User::class)->count())
                ->description('Active users in the system')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Total Arm Dealers', $getFilteredQuery(ArmDealer::class)->count())
                ->description('Registered arm dealers')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7]),

            Stat::make('Total Weapons', $getFilteredQuery(Weapon::class)->count())
                ->description('Weapons tracked in BSLW')
                ->descriptionIcon('heroicon-m-rocket-launch')
                ->color('info')
                ->chart([3, 4, 6, 5, 6, 8, 9]),

            Stat::make('Total Roles', $user && $user->hasRole('admin') ? Role::count() : 0)
                ->description('System roles configured')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning')
                ->chart([2, 3, 2, 3, 2, 3, 2]),

            Stat::make('Total Permissions', $user && $user->hasRole('admin') ? Permission::count() : 0)
                ->description('Access rules available')
                ->descriptionIcon('heroicon-m-key')
                ->color('danger')
                ->chart([5, 6, 5, 7, 5, 6, 5]),
        ];
    }
}

