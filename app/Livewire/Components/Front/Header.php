<?php

namespace App\Livewire\Components\Front;

use Livewire\Component;

class Header extends Component
{
    public $enableOrder;

    public function mount($enableorder = 'NO')
    {
        $this->enableOrder = $enableorder;
        
    }

    public function render()
    {
        return view('livewire.components.front.header');
    }
}
