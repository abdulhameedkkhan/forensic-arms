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
        $statuses = ArmDealer::selectRaw('COALESCE(status, "Unknown") as status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

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

