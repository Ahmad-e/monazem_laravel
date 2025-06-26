<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trial_balances extends Model
{
    protected $table = 'trial_balance'; // اسم الجدول مفرد

    protected $fillable = [
        'opening',
        'current',
        'closing',
        'account_id',
        'creator_id',
    ];
}
