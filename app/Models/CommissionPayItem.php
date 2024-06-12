<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionPayItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_pay_id',
        'order_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
