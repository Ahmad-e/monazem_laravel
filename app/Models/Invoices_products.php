<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoices_products extends Model
{
    protected $fillable = [
        'products_count',
        'total_product_price',
        'tax_amount',
        'product_id',
        'products_price_id',
        'place_id',
        'invoice_id',
        'currency_id',
    ];
}
