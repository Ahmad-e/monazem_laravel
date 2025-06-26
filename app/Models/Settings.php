<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'setting';

    protected $fillable = [
        'key',
        'value',
        'business_id',
        'branch_id',
    ];
}
