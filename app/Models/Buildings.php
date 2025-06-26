<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buildings extends Model
{
    protected $fillable = [
        'name',
        'type',
        'latitude',
        'longitude',
        'blocked',
        'branch_id',
        'creator_id'
    ];
}
