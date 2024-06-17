<?php

namespace App\Http\Resources\Front\Agent;

use App\Models\DistributorProductQty;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $qty = 0;
        if(!empty($this->qty)) $qty = $this->qty;
        
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'description'=>$this->description,
            'price'=>$this->price,
            'image'=>$this->cover_image,
            'max'=>$qty
        ];
    }
}
