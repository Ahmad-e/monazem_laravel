<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rent_prepaid_revenues extends Model
{
    protected $fillable = [
        'amount',
        'book_value',
        'amount_in_base',
        'month_count',
        'name',
        'note',
        'start_date',
        'end_date',
        'account_id',
        'business_id',
        'branch_id',
        'creator_id',
        'currency_id',
    ];

    public function currency()
    {
        return $this->belongsTo(Currencies::class);
    }

}
