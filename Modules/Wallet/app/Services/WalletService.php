<?php

namespace Modules\Wallet\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Manager\Interfaces\IMangerService;
use Modules\Manager\Models\Manager;
use Modules\Wallet\Interfaces\WalletServiceInterface;
use Modules\Wallet\Models\Wallet;
use Modules\Wallet\Models\WalletTransaction;

class WalletService implements WalletServiceInterface
{

    protected $managerService;

    public function __construct(IMangerService $managerService)
    {
        $this->managerService = $managerService;
    }

    public function getAdminIdFromToken($token)
    {
        try {
            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $tokenParts = explode('.', $token);
            $payload = base64_decode($tokenParts[1]);
            $decoded = json_decode($payload);

            if (isset($decoded->sub)) {
                return $decoded->sub;
            } else {
                throw new \Exception('Admin ID not found in token');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getMyWalletBalance(Request $request)
    {
        $authorization = $request->header('Authorization');
        $admin_id = $this->getAdminIdFromToken($authorization);

        if (!is_numeric($admin_id)) {
            $admin_request = $this->managerService->GetManger($authorization);

            if ($admin_request->status != 200) {
                return response()->json([
                    $admin_request
                ], 401);
            }

            $admin_id = $admin_request->data[0]->id;
            $admin_name = $admin_request->data[0]->username;

            // Check if the admin is already in the database
            $admin = Manager::firstOrCreate(
                ['admin_id' => $admin_id],
                ['username' => $admin_name]
            );

            // Create a new wallet for the admin if not exists
            $wallet = Wallet::firstOrCreate(
                ['admin_id' => $admin_id],
                ['username' => $admin_name],
                ['balance' => 0]
            );
        } else {
            $admin = Manager::where('admin_id', $admin_id)->first();
            if (!$admin) {
                return response()->json([
                    'status' => 401,
                    'error' => 'Unauthorized'
                ], 401);
            }

            $wallet = Wallet::firstOrCreate(
                ['admin_id' => $admin_id],
                ['username' => $admin->username],
                ['balance' => 0.00]
            );
        }


        return response()->json([
            'status' => 200,
            'data' => [
                'id' => $admin_id,
                'username' => $admin->username,
                'balance' => $wallet->balance
            ]
        ], 200);
    }

    public function addMoneyToWallet(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
            ]);

            $authorization = $request->header('Authorization');
            $admin_id = $this->getAdminIdFromToken($authorization);

            if (!is_numeric($admin_id)) {
                $admin_request = $this->managerService->GetManger($authorization);

                if ($admin_request->status != 200) {
                    return response()->json([
                        'status' => 401,
                        'error' => 'Unauthorized'
                    ], 401);
                }

                $admin_id = $admin_request->data[0]->id;
                $admin_name = $admin_request->data[0]->username;

                Manager::firstOrCreate(
                    ['admin_id' => $admin_id],
                    ['username' => $admin_name]
                );

                $wallet = Wallet::firstOrCreate(
                    ['admin_id' => $admin_id],
                    ['username' => $admin_name],
                    ['balance' => 0]
                );
            } else {
                $wallet = Wallet::where('admin_id', $admin_id)->first();

                if (!$wallet) {
                    return response()->json([
                        'status' => 404,
                        'error' => 'Wallet not found'
                    ], 404);
                }
            }

            $amount = $request->input('amount');
            $wallet->balance += $amount;
            $wallet->save();

            WalletTransaction::create([
                'admin_id' => $admin_id,
                'type' => 'credit',
                'amount' => $amount
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'data' => [
                    'id' => $admin_id,
                    'username' => $wallet->username,
                    'balance' => $wallet->balance
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateWalletAmount(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'amount' => 'required|numeric|min:0', // Ensure amount is a positive number or zero
            ]);

            $authorization = $request->header('Authorization');
            $admin_id = $this->getAdminIdFromToken($authorization);

            if (!is_numeric($admin_id)) {
                $admin_request = $this->managerService->GetManger($authorization);

                if ($admin_request['status'] != 200) {
                    return response()->json([
                        'status' => 401,
                        'error' => 'Unauthorized'
                    ], 401);
                }

                $admin_id = $admin_request['data'][0]['id'];
                $admin_name = $admin_request['data'][0]['username'];

                Manager::firstOrCreate(
                    ['admin_id' => $admin_id],
                    ['username' => $admin_name]
                );

                $wallet = Wallet::firstOrCreate(
                    ['admin_id' => $admin_id],
                    ['username' => $admin_name, 'balance' => 0]
                );
            } else {
                $wallet = Wallet::where('admin_id', $admin_id)->first();

                if (!$wallet) {
                    return response()->json([
                        'status' => 404,
                        'error' => 'Wallet not found'
                    ], 404);
                }
            }

            $amount = $request->input('amount');
            $current_balance = $wallet->balance;

            // Update the wallet balance
            $wallet->balance = $amount;
            $wallet->save();

            // Determine the transaction type
            $transaction_type = $amount < $current_balance ? 'debit' : 'credit';

            // Create a wallet transaction
            WalletTransaction::create([
                'admin_id' => $admin_id,
                'type' => $transaction_type, // Corrected by wrapping the type in quotes
                'amount' => abs($current_balance - $amount) // Store the difference as the transaction amount
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'data' => [
                    'id' => $admin_id,
                    'balance' => $wallet->balance
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function resetWalletAmount(Request $request)
    {
        DB::beginTransaction();

        try {
            $authorization = $request->header('Authorization');
            $admin_id = $this->getAdminIdFromToken($authorization);

            if (!is_numeric($admin_id)) {
                $admin_request = $this->managerService->GetManger($authorization);

                if ($admin_request->status != 200) {
                    return response()->json([
                        'status' => 401,
                        'error' => 'Unauthorized'
                    ], 401);
                }

                $admin_id = $admin_request->data[0]->id;
                $admin_name = $admin_request->data[0]->username;

                Manager::firstOrCreate(
                    ['admin_id' => $admin_id],
                    ['username' => $admin_name]
                );

                $wallet = Wallet::firstOrCreate(
                    ['admin_id' => $admin_id],
                    ['balance' => 0]
                );
            } else {
                $wallet = Wallet::where('admin_id', $admin_id)->first();

                if (!$wallet) {
                    return response()->json([
                        'status' => 404,
                        'error' => 'Wallet not found'
                    ], 404);
                }
            }

            $prior_balance = $wallet->balance;

            $wallet->balance = 0;
            $wallet->save();

            WalletTransaction::create([
                'admin_id' => $admin_id,
                'type' => 'debit',
                'amount' => $prior_balance // Log the reset amount as 0
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'data' => [
                    'id' => $admin_id,
                    'balance' => $wallet->balance
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getWalletTransactions(Request $request)
    {
        try {
            $authorization = $request->header('Authorization');
            $admin_id = $this->getAdminIdFromToken($authorization);

            if (!is_numeric($admin_id)) {
                $admin_request = $this->managerService->GetManger($authorization);

                if ($admin_request->status != 200) {
                    return response()->json([
                        'status' => 401,
                        'error' => 'Unauthorized'
                    ], 401);
                }

                $admin_id = $admin_request->data[0]->id;
            }

            $query = WalletTransaction::where('admin_id', $admin_id);

            // Handle filter
            $filter = $request->input('filter');
            if ($filter === 'debts') {
                $query->whereNotNull('debt_id')->whereNull('transaction_id');
            } elseif ($filter === 'expenses') {
                $query->whereNull('debt_id')->whereNotNull('transaction_id');
            } elseif ($filter === 'other') {
                $query->whereNull('debt_id')->whereNull('transaction_id');
            }

            // Handle type
            $type = $request->input('type');
            if ($type) {
                $query->where('type', $type);
            }

            // Handle sorting
            $sortBy = $request->input('sortBy', 'created_at');
            $direction = $request->input('direction', 'desc');
            $query->orderBy($sortBy, $direction);

            // Handle pagination
            $page = $request->input('page', 1);
            $pageSize = $request->input('pageSize', 10);
            $transactions = $query->paginate($pageSize, ['*'], 'page', $page);

            return response()->json([
                'status' => 200,
                'data' => $transactions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }



}
