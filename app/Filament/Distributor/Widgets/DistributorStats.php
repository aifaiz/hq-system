<?php

namespace App\Filament\Distributor\Widgets;

use App\Models\AgentUser;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DistributorStats extends BaseWidget
{
    private $distributorID;

    public function __construct()
    {
        $this->distributorID = auth()->id();
    }
    private function getTotalSales()
    {
        
        $total = Order::where('distributor_id', $this->distributorID)->where('pay_status','PAID')->sum('grand_total');

        return number_format($total, 2,'.',',');
    }

    private function countAgents()
    {
        $total = AgentUser::where('distributor_id', $this->distributorID)->count('distributor_id');
        return number_format($total, 0,'.',',');
    }

    private function sumTodaySales()
    {
        $total = Order::where('distributor_id', $this->distributorID)
            ->where('pay_status','PAID')
            ->whereDate('created_at', Carbon::today()->format('Y-m-d'))
            ->sum('grand_total');
        return number_format($total, 2,'.',',');
    }


    protected function getStats(): array
    {
        return [
            Stat::make('Total Sales', $this->getTotalSales()),
            Stat::make('Today Sales', $this->sumTodaySales()),
            Stat::make('Agent Count', $this->countAgents()),
        ];
    }
}
