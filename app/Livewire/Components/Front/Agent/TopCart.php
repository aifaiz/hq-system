<?php

namespace App\Livewire\Components\Front\Agent;

use Livewire\Component;

class TopCart extends Component
{
    public $enableOrder;

    public function mount($enableorder = 'NO')
    {
        $this->enableOrder = $enableorder;
    }

    public function render()
    {
        return view('livewire.components.front.agent.top-cart');
    }
}
