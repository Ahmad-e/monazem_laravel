<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products_units extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'group_ar',
        'group_en',
        'conversion_factor',
        'business_id',
        'branch_id',
    ];
}
