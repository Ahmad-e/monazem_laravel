<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products_prices extends Model
{
    protected $fillable = [
        'price',
        'note',
        'categories',
        'partner_ar',
        'partner_en',
        'product_id',
        'creator_id',
        'currency_id',
    ];
    public function Product(){
        return $this->belongsTo(Products::class, 'product_id');
    }
}
