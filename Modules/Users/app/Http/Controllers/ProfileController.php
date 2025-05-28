<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Users\Interfaces\ProfileServiceInterface;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileServiceInterface $profileService) {
        $this->profileService = $profileService;
    }
    public function getServices(Request $request, $id)
    {
        try {
            $data = $this->profileService->getServices($id, $request->header('Authorization'));
            return response()->json($data);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $message = $e->getResponse()->getBody()->getContents();
            return response()->json(['error' => $message, 'status' => $statusCode], $statusCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => $e->getCode()], $e->getCode());
        }
    }

    public function changeUserService(Request $request)
    {
        try {
            $request->validate(['payload' => 'required']);
            $data = $this->profileService->changeUserService($request->payload, $request->header('Authorization'));
            return response()->json($data);
        } catch (ValidationException $e) {
            $response = [
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'status' => $e->status,
            ];
            return response()->json($response, 422);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $message = $e->getResponse()->getBody()->getContents();
            return response()->json(['error' => $message, 'status' => $statusCode], $statusCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => $e->getCode()], $e->getCode());
        }
    }
    //get manger tree and return any error
    public function getManagerTree(Request $request)
    {
        try {
            $data = $this->profileService->getManagerTree($request->header('Authorization'));
            return response()->json($data);
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $message = $e->getResponse()->getBody()->getContents();
            return response()->json(['error' => $message, 'status' => $statusCode], $statusCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => $e->getCode()], $e->getCode());
        }
    }
    //user/changeProfile
    public function changeProfile(Request $request)
    {
        try {
            $request->validate(['payload' => 'required']);
            $data = $this->profileService->changeProfile($request->payload, $request->header('Authorization'));
            return response()->json($data);
        } catch (ValidationException $e) {
            $response = [
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'status' => $e->status,
            ];
            return response()->json($response, 422);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $message = $e->getResponse()->getBody()->getContents();
            return response()->json(['error' => $message, 'status' => $statusCode], $statusCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => $e->getCode()], $e->getCode());
        }
    }

    //git active data by user id
    public function getActiveData(Request $request, $id)
    {
        try {
            $data = $this->profileService->getActiveData($id, $request->header('Authorization'));
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
