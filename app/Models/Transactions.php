<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = [
        'description',
        'reference_number',
        'reference_number_type',
        'number',
        'business_id',
        'branch_id',
        'currency_id',
    ];
}
