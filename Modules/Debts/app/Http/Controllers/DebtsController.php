<?php

namespace Modules\Debts\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Debts\Services\DebtService;

class DebtsController extends Controller
{

    protected $debtService;

    public function __construct(DebtService $debtService)
    {
        $this->debtService = $debtService;
    }

    //function to show all debts using pagination
    public function getAll(Request $request)
    {
       return $this->debtService->getAll($request);
    }

    //make function to create new debt
    public function create(Request $request)
    {
        return $this->debtService->create($request);
    }

    // make function to get debt by user id
    public function getByUserId($id,Request $request)
    {
        return $this->debtService->getByUserId($id, $request);
    }

    //make function to make payment for debt by id
    public function payDebt($id)
    {
        return $this->debtService->payDebt($id);
    }

    //make function to return total debts for user
    public function getAllDebtsForUser($id, Request $request)
    {
        return $this->debtService->getAllDebtsForUser($id, $request);
    }

    //make function to return total debts is paid in my system  send month and year
    public function filter(Request $request)
    {
        return $this->debtService->filter($request);
    }

    //make function to update debt by id
    public function updateDebt($id, Request $request)
    {
        return $this->debtService->updateDebt($id, $request);
    }

    //make function to create partial payment for debt
    public function createPartialPayment($debtId, Request $request)
    {
        return $this->debtService->createPartialPayment($debtId, $request);
    }

    //make function to get all partial payments for debt
    public function getPartialPayments($debtId, Request $request)
    {
        return $this->debtService->getPartialPayments($debtId, $request);
    }

    //make function to update  PartialPayments by id
    public function updatePartialPayments($id, Request $request)
    {
        return $this->debtService->updatePartialPayments($id, $request);
    }

    //make function to get some statistics about debts
    public function getStatistics(Request $request)
    {
        return $this->debtService->getStatistics($request);
    }

    //make function to get statistics for user
    public function getUserStatistics($userId,Request $request)
    {
        return $this->debtService->getUserStatistics($userId, $request);
    }

}
