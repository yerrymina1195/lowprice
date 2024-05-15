<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static string $color = 'warning';
    protected static ?int $sort = 3 ;

    protected function getData(): array
    {
        $data = $this->getOrdersPerMonth();


        return [
            'datasets' => [
                [
                    'label' => 'Commandes Crées',
                    'data' => $data['ordersPerMonth'],
                   
                ],
            ],
            'labels' => $data['months'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }






    private function getOrdersPerMonth(): array
    {
      $now= Carbon::now();

      $ordersPerMonth = [];

      $months= collect(range(1,12))->map(function($month) use($now, &$ordersPerMonth){
        $count= Order::whereMonth('created_at', Carbon::parse($now->month($month)->format('Y-m')))->count();
        $ordersPerMonth[]= $count;

        return $now->month($month)->format('M');
      })->toArray();

      return [
        'ordersPerMonth' => $ordersPerMonth,
        'months' => $months,
      ];
    }
}
