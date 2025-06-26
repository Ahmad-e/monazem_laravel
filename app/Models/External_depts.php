<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class External_depts extends Model
{
    protected $table = 'external_debts';
    protected $fillable = [
        'note',
        'total',
        'paid',
        'remaining',
        'start_date',
        'end_date',
        'type',
        'state',
        'business_id',
        'user_id',
        'employee_id',
        'currency_id',
        'creator_id',
    ];
    public function Payment()
    {
        return $this->hasMany(External_depts_payments::class,'external_debt_id');
    }
}
