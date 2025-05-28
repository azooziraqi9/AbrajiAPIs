<?php

namespace Modules\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Wallet\Database\Factories\WalletTransactionFactory;

class WalletTransaction extends Model
{
    use softDeletes;

    protected $fillable = ['admin_id', 'debt_id', 'type', 'amount', 'transaction_id'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'admin_id', 'admin_id');
    }
}
