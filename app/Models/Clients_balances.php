<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clients_balances extends Model
{
    protected $table = 'clients_balance';

    protected $fillable = [
        'opening',
        'current',
        'closing',
        'trial_balance_id',
        'client_id',
        'creator_id',
        'currency_id',
    ];

}
