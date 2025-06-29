<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Internal_depts_paymentes extends Model
{
    protected $table = 'internal_dept_payments';

    protected $fillable = [
        'note',
        'total',
        'date',
        'internal_dept_id',
        'currency_id',
        'creator_id',
    ];
    public function currency()
    {
        return $this->belongsTo(Currencies::class,'internal_dept_id');
    }
}
