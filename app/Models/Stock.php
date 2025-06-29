<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stock';

    protected $fillable = [
        'name',
        'count',
        'date',
        'building_id',
        'place_id',
        'product_id',
        'products_price_id',
        'manager_id'
    ];
}
