<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products_codes extends Model
{
    protected $fillable = [
        'value',
        'date',
        'product_id',
        'creator_id',
    ];
    public function Product(){
        return $this->belongsTo(Products::class, 'product_id');
    }
}
