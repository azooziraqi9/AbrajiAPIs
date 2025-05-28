<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Users\Interfaces\UsersServiceInterface;

class UsersController extends Controller
{
    protected $usersService;

    public function __construct(UsersServiceInterface $usersService)
    {
        $this->usersService = $usersService;
    }

    public function usersTable(Request $request)
    {
        try {
            $request->validate([
                'payload' => 'required',
            ]);

            $response = $this->usersService->getUsersTable($request->all(), $request->header("Authorization"), $request->page);
            return response()->json($response);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => $e->getCode()
            ], $e->getCode());
        }
    }

    public function onlineUsers(Request $request)
    {
        try {
            $request->validate([
                'payload' => 'required',
            ]);

            $response = $this->usersService->getOnlineUsers($request->all(), $request->header("Authorization"), $request->page);
            return response()->json($response);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => $e->getCode()
            ], $e->getCode());
        }
    }

    public function createUser(Request $request)
    {
        try {
            $request->validate([
                'payload' => 'required',
            ]);

            $response = $this->usersService->createUser($request->all(), $request->header("Authorization"));
            return response()->json($response);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => $e->getCode()
            ], $e->getCode());
        }
    }

    public function getUserById(Request $request, $id)
    {
        try {
            $response = $this->usersService->getUserById($id, $request->header('Authorization'));
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => $e->getCode()
            ], $e->getCode());
        }
    }

    public function editUser(Request $request, $id)
    {
        try {
            $request->validate([
                'payload' => 'required',
            ]);

            $response = $this->usersService->editUser($id, $request->all(), $request->header("Authorization"));
            return response()->json($response);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => $e->getCode()
            ], $e->getCode());
        }
    }

    public function disconnectUser(Request $request, $id)
    {
        try {
            $response = $this->usersService->disconnectUser($id, $request->header('Authorization'));
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => $e->getCode()
            ], $e->getCode());
        }
    }

    public function usersActivate(Request $request)
    {
        try {
            $request->validate([
                'payload' => 'required',
            ]);

            $response = $this->usersService->usersActivate($request->all(), $request->header("Authorization"));
            return response()->json($response);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => $e->getCode()
            ], $e->getCode());
        }
    }
}
