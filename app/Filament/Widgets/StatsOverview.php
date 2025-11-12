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
        return [
            Stat::make('Total Users', User::count())
                ->description('Active users in the system')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Total Arm Dealers', ArmDealer::count())
                ->description('Registered arm dealers')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7]),

            Stat::make('Total Weapons', Weapon::count())
                ->description('Weapons tracked in BLMS')
                ->descriptionIcon('heroicon-m-rocket-launch')
                ->color('info')
                ->chart([3, 4, 6, 5, 6, 8, 9]),

            Stat::make('Total Roles', Role::count())
                ->description('System roles configured')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning')
                ->chart([2, 3, 2, 3, 2, 3, 2]),

            Stat::make('Total Permissions', Permission::count())
                ->description('Access rules available')
                ->descriptionIcon('heroicon-m-key')
                ->color('danger')
                ->chart([5, 6, 5, 7, 5, 6, 5]),
        ];
    }
}

