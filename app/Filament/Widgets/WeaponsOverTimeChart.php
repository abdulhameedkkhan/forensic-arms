<?php

namespace App\Filament\Widgets;

use App\Models\Weapon;
use Filament\Widgets\ChartWidget;

class WeaponsOverTimeChart extends ChartWidget
{
    protected ?string $heading = 'Weapons Added Over Time';
    
    protected static ?int $sort = 5;

    protected ?string $description = 'Monthly trend of weapons added to the system';

    protected function getData(): array
    {
        try {
            $user = auth()->user();
            
            // Build query with range filtering
            $query = Weapon::query();
            
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
            
            // Get weapons grouped by month (database-agnostic approach)
            $weapons = $query->get()
                ->groupBy(function ($weapon) {
                    return $weapon->created_at ? $weapon->created_at->format('Y-m') : 'unknown';
                })
                ->map(function ($group) {
                    return $group->count();
                })
                ->toArray();
            
            // Generate last 12 months
            $months = [];
            $data = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i months"));
                $months[] = date('M Y', strtotime("-$i months"));
                $data[] = $weapons[$month] ?? 0;
            }

            // If no data, provide default
            if (empty($data) || array_sum($data) === 0) {
                $months = [];
                $data = [];
                // Generate empty months for display
                for ($i = 11; $i >= 0; $i--) {
                    $months[] = date('M Y', strtotime("-$i months"));
                    $data[] = 0;
                }
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Weapons Added',
                        'data' => $data,
                        'borderColor' => 'rgb(59, 130, 246)',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                ],
                'labels' => $months,
            ];
        } catch (\Exception $e) {
            // Return empty chart data on error
            $months = [];
            $data = [];
            for ($i = 11; $i >= 0; $i--) {
                $months[] = date('M Y', strtotime("-$i months"));
                $data[] = 0;
            }
            
            return [
                'datasets' => [
                    [
                        'label' => 'Weapons Added',
                        'data' => $data,
                        'borderColor' => 'rgb(59, 130, 246)',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                ],
                'labels' => $months,
            ];
        }
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
        ];
    }
}

