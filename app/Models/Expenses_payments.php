<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenses_payments extends Model
{
    protected $fillable = [
        'note',
        'value',
        'date',
        'expenses_id',
        'creator_id',
        'currency_id'
    ];
}
