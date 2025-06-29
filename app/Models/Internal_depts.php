<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Internal_depts extends Model
{
    protected $table = 'internal_dept';
    protected $fillable = [
        'note',
        'total',
        'paid',
        'remaining',
        'start_date',
        'end_date',
        'type',
        'state',
        'invoice_id',
        'currency_id',
        'client_id',
        'creator_id',
        'business_id',
        'branch_id',
    ];

    public function payments()
    {
        return $this->hasMany(Internal_depts_paymentes::class,'internal_dept_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currencies::class);
    }
}
