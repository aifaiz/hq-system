<?php

namespace App\Livewire\Agent;

use App\Models\AgentUser;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CartSummary extends Component
{
    public $refcode;
    public $agent;
    public $enableOrder;
    public $items = [];

    public function mount($refcode)
    {
        $this->refcode = $refcode;
        $this->agent = AgentUser::where('refcode', $refcode)->where('status', 1)->first();

        if(!$this->agent):
            abort(404);
        endif;

        $this->enableOrder = $this->agent->enable_order;
        
    }

    public function setItem($items)
    {
        // Log::debug('setting items', ['item'=>$items]);
        $this->items = $items;
        return true;
    }

    public function render()
    {
        return view('livewire.agent.cart-summary')->layoutData([
            'enableOrder'=>$this->enableOrder
        ]);
    }
}
