<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Manager\Interfaces\IMangerService;
use Modules\Manager\Models\Manager;
use Modules\Wallet\Models\Wallet;

class LoginController extends Controller
{

    protected $ManagerService;

    public function __construct(IMangerService $ManagerService)
    {
        $this->ManagerService = $ManagerService;
    }
    public function getAdminIdFromToken($token)
    {
        try {
            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $tokenParts = explode('.', $token);
            $payload = base64_decode($tokenParts[1]);
            $decoded = json_decode($payload);

            if (isset($decoded->sub)) {
                return $decoded->sub;
            } else {
                throw new \Exception('Admin ID not found in token');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'payload' => 'required',
        ]);


        try {
            $client = new Client();
//            $url = getenv("API_DOMAIN") . '/admin/api/index.php/api/login';
            $url = config('app.api_domain') . '/admin/api/index.php/api/login';
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(['payload' => $request->payload])
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() === 200) {
//                session(["token"=> $responseData['token']]);
                Session::put('token' , $responseData['token']);
                $admin_id = $this->getAdminIdFromToken($responseData['token']);
                $MyWallet=Wallet::where('admin_id',$admin_id)->first();
                if (!$MyWallet) {
                    try {
                        $admin_data = $this->ManagerService->GetManger('Bearer ' . $responseData['token']);
                        $admin_id = $admin_data['data'][0]['id'];
                        $admin_name = $admin_data['data'][0]['username'];

                        Manager::firstOrCreate(['admin_id' => $admin_id, 'username' => $admin_name]);
                        Wallet::firstOrCreate(['admin_id' => $admin_id, 'username' => $admin_name, 'balance' => 0]);
                    } catch (\Exception $e) {
                        return response()->json([
                            'error' => 'Error fetching manager data: ' . $e->getMessage(),
                        ], 500);
                    }
                }

                return response()->json($responseData);
            } else {
                return response()->json([
                    'status' => 401,
                    'error' => 'Wrong username or password'
                ], 401);
            }

        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
}
