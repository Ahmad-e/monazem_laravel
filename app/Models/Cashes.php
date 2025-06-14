<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashes extends Model
{
    protected $fillable = [
        'Balance',
        'note',
        'branch_id',
        'manager_id',
        'description',
        'manager_id',
    ];
}
