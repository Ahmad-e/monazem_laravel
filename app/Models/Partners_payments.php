<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partners_payments extends Model
{
    protected $fillable = [
        'value',
        'date',
        'partner_id',
        'currency_id',
        'creator_id'
    ];
    public function currency()
    {
        return $this->belongsTo(Currencies::class);
    }
}
