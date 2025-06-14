<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Powers extends Model
{
    protected $fillable = [
        'en_name',
        'ar_name',
        'level',
        'blocked',
    ];
}
