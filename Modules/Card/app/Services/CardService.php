<?php

namespace Modules\Card\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Card\Interfaces\CardServiceInterface;

class CardService implements CardServiceInterface
{

    public function getAllCards(Request $request)
    {
        $apiClient = new HandleService();
        $url = config('app.api_domain') . '/admin/api/index.php/api/index/series';

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => $request->header('Authorization'),
        ];

        $body = ['payload' => $request->input('payload')];

        $response = $apiClient->sendRequest('POST', $url, $headers, $body);

        if ($response['status'] === 200) {
            return response()->json($response['data'], $response['status']);
        } else {
            return response()->json([
                'status' => $response['status'],
                'error' => $response['error']
            ], $response['status']);
        }

    }

    public function getCardById($id, Request $request)
    {
        $apiClient = new HandleService();
        $url = config('app.api_domain') . '/admin/api/index.php/api/cards/series/' . $id;

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => $request->header('Authorization'),
        ];

        $response = $apiClient->sendRequest('GET', $url, $headers);

        if ($response['status'] === 200) {
            return response()->json($response['data'], $response['status']);
        } else {
            return response()->json([
                'status' => $response['status'],
                'error' => $response['error']
            ], $response['status']);
        }

    }

    public function getListCardForSeries($id, Request $request)
    {
        $apiClient = new HandleService();
        $url = config('app.api_domain') . '/admin/api/index.php/api/index/card/' . $id;

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => $request->header('Authorization'),
        ];

        $body = ['payload' => $request->input('payload')];

        $response = $apiClient->sendRequest('POST', $url, $headers, $body);

        if ($response['status'] === 200) {
            return response()->json($response['data'], $response['status']);
        } else {
            return response()->json([
                'status' => $response['status'],
                'error' => $response['error']
            ], $response['status']);
        }
    }
}
