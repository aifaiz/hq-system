<?php 

namespace App\Services;

use App\Models\DistributorProductQty;
use App\Models\StockRequest;

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

    public function generateRequestStockRef()
    {
        $rand = rand(99,9999) . uniqid();
        $ref = hash('crc32', $rand);
        return $this->validateStockRef($ref);
    }

    public function validateStockRef($ref)
    {
        $check = StockRequest::where('ref', $ref)->value('ref');
        if($check):
            $rand = rand(99,99999) . uniqid();
            $nref = hash('crc32', $rand);
            return $this->validateStockRef($nref);
        else:
            return $ref;
        endif;
    }
}