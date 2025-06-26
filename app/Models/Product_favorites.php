<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_favorites extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
    ];
}
