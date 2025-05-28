<?php

namespace Modules\Transaction\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Modules\Manager\Interfaces\IMangerService;
use Modules\Transaction\Interfaces\TransactionServiceInterface;
use Modules\Transaction\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Interfaces\WalletServiceInterface;
use Modules\Wallet\Models\Wallet;
use Modules\Wallet\Models\WalletTransaction;
use Nette\Utils\Random;


class TransactionService implements TransactionServiceInterface
{
    protected $walletModel;

    protected $walletService;

    protected $managerService;

    public function __construct(Wallet $walletModel , WalletServiceInterface $walletService ,IMangerService $managerService)
    {
        $this->walletModel = $walletModel;
        $this->walletService = $walletService;
        $this->managerService = $managerService;
    }

    protected function getValidatedRequestInputs(Request $request)
    {
        $columns = $request->input('columns', ['*']);
        if (empty($columns)) {
            $columns = ['*'];
        }

        return [
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('pageSize', 10),
            'sortBy' => $request->input('sortBy', 'debt_timestamp'),
            'direction' => $request->input('direction', 'desc'),
            'search' => $request->input('search', null),
            'columns' => $columns,
            'count' => $request->input('count', null),
        ];
    }

    protected function applyRequestFilters(Builder $query, array $validColumns, array $inputs)
    {
        if (!empty($inputs['search'])) {
            $query->where(function ($q) use ($inputs, $validColumns) {
                foreach ($validColumns as $column) {
                    $q->orWhere($column, 'like', "%{$inputs['search']}%");
                }
            });
        }


        if (!empty($inputs['sortBy']) && !empty($inputs['direction'])) {
            $query->orderBy($inputs['sortBy'], $inputs['direction']);
        } else {
            $query->orderBy('date', 'desc');
        }

        if (!empty($inputs['count'])) {
            $query->take($inputs['count']);
        }

        return $query->paginate($inputs['pageSize'], $inputs['columns'], 'page', $inputs['page']);
    }


