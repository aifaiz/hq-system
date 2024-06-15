<?php

namespace App\Livewire\Agent;

use App\Helpers\SettingsHelper;
use App\Models\AgentUser;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CartSummary extends Component
{
    public $refcode;
    public $agent;
    public $enableOrder;
    public $items = [];
    private $settings = [];
    public $deliveryPrice = 10;

    public function mount($refcode)
    {
        $this->refcode = $refcode;
        $this->agent = AgentUser::where('refcode', $refcode)->where('status', 1)->first();

        if(!$this->agent):
            abort(404);
        endif;

        $this->settings = SettingsHelper::getDistributorSettings($this->agent->distributor_id);
        $this->deliveryPrice = $this->settings['DELIVERY_PRICE'];

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
