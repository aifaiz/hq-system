<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_request_id',
        'product_id',
        'qty'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id','id');
    }
}
