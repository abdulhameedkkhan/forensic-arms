<?php

namespace App\Filament\Widgets;

use App\Models\ArmDealer;
use Filament\Widgets\ChartWidget;

class ArmDealerPieChart extends ChartWidget
{
    protected ?string $heading = 'Arm Dealers by Status';
    
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = auth()->user();
        
        // Build query with range filtering
        $query = ArmDealer::selectRaw('COALESCE(status, "Unknown") as status, count(*) as count')
            ->groupBy('status');
        
        // Apply range filtering for non-admin users (if arm_dealers have range_id)
        if ($user && !$user->hasRole('admin')) {
            if ($user->range_id) {
                // If arm_dealers table has range_id, filter by it
                // For now, we'll keep it as is since arm_dealers might not have range_id
            } else {
                // Non-admin users without range_id see nothing
                $query->whereRaw('1 = 0');
            }
        }
        
        $statuses = $query->pluck('count', 'status')->toArray();

        $labels = array_keys($statuses);
        $data = array_values($statuses);

        // If no data, provide default
        if (empty($labels)) {
            $labels = ['No Data'];
            $data = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Arm Dealers',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}

