<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'address',
        'note',
        'blocked',
        'type',
        'business_id',
        'branch_id',
        'creator'
    ];
}
