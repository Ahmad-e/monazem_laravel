<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxes_products extends Model
{
    protected $fillable = [
        'tax_id',
        'product_id',
        'branch_id',
        'creator_id'
    ];
}
