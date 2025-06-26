<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $fillable = [
        'text',
        'seen',
        'link',
        'user_id',
    ];
}
