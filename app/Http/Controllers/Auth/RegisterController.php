<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterWithEmailAndPasswordRequest;
use App\Http\Resources\Auth\User as AuthUserResource;

class RegisterController extends Controller
{

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function registerWithEmailAndPassword(
        RegisterWithEmailAndPasswordRequest $request
    ): \Illuminate\Http\Response | AuthUserResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new AuthUserResource($this->authService->registerWithEmailAndPassword($request));
        } catch (Exception $exception) {
            return response([
                'status' => false,
                'message' => $exception->getMessage()
            ], 422);
        }
    }
}
