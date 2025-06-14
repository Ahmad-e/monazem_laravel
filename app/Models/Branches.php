<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branches extends Model
{
    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'blocked_branch',
        'contact_info',
        'business_id'
    ];
}
