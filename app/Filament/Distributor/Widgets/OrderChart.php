<?php

namespace App\Filament\Distributor\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class OrderChart extends ChartWidget
{
    protected static ?string $heading = 'Weekly Sales';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        // Initialize an array to store the counts for each day (Monday to Sunday)
        $data = array_fill(0, 7, 0);

        // Loop through each day of the week
        for ($i = 0; $i < 7; $i++) {
            // Calculate the start and end of the current day
            $startOfDay = $startOfWeek->copy()->addDays($i)->startOfDay();
            $endOfDay = $startOfWeek->copy()->addDays($i)->endOfDay();

            // Query the database to count attendance for the current day
            $sumSales = Order::whereBetween('created_at', [$startOfDay, $endOfDay])->where('pay_status', 'PAID')->sum('grand_total');

            // $count = $countRegistered + $countImported;

            // Store the count in the corresponding index of the $data array
            $data[$i] = $sumSales;//$count;
        }

        return [
            'datasets'=>[
                [
                    'label'=>'Weekly Sales ('.$startOfWeek->format('d M').'-'.$endOfWeek->format('d M').')',
                    'data'=>$data,
                    'barThickness'=>20,
                    'borderWidth'=>1,
                    'borderRadius'=>10
                ]
            ],
            'labels'=>[
                'Mon','Tue','Wed','Thu','Fri','Sat','Sun'
            ]
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales'=>[
                'x'=>[
                    'grid'=>[
                        'offset'=>false
                    ]
                ]
            ]
        ];
    }
    
}
