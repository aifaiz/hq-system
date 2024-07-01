<?php

namespace App\Livewire\Components\Front\Agent;

use Livewire\Component;

class ProductCard extends Component
{
    public $productid;
    public $slug;
    public $name;
    public $description;
    public $price;
    public $enableOrder;
    public $image;
    public $max;
    public $refcode;
    public $url;

    public function mount($productid, $slug, $image, $name, $description, $price, $enableorder, $max, $refcode)
    {
        if(empty($description)):
            $this->description = "&nbsp;";
        endif;

        $this->productid = (int)$productid;
        $this->slug = $slug;
        $this->name = $name;
        $this->image = $image;
        $this->price = $price;
        $this->enableOrder = $enableorder;
        $this->max = $max;
        $this->refcode = $refcode;
        if($slug):
            $this->url = route('agent.product.view', ['refcode'=>$refcode,'slug'=>$slug]);
        else:
            $this->url = '#';
        endif;
    }

    public function render()
    {
        return view('livewire.components.front.agent.product-card');
    }
}
