<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Internal_depts_paymentes extends Model
{
    protected $fillable = [
        'note',
        'total',
        'date',
        'internal_dept_id',
        'currency_id',
        'creator_id',
    ];
}
