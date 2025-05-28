<?php

namespace Modules\Transaction\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Transaction\Services\TransactionService;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        try {


            $transactions = $this->transactionService->getTransactions($request);

            return response()->json($transactions, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function store(Request $request)
    {

        try {
            return $this->transactionService->createTransaction($request);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id,Request $request)
    {
        try {
            $transaction = $this->transactionService->getTransactionById($id , $request);
            return response()->json($transaction, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update( $id,Request $request)
    {

        try {
            $transaction = $this->transactionService->updateTransaction($id, $request);
            return response()->json($transaction, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id ,Request $request)
    {
        try {
            $this->transactionService->deleteTransaction($id, $request);
            return response()->json(['message' => 'Transaction deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
