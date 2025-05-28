<?php

namespace Modules\Debts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Debts\Database\Factories\PartialPaymentFactory;

class PartialPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['debt_id', 'amount', 'paid_at'];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }
}
