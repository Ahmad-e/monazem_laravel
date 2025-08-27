<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employees_salaries_advances_payments extends Model
{
    protected $fillable = [
        'value',
        'date',
        'salaries_advance_id'
    ];

    public function salaryAdvance()
    {
        return $this->belongsTo(Employees_salaries::class, 'salaries_advance_id');
    }
}
