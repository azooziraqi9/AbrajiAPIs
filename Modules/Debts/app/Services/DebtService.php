<?php

namespace Modules\Debts\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Debts\Interfaces\DebtServiceInterface;
use Modules\Debts\Models\Debt;
use Illuminate\Database\Eloquent\Builder;
use Modules\Debts\Models\PartialPayment;
use Modules\Wallet\Models\Wallet;
use Modules\Wallet\Models\WalletTransaction;
use Modules\Wallet\Services\WalletService;

class DebtService implements DebtServiceInterface
{

    protected $servicewallet;
    public function __construct(WalletService $walletService)
    {
        $this->servicewallet = $walletService;
    }

    protected $validColumns = ['id', 'amount', 'debt_timestamp', 'description', 'username'];

    public function getAll(Request $request)
    {
        try {
            $inputs = $this->getValidatedRequestInputs($request);

            //get token from request
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }
            $MyWallet= $this->servicewallet->getMyWalletBalance($request);

            $walletData = $MyWallet->getData(true);


            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $query = Debt::query()->where('admin_id', $admin_id);

            $debts = $this->applyRequestFilters($query, $this->validColumns, $inputs);

            return response()->json($debts, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
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
            'pay' => $request->input('pay', null),
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

        if (!is_null($inputs['pay'])) {
            $query->where('pay', $inputs['pay']);
        }

        if (!empty($inputs['sortBy']) && !empty($inputs['direction'])) {
            $query->orderBy($inputs['sortBy'], $inputs['direction']);
        } else {
            $query->orderBy('debt_timestamp', 'desc');
        }

        if (!empty($inputs['count'])) {
            $query->take($inputs['count']);
        }

        return $query->paginate($inputs['pageSize'], $inputs['columns'], 'page', $inputs['page']);
    }
    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string',
                'user_id' => 'required|numeric',
                'debt_timestamp' => 'required|date',
                'pay' => 'required|boolean',
                'username' => 'required|string',

            ]);

            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }


            $MyWallet= $this->servicewallet->getMyWalletBalance($request);

            $walletData = $MyWallet->getData(true);

            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $wallet=Wallet::where('admin_id',$admin_id)->first();

            //add filed admin_id in request


            // Check if the wallet has sufficient balance
            if ($wallet->balance < $request->input('amount')) {
                return response()->json(['message' => 'Insufficient balance in admin wallet'], 400);
            }

            // Deduct the amount from the admin's wallet
            $wallet->balance -= $request->input('amount');
            $wallet->save();

            // Insert the transaction into the WalletTransaction model
            $walletTransaction = WalletTransaction::create([
                'admin_id' => $wallet->admin_id,
                'debt_id' => null, // Temporarily set to null
                'type' => 'debit', // or 'credit' based on your logic
                'amount' => $request->input('amount')
            ]);

            $request->merge(['admin_id' => $admin_id]);

            $debt = Debt::create($request->all());

            // Update the WalletTransaction with the correct debt_id
            $walletTransaction->debt_id = $debt->id;
            $walletTransaction->save();

            DB::commit();

            // Return the created debt with a 201 status code
            return response()->json(['message' => 'Debt created successfully', 'debt' => $debt], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getByUserId($id, Request $request)
    {
        try {
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }

            $inputs = $this->getValidatedRequestInputs($request);

            $MyWallet= $this->servicewallet->getMyWalletBalance($request);

            $walletData = $MyWallet->getData(true);

            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $query = Debt::where('user_id', $id)->where('admin_id',$admin_id);

            $debts = $this->applyRequestFilters($query, $this->validColumns, $inputs);

            return response()->json($debts, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function payDebt($id)
    {

        DB::beginTransaction();
        try {

            $debt = Debt::find($id);

            if (!$debt) {
                return response()->json(['message' => 'Debt not found'], 404);
            }

            $partialPayments = PartialPayment::where('debt_id', $id)->get();
            if ($partialPayments->isNotEmpty()) {
                return response()->json(['message' => 'Debt should be paid using the partial payments'], 400);
            }

            if ($debt->pay) {
                return response()->json(['message' => 'Debt has already been paid'], 400);
            }


            $wallet = Wallet::where('admin_id', $debt->admin_id)->first();
            if (!$wallet) {
                return response()->json(['message' => 'Admin wallet not found'], 404);
            }

            $debt->pay = true;
            $debt->paid_at = now();
            $debt->save();

            $wallet->balance += $debt->amount;
            $wallet->save();

            WalletTransaction::create([
                'admin_id' => $debt->admin_id,
                'debt_id' => $debt->id,
                'type' => 'credit',
                'amount' => $debt->amount
            ]);

            DB::commit();

            return response()->json(['message' => 'Debt paid successfully', 'debt' => $debt], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function filter(Request $request)
    {
        try {
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }

            $month = $request->input('month');
            $year = $request->input('year');
            $pay = $request->input('pay');
            $userId = $request->input('user_id');

            if (!$month || !$year) {
                return response()->json(['message' => 'Month and year are required'], 400);
            }

            $MyWallet= $this->servicewallet->getMyWalletBalance($request);

            $walletData = $MyWallet->getData(true);

            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }


            $query = Debt::query();
            $query->where('admin_id', $admin_id);

            $query->where('pay', $pay)
                ->whereBetween('debt_timestamp', ["$year-$month-01 00:00:00", "$year-$month-31 23:59:59"]);

            if ($userId) {
                $query->where('user_id', $userId);
            }

            if ($pay == 1) {
                $total = $query->sum('amount_paid');
            } else {
                $total = $query->get()->sum(function ($debt) {
                    return $debt->amount - $debt->amount_paid;
                });
            }

            return response()->json(['total' => $total], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function updateDebt($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }

            $debt = Debt::find($id);
            if (!$debt) {
                return response()->json(['message' => 'Debt not found'], 404);
            }

            $request->validate([
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string',
                'debt_timestamp' => 'required'
            ]);

            $MyWallet= $this->servicewallet->getMyWalletBalance($request);

            $walletData = $MyWallet->getData(true);

            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $newAmount = $request->input('amount');

            $wallet = Wallet::where('admin_id', $admin_id)->first();

            if (!$wallet) {
                return response()->json(['message' => 'Admin wallet not found'], 404);
            }

            if ($newAmount < $debt->amount_paid) {
                return response()->json([
                    'message' => 'The new amount cannot be less than the amount paid. Please update partial payments first.'
                ], 400);
            }

            $difference = $newAmount - $debt->amount;

            $debt->update($request->only(['amount', 'description', 'debt_timestamp']));

            if ($debt->amount_paid >= $debt->amount) {
                $debt->pay = 1;
                $debt->paid_at = now();
            } else {
                $debt->pay = 0;
                $debt->paid_at = null;
            }

            $debt->save();

            // Update the wallet balance
            $wallet->balance -= $difference;
            $wallet->save();

            // Update the wallet transaction
            $walletTransaction = WalletTransaction::where('debt_id', $debt->id)->first();
            if ($walletTransaction) {
                $walletTransaction->amount = $debt->amount;
                $walletTransaction->save();
            }

            DB::commit();

            return response()->json(['message' => 'Debt updated successfully', 'debt' => $debt], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function createPartialPayment($debtId, Request $request)
    {
        DB::beginTransaction();

        try {
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }

            $MyWallet= $this->servicewallet->getMyWalletBalance($request);

            $walletData = $MyWallet->getData(true);

            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $debt = Debt::find($debtId)->where('admin_id', $admin_id)->first();


            if (!$debt) {
                return response()->json(['message' => 'Debt not found'], 404);
            }

            $request->validate([
                'amount' => 'required|numeric|min:1'
            ]);

            $amount = $request->input('amount');

            if ($debt->pay) {
                return response()->json(['message' => 'Debt has already been paid'], 400);
            }

            if ($debt->amount_paid + $amount > $debt->amount) {
                return response()->json(['message' => 'Partial payment exceeds the remaining debt amount'], 400);
            }

            $wallet = Wallet::where('admin_id', $admin_id)->first();

            if (!$wallet) {
                return response()->json(['message' => 'Admin wallet not found'], 404);
            }

            $partialPayment = PartialPayment::create([
                'debt_id' => $debt->id,
                'amount' => $amount
            ]);

            $debt->amount_paid += $amount;

            if ($debt->amount_paid >= $debt->amount) {
                $debt->pay = 1;
                $debt->paid_at = now();
            }

            $debt->save();

            // Update the wallet balance
            $wallet->balance += $amount;
            $wallet->save();

            // Insert a wallet transaction
            WalletTransaction::create([
                'admin_id' => $admin_id,
                'debt_id' => $debt->id,
                'type' => 'credit',
                'amount' => $amount
            ]);

            DB::commit();

            return response()->json(['message' => 'Partial payment created successfully', 'partial_payment' => $partialPayment], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function updatePartialPayments($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }

            $MyWallet= $this->servicewallet->getMyWalletBalance($request);

            $walletData = $MyWallet->getData(true);

            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $partialPayment = PartialPayment::find($id);

            if (!$partialPayment) {
                return response()->json(['message' => 'Partial payment not found'], 404);
            }

            $request->validate([
                'amount' => 'required|numeric|min:1'
            ]);

            $newAmount = $request->input('amount');
            $debt = Debt::find($partialPayment->debt_id)->where('admin_id', $admin_id)->first();


            if (!$debt) {
                return response()->json(['message' => 'Associated debt not found'], 404);
            }

            $wallet = Wallet::where('admin_id', $admin_id)->first();

            if (!$wallet) {
                return response()->json(['message' => 'Admin wallet not found'], 404);
            }

            $difference = $newAmount - $partialPayment->amount;

            $totalPaid = PartialPayment::where('debt_id', $debt->id)->sum('amount') - $partialPayment->amount + $newAmount;

            if ($totalPaid > $debt->amount) {
                return response()->json(['message' => 'Total payments exceed debt amount'], 400);
            }

            $partialPayment->amount = $newAmount;
            $partialPayment->save();

            // Update debt status
            $debt->amount_paid = $totalPaid;
            if ($debt->amount_paid >= $debt->amount) {
                $debt->pay = 1;
                $debt->paid_at = now();
            } else {
                $debt->pay = 0;
                $debt->paid_at = null;
            }
            $debt->save();

            // Update the wallet balance
            $wallet->balance -= $difference;
            $wallet->save();

            // Update the wallet transaction
            $walletTransaction = WalletTransaction::where('debt_id', $debt->id)->where('amount', $partialPayment->amount)->first();
            if ($walletTransaction) {
                $walletTransaction->amount = $newAmount;
                $walletTransaction->save();
            }

            DB::commit();

            return response()->json(['message' => 'Partial payment updated successfully', 'partial_payment' => $partialPayment], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function getStatistics(Request $request)
    {
        try {
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }

            $MyWallet= $this->servicewallet->getMyWalletBalance($request);

            $walletData = $MyWallet->getData(true);

            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $month = $request->input('month');
            $year = $request->input('year');

            if (!$month || !$year) {
                return response()->json(['message' => 'Month and year are required'], 400);
            }

            $startDate = "$year-$month-01 00:00:00";
            $endDate = "$year-$month-31 23:59:59";

            $totalDebts = Debt::where('admin_id', $admin_id)->whereBetween('debt_timestamp', [$startDate, $endDate])->sum('amount');
            $totalPaid = Debt::where('admin_id', $admin_id)->whereBetween('paid_at', [$startDate, $endDate])->sum('amount');

            return response()->json([
                'total_debts' => $totalDebts,
                'total_paid' => $totalPaid,
                'outstanding' => $totalDebts - $totalPaid
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function getUserStatistics($userId,Request $request)
    {
        try {

            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }
            $MyWallet= $this->servicewallet->getMyWalletBalance($request);

            $walletData = $MyWallet->getData(true);

            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $totalDebts = Debt::where('admin_id',$admin_id)->where('user_id', $userId)->sum('amount');
            $totalPaid = Debt::where('admin_id',$admin_id)->where('user_id', $userId)->where('pay', 1)->sum('amount');

            return response()->json([
                'total_debts' => $totalDebts,
                'total_paid' => $totalPaid,
                'outstanding' => $totalDebts - $totalPaid
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function getAllDebtsForUser($id, Request $request)
    {
        try {
            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }
            $MyWallet= $this->servicewallet->getMyWalletBalance($request);

            $walletData = $MyWallet->getData(true);

            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Validate the user_id
            if (!$id) {
                return response()->json(['message' => 'User ID is required'], 400);
            }


            // Get the 'pay' status filter from the request
            $pay = $request->input('pay');

            // Initialize the query
            $query = Debt::where('admin_id',$admin_id)->where('user_id', $id);

            // Apply filter based on 'pay' status if provided
            if ($pay !== null) {
                $query->where('pay', $pay);
            }

            // Get the paginated result
            $debts = $query->paginate($request->input('pageSize', 10), ['*'], 'page', $request->input('page', 1));

            return response()->json($debts, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function getPartialPayments($debtId, Request $request)
    {
        try {

            $authorization = $request->header('Authorization');
            if (!$authorization) {
                return response()->json(['message' => 'Authorization token is required'], 400);
            }

            $MyWallet= $this->servicewallet->getMyWalletBalance($request);

            $walletData = $MyWallet->getData(true);

            if (isset($walletData['data']['id'])) {
                $admin_id = $walletData['data']['id'];
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Find the debt by id
            $debt = Debt::find($debtId)->where('admin_id', $admin_id)->first();

            // Check if the debt exists
            if (!$debt) {
                return response()->json(['message' => 'Debt not found'], 404);
            }

            // Get all partial payments for the debt
            $partialPayments = $debt->partialPayments;

            return response()->json(['partial_payments' => $partialPayments], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
