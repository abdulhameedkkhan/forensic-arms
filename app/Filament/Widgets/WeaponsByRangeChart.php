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

    public static function canView(): bool
    {
        return auth()->user()?->can('view weapons') ?? false;
    }

    protected function getData(): array
    {
        try {
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
                    return $weapon->created_at ? $weapon->created_at->format('Y-m-d') : 'unknown';
                })
                ->map(function ($dayWeapons) {
                    return $dayWeapons->groupBy('range_id')
                        ->map(function ($rangeWeapons) {
                            $firstWeapon = $rangeWeapons->first();
                            if (!$firstWeapon) {
                                return [
                                    'range_name' => 'Unknown',
                                    'count' => 0
                                ];
                            }
                            return [
                                'range_name' => $firstWeapon->range ? ($firstWeapon->range->name ?? 'Unknown') : 'Unknown',
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
                if ($dayData && is_iterable($dayData)) {
                    foreach ($dayData as $rangeId => $rangeData) {
                        if (!isset($allRanges[$rangeId]) && isset($rangeData['range_name'])) {
                            $allRanges[$rangeId] = $rangeData['range_name'];
                        }
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
                    $data[] = ($rangeData && isset($rangeData['count'])) ? $rangeData['count'] : 0;
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
            
            // If no data, provide default with all days
            if (empty($datasets)) {
                // Still show all days but with zero data
                $datasets = [[
                    'label' => 'Weapons',
                    'data' => array_fill(0, count($labels), 0),
                    'backgroundColor' => 'rgb(59, 130, 246)',
                    'borderColor' => 'rgb(37, 99, 235)',
                    'borderWidth' => 1,
                ]];
            }

            return [
                'datasets' => $datasets,
                'labels' => $labels,
            ];
        } catch (\Exception $e) {
            // Return empty chart data on error
            $currentMonthStart = now()->startOfMonth();
            $currentMonthEnd = now()->endOfMonth();
            $days = [];
            $currentDate = $currentMonthStart->copy();
            while ($currentDate->lte($currentMonthEnd)) {
                $days[] = [
                    'key' => $currentDate->format('Y-m-d'),
                    'label' => $currentDate->format('d M')
                ];
                $currentDate->addDay();
            }
            $labels = array_column($days, 'label');
            
            return [
                'datasets' => [[
                    'label' => 'Weapons',
                    'data' => array_fill(0, count($labels), 0),
                    'backgroundColor' => 'rgb(59, 130, 246)',
                    'borderColor' => 'rgb(37, 99, 235)',
                    'borderWidth' => 1,
                ]],
                'labels' => $labels,
            ];
        }
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

