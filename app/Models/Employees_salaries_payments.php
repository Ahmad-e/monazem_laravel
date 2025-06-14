<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employees_salaries_payments extends Model
{
    protected $fillable = [
        'value',
        'allowances',
        'deductions',
        'description',
        'date',
        'work_from',
        'work_to',
        'salary_id',
        'currency_id',
        'creator_id',
    ];

    public function salary()
    {
        return $this->belongsTo(Employees_salaries::class, 'salary_id');
    }
}
