<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partners_withdrawals extends Model
{
    protected $fillable = [
        'value',
        'date',
        'partner_id',
        'currency_id',
    ];
}
