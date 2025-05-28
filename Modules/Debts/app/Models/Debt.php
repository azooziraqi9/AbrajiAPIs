<?php

namespace Modules\Debts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Debts\Database\Factories\DebtFactory;

class Debt extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'username',
        'debt_timestamp',
        'pay',
        'paid_at',
        'admin_id'
    ];

    public function partialPayments()
    {
        return $this->hasMany(PartialPayment::class);
    }

    public function getRemainingAmountAttribute()
    {
        $totalPaid = $this->partialPayments()->sum('amount');
        return $this->amount - $totalPaid;
    }

}
