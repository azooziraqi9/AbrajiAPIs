<?php

namespace Modules\Wallet\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Wallet\Interfaces\WalletServiceInterface;

class WalletController extends Controller
{

    protected $walletService;

    public function __construct(WalletServiceInterface $walletService)
    {
        $this->walletService = $walletService;
    }

    public function getMyWalletBalance(Request $request)
    {
        return $this->walletService->getMyWalletBalance($request);
    }

    public function addMoneyToWallet(Request $request)
    {
        return $this->walletService->addMoneyToWallet($request);
    }

    public function updateWalletAmount(Request $request)
    {
        return $this->walletService->updateWalletAmount($request);
    }

    public function resetWalletAmount(Request $request)
    {
        return $this->walletService->resetWalletAmount($request);
    }

    public function getWalletTransactions(Request $request)
    {
        return $this->walletService->getWalletTransactions($request);
    }
}
