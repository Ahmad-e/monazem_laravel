<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employees_holidays extends Model
{
    protected $fillable = [
        'value',
        'description',
        'pay_time',
        'type',
        'employee_id',
        'currency_id',
        'creator_id'
    ];
}
