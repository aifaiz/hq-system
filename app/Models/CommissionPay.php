<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionPay extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref',
        'agent_id',
        'amount',
        'pay_at'
    ];

    public function agent()
    {
        return $this->belongsTo(AgentUser::class);
    }

    public function items()
    {
        return $this->hasMany(
            CommissionPayItem::class,
            'commission_pay_id',
            'id'
        );
    }
}
