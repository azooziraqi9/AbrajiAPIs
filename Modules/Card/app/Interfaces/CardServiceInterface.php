<?php

namespace Modules\Card\Interfaces;

use Illuminate\Http\Request;

interface CardServiceInterface
{
    // get all cards
    public function getAllCards(Request $request);

    //get details of single card by id
    public function getCardById($id, Request $request);

    //get list of cards in the series
    public function getListCardForSeries($id,Request $request);
}
