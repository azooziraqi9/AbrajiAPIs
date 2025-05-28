<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Users\Services\SessionService;

class SessionController extends Controller
{
    private $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function getUserSessions(Request $request, $id)
    {
        $authorization = $request->header('Authorization');
        $payload = $request->payload;
        $page = $request->input('page');
        $response = $this->sessionService->handle($payload,$authorization, $id, $page);

        if (isset($response['error'])) {
            return response()->json([
                'error' => $response['error'],
                'status' => $response['status'] ?? '500'
            ], $response['status']);
        }

        return response()->json($response, 200);
    }
}
