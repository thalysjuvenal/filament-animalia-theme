<?php

namespace Workbench\App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DemoStats extends StatsOverviewWidget
{
    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Pedidos', '1.280')->description('12% a mais')->color('success'),
            Stat::make('Receita', 'R$ 48.900')->description('3% a menos')->color('danger'),
            Stat::make('Clientes', '342')->description('Estável')->color('gray'),
        ];
    }
}
