<?php

namespace App\Infolists\Components;

use Filament\Infolists\Components\Entry;

class AgentPaidCommission extends Entry
{
    protected string $view = 'infolists.components.agent-paid-commission';

    public function calculatePaid()
    {
        $amount = \App\Models\Order::where('agent_id',$this->getRecord()->id)->where('pay_status', 'PAID')->where('agent_paid', 'YES')->sum('agent_comm');

        return 'RM '.number_format($amount, 2,'.',',');
    }
}
