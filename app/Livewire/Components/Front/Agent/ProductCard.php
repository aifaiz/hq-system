<?php

namespace App\Livewire\Components\Front\Agent;

use Livewire\Component;

class ProductCard extends Component
{
    public $productid;
    public $name;
    public $description;
    public $price;
    public $enableOrder;

    public function mount($productid, $name, $description, $price, $enableorder)
    {
        $this->productid = (int)$productid;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->enableOrder = $enableorder;
    }

    public function render()
    {
        return view('livewire.components.front.agent.product-card');
    }
}
