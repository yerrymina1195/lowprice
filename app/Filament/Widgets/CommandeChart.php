<?php

namespace App\Filament\Widgets;


use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CommandeChart extends ApexChartWidget
{

    
    protected static string $color = 'warning';
    protected static ?int $sort = 3 ;
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'commandeChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Total des commandes';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected int | string | array $columnSpan = 'full';

     protected function getOptions(): array
     {
         $data = Trend::model(Order::class) 
             ->between(
                start: Carbon::parse($this->filterFormData['date_start']), 
                end: Carbon::parse($this->filterFormData['date_end']), 
             )
             ->perDay()
             ->count(); 
  
         return [
             'chart' => [
                 'type' => 'line',
                 'height' => 300,
             ],
             'series' => [
                 [
                     'name' => 'TasksChart',
                     'data' => $data->map(fn (TrendValue $value) => $value->aggregate), 
                 ],
             ],
             'xaxis' => [
                //  'categories' => $data->map(fn (TrendValue $value) => $value->date), 
                 'categories' => $data->map(function (TrendValue $value) {
                    $date = is_string($value->date) ? Carbon::parse($value->date) : $value->date;
                    return $date->format('d-m-Y'); 
                }),
                 'labels' => [
                     'style' => [
                         'colors' => '#9ca3af',
                         'fontWeight' => 600,
                     ],
                 ],
             ],
             'yaxis' => [
                 'labels' => [
                     'style' => [
                         'colors' => '#9ca3af',
                         'fontWeight' => 600,
                     ],
                 ],
             ],
             'colors' => ['#6366f1'],
             'stroke' => [
                 'curve' => 'smooth',
             ],
         ];
     }


     protected function getFormSchema(): array
     {
         return [
             DatePicker::make('date_start')
                 ->default(now()->subMonth()),
             DatePicker::make('date_end')
                 ->default(now()),
         ];
     }
     

}
