<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Modules\Dashboard\Abstract\DashboardServiceInterface;
use Modules\Dashboard\Services\DashboardCardService;
use Modules\Dashboard\Services\DashboardService;
use Modules\Transaction\Services\TransactionService;

class DashboardController extends Controller
{
    protected  $dashboardService;
    protected $dashboardCardService;
    protected $transactionService;

    public function __construct(DashboardServiceInterface $dashboardService,
                                DashboardCardService $dashboardCardService,
                                TransactionService $transactionService)
    {
        $this->dashboardService = $dashboardService;
        $this->dashboardCardService = $dashboardCardService;
        $this->transactionService = $transactionService;
    }

    public function getDashboard(Request $request)
    {
        return $this->dashboardService->GetDashboard($request->header("Authorization"));
    }

    public function getCardsWidgets(Request $request)
    {
        $authorization = $request->header('Authorization');
        $payload = 'U2FsdGVkX19i3YM9oXk6HfWhE1LsJD+oi/mv3YrcDz0xC6GuNeCws1j4kqyn4pA00zIJ7lDHQiesUkWdwm5q5N6CAvAIwptL6tgRT4sWmDmd1XYIZ0UZe6DdguPNhh7bpQFkqdW86Pjhub6SlptOonCsBso3b/qEyqoEPsZIYkguSZH2Aje4A7w/zBm5GR0zDG1kLO/EDZ4JO+gvve8Qn379K55L+Ce4HT84iI1Le1A=';
        $response = $this->dashboardCardService->handle($authorization, $payload);

        if (isset($response['cards'])) {
            return response()->json($response, 200);
        } else {
            return response()->json(['error' => $response['error']], $response['status']);
        }
    }

    public function getTransactionsWidgets(Request $request)
    {
        try {
            $widgets = $this->transactionService->getTransactionWidgets($request);
            return response()->json($widgets);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch transaction widgets',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
