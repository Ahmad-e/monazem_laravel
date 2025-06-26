<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashes extends Model
{
    protected $fillable = [
        'note',
        'Balance',
        'branch_id',
        'currency_id',
        'manager_id',
    ];
}
