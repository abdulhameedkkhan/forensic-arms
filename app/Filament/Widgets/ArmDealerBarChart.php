<?php

namespace App\Filament\Widgets;

use App\Models\ArmDealer;
use Filament\Widgets\ChartWidget;

class ArmDealerBarChart extends ChartWidget
{
    protected ?string $heading = 'Arm Dealers by District';
    
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $user = auth()->user();
        
        // Build query with range filtering
        $query = ArmDealer::selectRaw('district, count(*) as count')
            ->whereNotNull('district')
            ->groupBy('district')
            ->orderBy('count', 'desc')
            ->limit(10);
        
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
        
        $districts = $query->pluck('count', 'district')->toArray();

        $labels = array_keys($districts);
        $data = array_values($districts);

        // If no data, provide default
        if (empty($labels)) {
            $labels = ['No Data'];
            $data = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Arm Dealers Count',
                    'data' => $data,
                    'backgroundColor' => 'rgb(54, 162, 235)',
                    'borderColor' => 'rgb(54, 162, 235)',
                ],
            ],
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
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}

