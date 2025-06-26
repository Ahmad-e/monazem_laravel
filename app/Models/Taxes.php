<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxes extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'level',
        'blocked',
        'rate',
        'business_id',
        'branch_id',
        'creator_id'
    ];
}
