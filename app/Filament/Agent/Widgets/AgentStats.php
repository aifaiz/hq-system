<?php

namespace App\Filament\Agent\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgentStats extends BaseWidget
{
    private function calculateComm($status = 'YES')
    {
        $agentID = auth()->id();
        $comm = Order::where('agent_id', $agentID)
            ->where('pay_status', 'PAID')
            ->where('agent_paid', $status)
            ->sum('agent_comm');
        return number_format($comm, 2,'.',',');
    }

    private function countOrders()
    {
        $agentID = auth()->id();
        return Order::where('agent_id', $agentID)
            ->where('pay_status', 'PAID')
            ->count('pay_status');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Unpaid Commission', $this->calculateComm('NO')),
            Stat::make('Paid Commission', $this->calculateComm('YES')),
            Stat::make('Paid Order Count', $this->countOrders()),
        ];
    }
}
