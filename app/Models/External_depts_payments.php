<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class External_depts_payments extends Model
{
    protected $table = 'external_debts_payments';
    protected $fillable = [
        'note',
        'total',
        'date',
        'external_debt_id',
        'currency_id',
        'creator_id',
    ];
    public function External_dept(){
        return $this->belongsTo(External_depts::class, 'external_debt_id');
    }
}
