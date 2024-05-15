<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Produit;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsUsers extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
            ->description("Nombres d'utilsateurs")
            ->descriptionColor('success')->descriptionIcon('heroicon-o-users')->chart([10,16,8,14])->color('success'),
            Stat::make('Produits', Produit::count())
            ->description("Nombres de Produit")
            ->descriptionColor('success')->descriptionIcon('heroicon-o-inbox-stack')->chart([10,16,8,14])->color('success'),
            Stat::make('commandess', Order::count())
            ->description("Nombres de commandes")
            ->descriptionColor('success')->descriptionIcon('heroicon-o-shopping-bag')->chart([10,16,8,14])->color('success'),
        ];
    }
}
