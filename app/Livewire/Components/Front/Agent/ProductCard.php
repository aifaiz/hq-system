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
    public $image;
    public $max;

    public function mount($productid, $image, $name, $description, $price, $enableorder, $max)
    {
        $this->productid = (int)$productid;
        $this->name = $name;
        $this->image = $image;
        $this->description = $description;
        $this->price = $price;
        $this->enableOrder = $enableorder;
        $this->max = $max;
    }

    public function render()
    {
        return view('livewire.components.front.agent.product-card');
    }
}
