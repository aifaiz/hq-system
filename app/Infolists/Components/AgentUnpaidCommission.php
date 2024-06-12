<?php

namespace App\Infolists\Components;

use Filament\Infolists\Components\Entry;

class AgentUnpaidCommission extends Entry
{
    protected string $view = 'infolists.components.agent-unpaid-commission';

    public $amount;

    public function mount()
    {
        $this->calculateUnpaid();
    }

    public function calculateUnpaid()
    {
        $amount = \App\Models\Order::where('agent_id',$this->getRecord()->id)->where('pay_status', 'PAID')->where('agent_paid', 'NO')->sum('agent_comm');

        return 'RM '.number_format($amount, 2,'.',',');
    }
}
