<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorProductQty extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'product_id',
        'qty'
    ];
}
