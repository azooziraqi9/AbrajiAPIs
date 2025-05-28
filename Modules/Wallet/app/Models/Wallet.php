<?php

namespace Modules\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Wallet\Database\Factories\WalletFactory;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id', 'balance', 'username'];

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'admin_id', 'admin_id');
    }

    public function adjustBalance($amount, $type, $debtId = null)
    {
        if ($type === 'debit') {
            $this->balance -= $amount;
        } elseif ($type === 'credit') {
            $this->balance += $amount;
        }
        $this->save();

        // Record the transaction
        WalletTransaction::create([
            'admin_id' => $this->admin_id,
            'debt_id' => $debtId,
            'type' => $type,
            'amount' => $amount,
        ]);
    }
}
