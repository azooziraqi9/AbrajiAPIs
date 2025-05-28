<?php

namespace Modules\Wallet\Interfaces;

use Illuminate\Http\Request;

interface WalletServiceInterface
{

    //get my wallet balance
    public function getMyWalletBalance(Request $request);

    public function addMoneyToWallet(Request $request);

     public function updateWalletAmount(Request $request);

     public function resetWalletAmount(Request $request);

     public function getWalletTransactions(Request $request);




}
