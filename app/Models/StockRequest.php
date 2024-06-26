<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref',
        'distributor_id',
        'delivery_price',
        'sub_total',
        'total_amount',
        'pay_status',
        'deliver_status'
    ];

    public function items()
    {
        return $this->hasMany(StockRequestItem::class);
    }
}
