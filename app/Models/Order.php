<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'distributor_id',
        'delivery_status',
        'pay_status',
        'pay_at',
        'discount_amount',
        'discount_type',
        'delivery_price',
        'sub_total',
        'grand_total',
        'customer_name',
        'customer_phone',
        'customer_email',
        'address',
        'fpx_ref',
        'agent_comm',
        'agent_paid'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function agent()
    {
        return $this->belongsTo(AgentUser::class);
    }

    public function distributor()
    {
        return $this->belongsTo(DistributorUser::class);
    }
}
