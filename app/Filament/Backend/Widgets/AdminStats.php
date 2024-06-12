<?php

namespace App\Filament\Backend\Widgets;

use App\Models\AgentUser;
use App\Models\DistributorUser;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStats extends BaseWidget
{

    private function sumTotalSales()
    {
        $total = Order::where('pay_status', 'PAID')->sum('grand_total');

        return number_format($total, 2,'.',',');
    }

    private function countDistributor()
    {
        $total = DistributorUser::where('status', '1')->count('name');

        return number_format($total, 0,'.',',');
    }

    private function countAgent()
    {
        $total = AgentUser::where('status', '1')->count('name');

        return number_format($total, 0,'.',',');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Sales', $this->sumTotalSales()),
            Stat::make('Distributor Count', $this->countDistributor()),
            Stat::make('Agent Count', $this->countAgent()),
        ];
    }
}
