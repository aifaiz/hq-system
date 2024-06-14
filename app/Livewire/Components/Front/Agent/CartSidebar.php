<?php

namespace App\Livewire\Components\Front\Agent;

use Livewire\Component;

class CartSidebar extends Component
{
    public $refcode;
    
    public function mount($refcode)
    {
        $this->refcode = $refcode;
    }
    public function render()
    {
        return view('livewire.components.front.agent.cart-sidebar');
    }
}
