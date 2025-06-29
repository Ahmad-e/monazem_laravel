<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $table = 'transaction';

    protected $fillable = [
        'description',
        'reference_number',
        'reference_number_type',
        'number',
        'business_id',
        'branch_id',
        'currency_id',
    ];
    public function lines()
    {
        return $this->hasMany(Transactions_lines::class,'transaction_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currencies::class);
    }

}
