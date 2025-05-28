<?php

namespace Modules\Transaction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'transaction_id',
        "user_id",
        'created_by',
        'cost',
        'description',
        'date',
        'type',
        'category',
        'admin_id'
    ];

}
