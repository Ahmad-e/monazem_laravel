<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    protected $fillable = [
        'name',
        'note',
        'value',
        'remaining',
        'date',
        'business_id',
        'branch_id',
        'creator_id',
        'currency_id',
    ];

    public function currency()
    {
        return $this->belongsTo(Currencies::class);
    }
    public function payments()
    {
        return $this->hasMany(Expenses_payments::class,'expenses_id');
    }
}
