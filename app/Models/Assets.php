<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assets extends Model
{
    protected $fillable = [
        'name',
        'note',
        'date',
        'state',
        'amount',
        'count',
        'book_value',
        'business_id',
        'branch_id',
        'creator_id',
        'currency_id'
    ];

    public function currency()
    {
        return $this->belongsTo(Currencies::class);
    }
}
