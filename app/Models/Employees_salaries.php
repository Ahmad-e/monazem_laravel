<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employees_salaries extends Model
{
    protected $fillable = [
        'value',
        'description',
        'pay_time',
        'active',
        'frequency',
        'employee_id',
        'currency_id',
        'creator_id',
    ];

    public function payments()
    {
        return $this->hasMany(Employees_salaries_payments::class,'salary_id');
    }
    public function currency()
    {
        return $this->belongsTo(Currencies::class);
    }
}
