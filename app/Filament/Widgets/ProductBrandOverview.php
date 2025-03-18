<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ProductBrandOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Nikes', Product::query()->where('brand', 'nike')->count()),
            Stat::make('Puma', Product::query()->where('brand', 'puma')->count()),
            Stat::make('Adidas', Product::query()->where('brand', 'adidas')->count()),
            Stat::make('Users', User::count()),
        ];
    }
}
