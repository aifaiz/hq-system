<?php

namespace App\Livewire\Components\Front\Agent;

use App\Models\AgentUser;
use App\Models\DistributorProductQty;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CartSummaryCard extends Component
{
    public $refcode;
    public $distributorID;
    public array $items = [];
    public array $customer = [];
    public $agentID;

    public function mount($refcode)
    {
        $this->refcode = $refcode;
        $agent = AgentUser::where('refcode', $refcode)->first();
        $this->agentID = $agent->id;
        $this->distributorID = $agent->distributor_id;
        // dd($this->distributorID, $this->agentID);
        // Log::debug('cartsum', ['agent'=>$this->agentID,'distr'=>$this->distributorID]);
    }

    // public function getItems()
    // {
    //     // Log::debug('latest items: ', $this->items);
    //     return $this->items;
    // }

    public function setItems($items, $customer)
    {
        // Log::debug('setting items', ['item'=>$items]);
        $this->items = $items;
        $this->customer = $customer;
        $prodIDs = [];
        $canOrder = [];
        $outOfStockProducts = [];
        foreach($this->items as $k=>$i):
            $prodIDs[] = $i['id'];
            $qty = $i['qty'];
            $stock = DistributorProductQty::where('distributor_id', $this->distributorID)->where('product_id', $i['id'])->value('qty');
            if($stock < $qty):
                $this->items[$k]['max'] = (string)$stock;
                $canOrder[] = false;
                $outOfStockProducts[] = $i;
            endif;
        endforeach;

        if(in_array(false, $canOrder)):
            return [
                'status'=>false,
                'outOfStock'=>$outOfStockProducts,
                'items'=>$this->items,
                'distributor_id'=>$this->distributorID,
                'refcode'=>$this->refcode
            ];
        endif;

        return [
            'status'=>true
        ];
    }

    public function processCart()
    {
        // Log::debug('cart', ['aid'=>$this->agentID,'customer'=>$this->customer,'items'=>$this->items]);
        // return false;
        $orderService = new OrderService;
        $url = $orderService->processOrder(
            $this->agentID, 
            $this->distributorID, 
            $this->items, 
            $this->customer
        );
        return $url;
    }

    public function render()
    {
        return view('livewire.components.front.agent.cart-summary-card');
    }
}
