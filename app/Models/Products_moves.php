<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products_moves extends Model
{
    protected $fillable = [
        'name',
        'move_amount',
        'count',
        'date',
        'old_place_id',
        'new_place_id',
        'product_id',
        'creator_id',
        'currency_id'
    ];
}
