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
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function manager()
    {
        return $this->belongsTo(User::class);
    }
}
