<?php

namespace Modules\Manager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Manager\Interfaces\IMangerService;
use Modules\Manager\Services\MangerService;

class ManagerController extends Controller
{
   protected $IMangerService;

    public function __construct(IMangerService $IMangerService)
    {
           $this->IMangerService = $IMangerService;
    }
    public function GetManger(Request $request)
    {
        try {
            $data = $this->IMangerService->GetManger($request->header('Authorization'));
            return response()->json($data);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $message = $e->getResponse()->getBody()->getContents();
            return response()->json(['error' => $message, 'status' => $statusCode], $statusCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => $e->getCode()], $e->getCode());
        }
    }







}
