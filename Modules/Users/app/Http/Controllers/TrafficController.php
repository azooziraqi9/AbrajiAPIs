<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Users\Services\TrafficService;

class TrafficController extends Controller
{
    private $trafficService;

    public function __construct(TrafficService $trafficService)
    {
        $this->trafficService = $trafficService;
    }

    public function trafficData(Request $request)
    {
        $authorization = $request->header('Authorization');
        $payload = $request->payload;

        $response = $this->trafficService->handle($authorization, $payload);

        if (isset($response['error'])) {
            return response()->json([
                'error' => $response['error'],
                'status' => $response['status'] ?? '500'
            ], $response['status']);
        }

        return response()->json($response, 200);
    }
}