    public function createTransaction(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate request data
            $request->validate([
                'created_by' => 'required',
                'cost' => 'required',
                'description' => 'required',
                'date' => 'required',
                'type' => 'required',
                'category' => 'required',
            ]);

            // Extract admin_id from JWT token
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['error' => 'Admin ID not found in token'], 401);
            }
            $MyWallet = $this->walletService->getMyWalletBalance($request);

            // Decode the JSON response
            $walletData = $MyWallet->getData(true);

            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['error' => 'Admin ID not found in token'], 401);
            }

            $Wallet = Wallet::where('admin_id', $admin_id)->first();
            if (!$Wallet) {
                throw new \Exception('Wallet not found for admin.');
            }

            //add in request admin_id
            $request->merge(['admin_id' => $admin_id]);


            // Generate a random transaction ID
            $transaction_id =  Random::generate(6);

            // Create a new transaction
            $transactionData = $request->only(['created_by', 'cost', 'description', 'date', 'type', 'category', 'admin_id']);
            $transactionData['admin_id'] = $admin_id;
            $transactionData['transaction_id'] = $transaction_id;
            $transaction = Transaction::create($transactionData);

            // Find the wallet for the admin
            $wallet = $this->walletModel->where('admin_id', $admin_id)->first();
            if (!$wallet) {
                throw new \Exception('Wallet not found for admin.');
            }

            // Check if wallet has enough balance for 'out' transactions
            if ($transaction->type === 'out' && $wallet->balance < $transaction->cost) {
                throw new \Exception('Insufficient wallet balance.');
            }

            // Adjust the wallet balance based on the transaction type
            $TypeWallet="";
            if ($transaction->type === 'in') {
                $wallet->balance += $transaction->cost;
                $TypeWallet='credit';
            } elseif ($transaction->type === 'out') {
                $wallet->balance -= $transaction->cost;
                $TypeWallet='debit';
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $wallet->save();

            // Create a wallet transaction entry
            WalletTransaction::create([
                'transaction_id' => $transaction->id,
                'admin_id' => $admin_id,
                'type' => $TypeWallet,
                'amount' => $transaction->cost,
            ]);

            DB::commit();

            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getTransactionById(int $id,Request $request)
    {
        try {
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }
            $MyWallet = $this->walletService->getMyWalletBalance($request);

            // Decode the JSON response
            $walletData = $MyWallet->getData(true);

            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $transaction = Transaction::where('admin_id',$admin_id)->findOrFail($id);

            return $transaction;

        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function getTransactions(Request $request)
    {
        try {
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }
            $MyWallet= $this->walletService->getMyWalletBalance($request);

            $walletData = $MyWallet->getData(true);


            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }


            // Validate and process the request inputs
            $inputs = $this->getValidatedRequestInputs($request);

            // Define the valid columns for searching and filtering
            $validColumns = Schema::getColumnListing('transactions');

            // Build the query
            $query = Transaction::query()->where('admin_id',$admin_id);

            // Apply filters and pagination using the helper method
            $paginatedResult = $this->applyRequestFilters($query, $validColumns, $inputs);
            return response()->json($paginatedResult, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }




    public function updateTransaction(int $id, Request $request)
    {
        DB::beginTransaction();

        try {
            // Check for Authorization token
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }

            // Get wallet data using the authorization token
            $MyWallet = $this->walletService->getMyWalletBalance($request);
            $walletData = $MyWallet->getData(true);

            // Check if the wallet data contains the admin ID
            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Fetch the transaction by ID and ensure it belongs to the admin
            $transaction = Transaction::where('id', $id)->where('admin_id', $admin_id)->firstOrFail();

            // Fetch the wallet associated with the admin_id
            $wallet = $this->walletModel->where('admin_id', $admin_id)->first();
            if (!$wallet) {
                throw new \Exception('Wallet not found for user.');
            }

            // Calculate the difference between the old and new amounts
            $oldAmount = $transaction->cost;
            $newAmount = $request->input('cost');
            $amountDifference = $newAmount - $oldAmount;

            // Adjust the wallet balance based on the transaction type
            $WalletType="";
            if ($transaction->type === 'in') {
                $wallet->balance += $amountDifference;
                $WalletType='credit';
            } elseif ($transaction->type === 'out') {
                $wallet->balance -= $amountDifference;
                $WalletType='debit';
            } else {
                throw new \Exception('Invalid transaction type.');
            }

            $wallet->save();

            // Update or create the WalletTransaction entry
            $walletTransaction = WalletTransaction::where('transaction_id', $transaction->id)->first();
            if ($walletTransaction) {
                // Update the existing WalletTransaction
                $walletTransaction->update([
                    'admin_id' => $admin_id,
                    'amount' => $newAmount,
                    'type' => $WalletType,
                    'description' => $request->input('description'),
                    'date' => $request->input('date'),
                ]);
            } else {
                // Create a new WalletTransaction if not found
                WalletTransaction::create([
                    'transaction_id' => $transaction->id,
                    'admin_id' => $admin_id,
                    'amount' => $newAmount,
                    'type' => $WalletType,
                    'description' => $request->input('description'),
                    'date' => $request->input('date'),
                ]);
            }

            // Update the transaction with the new data
            $transaction->update($request->only(['cost', 'description', 'date', 'type', 'category']));

            DB::commit();

            return $transaction->toArray();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function deleteTransaction(int $id, Request $request)
    {
        DB::beginTransaction();

        try {
            // Check for Authorization token
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }

            // Get wallet data using the authorization token
            $MyWallet = $this->walletService->getMyWalletBalance($request);
            $walletData = $MyWallet->getData(true);

            // Check if the wallet data contains the admin ID
            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Fetch the transaction by ID and ensure it belongs to the admin
            $transaction = Transaction::where('id', $id)->where('admin_id', $admin_id)->firstOrFail();

            // Fetch the wallet associated with the admin_id
            $wallet = $this->walletModel->where('admin_id', $admin_id)->first();
            if (!$wallet) {
                throw new \Exception('Wallet not found for user.');
            }

            // Adjust the wallet balance based on the transaction type
            if ($transaction->type === 'in') {
                $wallet->balance -= $transaction->cost;
            } elseif ($transaction->type === 'out') {
                $wallet->balance += $transaction->cost;
            } else {
                throw new \Exception('Invalid transaction type.');
            }

            $wallet->save();

            // Soft delete the wallet transaction entry
            $walletTransaction = WalletTransaction::where('transaction_id', $transaction->id)->first();
            if ($walletTransaction) {
                $walletTransaction->delete();
            }

            // Soft delete the transaction
            $transaction->delete();

            DB::commit();

            return response()->json(['message' => 'Transaction deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function restoreTransaction(int $id,Request $request)
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::withTrashed()->findOrFail($id);
            $wallet = $this->walletModel->where('admin_id', $transaction->admin_id)->first();

            if (!$wallet) {
                throw new \Exception('Wallet not found for user.');
            }

            // Adjust the wallet balance based on the transaction type
            if ($transaction->type === 'in') {
                $wallet->balance += $transaction->cost;
            } elseif ($transaction->type === 'out') {
                $wallet->balance -= $transaction->cost;
            } else {
                throw new \Exception('Invalid transaction type.');
            }

            $wallet->save();

            // Restore the wallet transaction entry
            $walletTransaction = WalletTransaction::withTrashed()->where('transaction_id', $transaction->id)->first();
            if ($walletTransaction) {
                $walletTransaction->restore();
            }

            // Restore the transaction
            $transaction->restore();

            DB::commit();

            return ['message' => 'Transaction restored successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getTransactionWidgets(Request $request)
    {
        try {
            // Check for Authorization token
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }

            // Get wallet data using the authorization token
            $MyWallet = $this->walletService->getMyWalletBalance($request);
            $walletData = $MyWallet->getData(true);

            // Check if the wallet data contains the admin ID
            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $categories = ['maintenance', 'expense', 'general'];
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $widgetData = [];
            foreach ($categories as $category) {
                $totalTransactions = Transaction::where('category', $category)
                    ->where('admin_id', $admin_id)
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->count();

                $totalAmount = Transaction::where('category', $category)
                    ->where('admin_id', $admin_id)
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->sum('cost');

                $widgetData[$category] = [
                    'total_transactions' => $totalTransactions,
                    'total_amount' => $totalAmount,
                ];
            }

            return response()->json($widgetData, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
