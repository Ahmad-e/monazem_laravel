<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'name',
        'description',
        'code',
        'blocked_product',
        'img_url',
        'categories',
        'business_id',
        'branch_id',
        'type_id',
        'unit_id',
        'creator_id'
    ];

    public function prices()
    {
        return $this->hasMany(Products_prices::class,'product_id');
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoices::class, 'invoices_products', 'product_id', 'invoice_id')
            ->withPivot([
                'products_count',
                'total_product_price',
                'tax_amount',
                'products_price_id',
                'place_id',
                'currency_id'
            ])
            ->withTimestamps();
    }
}
