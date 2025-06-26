<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency_branch extends Model
{
    protected $table = 'currency_branch'; // الاسم مفرد وليس بصيغة الجمع القياسية

    protected $fillable = [
        'is_base',
        'near_factor',
        'manual_exchange',
        'business_id',
        'branch_id',
        'creator_id',
        'currency_id',
    ];
}
