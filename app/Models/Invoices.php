<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    protected $fillable = [
        'number',
        'type',
        'payment_status',
        'note',
        'unDiscounted_amount',
        'discounted_amount',
        'tax_amount',
        'shipping_cost',
        'refunded_amount',
        'affect_refund',
        'paid_amount',
        'amount_in_base',
        'shipping_cost_in_base',
        'blocked',
        'date',
        'branch_id',
        'original_invoice_id',
        'partner_id',
        'client_id',
        'creator_id',
        'currency_id',
        'business_id'
    ];
}
