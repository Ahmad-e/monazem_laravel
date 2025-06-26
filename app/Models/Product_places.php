<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_places extends Model
{
    protected $fillable = [
        'count',
        'place_id',
        'batches_id',
        'product_id',
        'unit_id'
    ];
}
