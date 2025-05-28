<?php

namespace Modules\Card\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Card\Interfaces\CardServiceInterface;

class CardController extends Controller
{
  Protected $cardService;

    public function __construct(CardServiceInterface $cardService)
    {
        $this->cardService = $cardService;
    }

    public function getAllCards(Request $request)
    {
        return $this->cardService->getAllCards($request);
    }

    public function getCardById($id, Request $request)
    {
        return $this->cardService->getCardById($id, $request);
    }

    public function getListCardForSeries($id,Request $request)
    {
        return $this->cardService->getListCardForSeries($id,$request);
    }

}
