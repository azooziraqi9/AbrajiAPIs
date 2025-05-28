<?php

namespace Modules\Transaction\Interfaces;

use Illuminate\Http\Request;

interface TransactionServiceInterface
{
    public function createTransaction(Request $request);
    public function getTransactionById(int $id,Request $request);
    public function getTransactions(Request $request);
    public function updateTransaction(int $id, Request $request);
    public function deleteTransaction(int $id,Request $request);

}
