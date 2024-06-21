<?php

namespace App\Livewire\Components\Front\Agent;

use Livewire\Component;

class CartSidebar extends Component
{
    public $refcode;
    public $checkoutUrl;
    
    public function mount($refcode)
    {
        $this->refcode = $refcode;
        $this->checkoutUrl = route('agent.cart', ['refcode'=>$refcode]);
    }
    public function render()
    {
        return view('livewire.components.front.agent.cart-sidebar');
    }
}
