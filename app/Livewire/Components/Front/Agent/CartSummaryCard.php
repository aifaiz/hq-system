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

    public function mount($refcode)
    {
        $this->refcode = $refcode;
        $this->distributorID = AgentUser::where('refcode', $refcode)->value('distributor_id');
        // dd($this->distributorID);
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
        // Log::debug('cart', ['customer'=>$this->customer,'items'=>$this->items]);
        $orderService = new OrderService;
        $orderService->processOrder($this->items, $this->customer);
        return 'https://toyyibpay.com';
    }

    public function render()
    {
        return view('livewire.components.front.agent.cart-summary-card');
    }
}
