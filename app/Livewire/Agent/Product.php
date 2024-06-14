<?php

namespace App\Livewire\Agent;

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
        $this->products = ModelsProduct::where('status', 1)->get();
    }

    public function render()
    {
        return view('livewire.agent.product')->layoutData([
            'enableOrder'=>$this->enableOrder
        ]);
    }
}
