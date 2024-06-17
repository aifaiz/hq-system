<?php

namespace App\Livewire\Agent;

use App\Http\Resources\Front\Agent\ProductResource;
use App\Models\AgentUser;
use App\Models\Product as ModelsProduct;
use Livewire\Component;

class Product extends Component
{
    public $refcode;
    public $products;
    public $agent;
    public $enableOrder = 'NO';

    public function mount($refcode)
    {
        $this->refcode = $refcode;
        $this->agent = AgentUser::where('refcode', $refcode)->where('status', 1)->first();

        if(!$this->agent):
            abort(404);
        endif;

        $this->enableOrder = $this->agent->enable_order;
        $distributorID = $this->agent->distributor_id;
        $products = ModelsProduct::with('images')->select('products.*', 'distributor_product_qties.qty')
            ->where('status', 1)
            ->leftJoin('distributor_product_qties', function($join) use($distributorID){
                $join->on('products.id', 'distributor_product_qties.product_id')
                    ->where('distributor_id', $distributorID);
            })
            ->get();
        // dd($products->toArray());
        
        $this->products = $products; //ProductResource::collection($products);
        // dd($this->products);
    }

    public function render()
    {
        
        return view('livewire.agent.product')->layoutData([
            'enableOrder'=>$this->enableOrder
        ]);
    }
}
