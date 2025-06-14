<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currencies extends Model
{
    protected $fillable = [
        'code_en',
        'code_ar',
        'symbol',
        'name_en',
        'name_ar',
        'exchange_rate_to_dollar',
        'blocked_currency',
    ];
}
