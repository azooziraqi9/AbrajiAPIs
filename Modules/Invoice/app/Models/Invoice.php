<?php

namespace Modules\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Invoice\Database\Factories\InvoiceFactory;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'tower',
        'invoice_number',
        'subscriber_name',
        'subs_type',
        'subs_price',
        'activation_date',
        'expiry_date',
        'payment_date',
        'payment_method',
        'payed_price',
        'remaining_price',
        'created_by',
        'status',
    ];

}
