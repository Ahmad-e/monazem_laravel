<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products_types extends Model
{
    protected $fillable = [
        'name',
        'business_id',
        'branch_id',
    ];
}
