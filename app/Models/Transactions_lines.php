<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions_lines extends Model
{
    protected $fillable = [
        'description',
        'debit_credit',
        'amount',
        'transaction_id',
        'account_id',
        'partner_id',
        'employee_id',
        'client_id',
        'currency_id',
    ];
}
