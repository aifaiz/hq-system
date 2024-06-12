<?php

namespace App\Filament\Agent\Widgets;

use App\Models\Order;
use Filament\Widgets\Widget;

class AgentCommission extends Widget
{
    protected static string $view = 'filament.agent.widgets.agent-commission';

    public $totalComm = 0;

    public function calculateComm()
    {
        $agentID = auth()->id();
        $comm = Order::where('agent_id', $agentID)->where('pay_status', 'PAID')->sum('agent_comm');
        $this->totalComm = number_format($comm, 2,'.',',');
    }

    public function mount()
    {
        $this->calculateComm();
    }
}
