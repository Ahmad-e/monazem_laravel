<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    protected $fillable = [
        'text',
        'seen',
        'reText',
        'user_id',
        'admin_id'
    ];
}
