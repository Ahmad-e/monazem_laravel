<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'position',
        'image_url',
        'hire_date',
        'termination_date',
        'blocked_employee',
        'note',
        'business_id',
        'branch_id',
        'creator_id',
    ];
}
