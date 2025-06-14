<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenues_payments extends Model
{
    protected $fillable = [
        'note',
        'value',
        'date',
        'revenues_id',
        'creator_id',
        'currency_id'
    ];
}
