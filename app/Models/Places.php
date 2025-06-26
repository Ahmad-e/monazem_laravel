<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Places extends Model
{
    protected $fillable = [
        'name',
        'floor_number',
        'room_number',
        'shelves_alphabet',
        'blocked',
        'building_id',
        'creator_id'
    ];
}
