<?php 

namespace App\Services;

use App\Models\DistributorProductQty;

class StockService
{
    public function deductStock($distributorID, $items)
    {
        foreach($items as $item):
            $stock = DistributorProductQty::where('product_id', $item->product_id)->where('distributor_id', $distributorID)->first();
            $available = $stock->qty;
            $newQty = $available - $item->qty;
            $stock->qty = $newQty;
            $stock->save();
        endforeach;
    }
}