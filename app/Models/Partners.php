<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partners extends Model
{
    protected $fillable = [
        'total_capital',
        'ownership_percentage',
        'role',
        'note',
        'join_date',
        'business_id',
        'user_id',
        'currency_id',
    ];
    public function currency()
    {
        return $this->belongsTo(Currencies::class);
    }
}
