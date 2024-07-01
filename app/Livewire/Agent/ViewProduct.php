<?php

namespace App\Livewire\Agent;

use App\Models\Product;
use Livewire\Component;

class ViewProduct extends Component
{
    public $refcode;
    public $slug;
    public $product;

    public function mount($refcode,$slug)
    {
        $this->refcode = $refcode;
        $this->slug = $slug;
        $product = Product::where('slug', $slug)->first();
        if(!$product):
            abort(404);
        endif;

        $this->product = $product;
    }
    public function render()
    {
        return view('livewire.agent.view-product');
    }
}
