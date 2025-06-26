<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batches extends Model
{
    protected $fillable = [
        'active',
        'unit_cost',
        'expiration_date',
        'invoices_products_id',
        'products_prices_id',
        'user_id',
        'currency_id',
    ];
}
