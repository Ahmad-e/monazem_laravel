<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employees_salaries_advance extends Model
{
    protected $fillable = [
        'value',
        'paid',
        'description',
        'is_debts',
        'pay_time',
        'employee_id',
        'currency_id',
        'creator_id',
    ];

    public function AdvancePayments()
    {
        return $this->hasMany(Employees_salaries_advances_payments::class,'salaries_advance_id');
    }
    public function currency()
    {
        return $this->belongsTo(Currencies::class);
    }

}
