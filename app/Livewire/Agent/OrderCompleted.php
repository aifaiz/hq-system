<?php

namespace App\Livewire\Agent;

use App\Models\AgentUser;
use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class OrderCompleted extends Component
{
    public ?Order $order;
    public ?Collection $items;
    public AgentUser $agent;
    public $shopUrl;

    public function mount($refcode, $billCode)
    {
        $this->order = Order::with(['items.product','agent'])->where('fpx_ref', $billCode)->first();

        if(!isset($this->order) && empty($this->order)):
            abort(404);
        endif;

        // dd($this->order);
        $this->shopUrl = route('agent.product', ['refcode'=>$refcode]);
        $this->items = $this->order->items;
        $this->agent = $this->order->agent;
        // dd($refcode, $billCode, $this->order, $this->agent, $this->items);
    }

    public function render()
    {
        return view('livewire.agent.order-completed');
    }
}
