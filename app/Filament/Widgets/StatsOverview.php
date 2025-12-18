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
        // Models that have range_id: User, ArmDealer, Weapon
        // Models that don't have range_id: Role, Permission
        $modelsWithRangeId = [User::class, ArmDealer::class, Weapon::class];
        
        $getFilteredQuery = function ($modelClass) use ($user, $modelsWithRangeId) {
            $query = $modelClass::query();
            
            // Only apply range_id filter if the model has this column
            if (in_array($modelClass, $modelsWithRangeId) && $user) {
                if ($user->range_id) {
                    // Range users see only their range's data
                    return $query->where('range_id', (int) $user->range_id);
                } elseif (!$user->hasRole('admin')) {
                    // Non-admin users without range_id see nothing
                    return $query->whereRaw('1 = 0');
                }
            }
            
            // Admin users see all data, or models without range_id show all
            return $query;
        };

        // Helper function to get last 7 days trend data
        $getTrendData = function ($modelClass, $dateColumn = 'created_at') use ($getFilteredQuery) {
            $trend = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->startOfDay();
                $nextDate = $date->copy()->endOfDay();
                
                $count = (clone $getFilteredQuery($modelClass))
                    ->whereBetween($dateColumn, [$date, $nextDate])
                    ->count();
                
                $trend[] = $count;
            }
            return $trend;
        };

        return [
            Stat::make('Total Users', $getFilteredQuery(User::class)->count())
                ->description('Active users in the system')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart($getTrendData(User::class)),

            Stat::make('Total Arm Dealers', $getFilteredQuery(ArmDealer::class)->count())
                ->description('Registered arm dealers')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary')
                ->chart($getTrendData(ArmDealer::class)),

            Stat::make('Total Weapons', $getFilteredQuery(Weapon::class)->count())
                ->description('Weapons tracked in BSLW')
                ->descriptionIcon('heroicon-m-rocket-launch')
                ->color('info')
                ->chart($getTrendData(Weapon::class)),

            Stat::make('Total Roles', $user && $user->hasRole('admin') ? Role::count() : 0)
                ->description('System roles configured')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning')
                ->chart($user && $user->hasRole('admin') ? [0, 0, 0, 0, 0, 0, 0] : [0, 0, 0, 0, 0, 0, 0]),

            Stat::make('Total Permissions', $user && $user->hasRole('admin') ? Permission::count() : 0)
                ->description('Access rules available')
                ->descriptionIcon('heroicon-m-key')
                ->color('danger')
                ->chart($user && $user->hasRole('admin') ? [0, 0, 0, 0, 0, 0, 0] : [0, 0, 0, 0, 0, 0, 0]),
        ];
    }
}

