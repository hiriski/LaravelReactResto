<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\User as AuthUserResource;

class LoginController extends Controller
{

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(
        LoginRequest $request
    ): \Illuminate\Http\Response | JsonResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $user   = $this->authService->login($request);
            $token  = $user->createToken($user->email)->plainTextToken;
            return response()->json([
                'token'   => $token,
                'data'    => new AuthUserResource($user)
            ], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response([
                'status' => false,
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
