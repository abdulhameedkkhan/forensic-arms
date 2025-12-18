<?php

namespace App\Filament\Widgets;

use App\Models\Range;
use App\Models\Weapon;
use Filament\Widgets\ChartWidget;

class WeaponsByRangeChart extends ChartWidget
{
    protected ?string $heading = 'Weapons by Range (Daily)';
    
    protected static ?int $sort = 4;

    protected ?string $description = 'Daily distribution of weapons across different ranges for the current month';

    protected function getData(): array
    {
        $user = auth()->user();
        
        // Get current month start and end dates
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        
        // Build query with range filtering
        $query = Weapon::query()
            ->with('range')
            ->whereNotNull('range_id')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd]);
        
        // Apply range filtering for non-admin users
        if ($user) {
            if ($user->range_id) {
                // Range users see only their range's data
                $query->where('range_id', (int) $user->range_id);
            } elseif (!$user->hasRole('admin')) {
                // Non-admin users without range_id see nothing
                $query->whereRaw('1 = 0');
            }
        }
        
        // Get weapons grouped by day and range
        $weapons = $query->get()
            ->groupBy(function ($weapon) {
                return $weapon->created_at->format('Y-m-d');
            })
            ->map(function ($dayWeapons) {
                return $dayWeapons->groupBy('range_id')
                    ->map(function ($rangeWeapons) {
                        $firstWeapon = $rangeWeapons->first();
                        return [
                            'range_name' => $firstWeapon->range->name ?? 'Unknown',
                            'count' => $rangeWeapons->count()
                        ];
                    });
            });
        
        // Generate all days of current month
        $days = [];
        $currentDate = $currentMonthStart->copy();
        while ($currentDate->lte($currentMonthEnd)) {
            $dayKey = $currentDate->format('Y-m-d');
            $days[] = [
                'key' => $dayKey,
                'label' => $currentDate->format('d M') // e.g., "01 Jan", "15 Jan"
            ];
            $currentDate->addDay();
        }
        
        // Get all unique ranges
        $allRanges = [];
        foreach ($weapons as $dayData) {
            foreach ($dayData as $rangeId => $rangeData) {
                if (!isset($allRanges[$rangeId])) {
                    $allRanges[$rangeId] = $rangeData['range_name'];
                }
            }
        }
        
        // Prepare datasets for each range
        $datasets = [];
        $colors = [
            ['bg' => 'rgb(59, 130, 246)', 'border' => 'rgb(37, 99, 235)'],
            ['bg' => 'rgb(16, 185, 129)', 'border' => 'rgb(5, 150, 105)'],
            ['bg' => 'rgb(245, 158, 11)', 'border' => 'rgb(217, 119, 6)'],
            ['bg' => 'rgb(239, 68, 68)', 'border' => 'rgb(220, 38, 38)'],
            ['bg' => 'rgb(139, 92, 246)', 'border' => 'rgb(124, 58, 237)'],
            ['bg' => 'rgb(236, 72, 153)', 'border' => 'rgb(219, 39, 119)'],
            ['bg' => 'rgb(14, 165, 233)', 'border' => 'rgb(2, 132, 199)'],
            ['bg' => 'rgb(34, 197, 94)', 'border' => 'rgb(22, 163, 74)'],
            ['bg' => 'rgb(251, 146, 60)', 'border' => 'rgb(234, 88, 12)'],
            ['bg' => 'rgb(168, 85, 247)', 'border' => 'rgb(147, 51, 234)'],
        ];
        
        $colorIndex = 0;
        foreach ($allRanges as $rangeId => $rangeName) {
            $data = [];
            foreach ($days as $day) {
                $dayData = $weapons->get($day['key'], collect());
                $rangeData = $dayData->get($rangeId);
                $data[] = $rangeData ? $rangeData['count'] : 0;
            }
            
            $color = $colors[$colorIndex % count($colors)];
            $datasets[] = [
                'label' => $rangeName,
                'data' => $data,
                'backgroundColor' => $color['bg'],
                'borderColor' => $color['border'],
                'borderWidth' => 1,
            ];
            $colorIndex++;
        }
        
        $labels = array_column($days, 'label');
        
        // If no data, provide default
        if (empty($datasets) || empty($labels)) {
            $labels = ['No Data'];
            $datasets = [[
                'label' => 'Weapons',
                'data' => [0],
                'backgroundColor' => 'rgb(59, 130, 246)',
                'borderColor' => 'rgb(37, 99, 235)',
            ]];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'stacked' => true,
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                ],
            ],
        ];
    }
}

