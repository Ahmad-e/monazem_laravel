<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    protected $fillable = [
        'name',
        'nature',
        'statement',
        'level',
        'is_sub',
        'code',
        'partner_id',
        'business_id',
        'branch_id',
    ];
}
