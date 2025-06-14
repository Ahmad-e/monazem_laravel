<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Powers_users extends Model
{
    protected $fillable = [
        'user_id',
        'power_id',
    ];
}
