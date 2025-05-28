<?php

namespace Modules\Debts\Interfaces;

use Illuminate\Http\Request;

interface DebtServiceInterface
{
    public function getAll(Request $request);

    public function create(Request $request);

    public function getByUserId($id,Request $request);

    public function payDebt($id);

    public function getAllDebtsForUser($id, Request $request);

    public function filter(Request $request);

    public function updateDebt($id, Request $request);

    public function createPartialPayment($debtId, Request $request);

    public function getPartialPayments($debtId, Request $request);

    public function updatePartialPayments($id, Request $request);

    public function getStatistics(Request $request);

    public function getUserStatistics($userId,Request $request);

}
