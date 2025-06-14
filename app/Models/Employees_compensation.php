<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employees_compensation extends Model
{
    protected $fillable = [
        'value',
        'description',
        'pay_time',
        'employee_id',
        'currency_id',
        'creator_id',
    ];
}
