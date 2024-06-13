<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'skey',
        'sval',
    ];
}
