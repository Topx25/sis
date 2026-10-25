<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(protected AuthService $authService) {}

    public function login(AdminLoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->email, $request->password);
        return $this->successResponse([
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
            ],
            'access_token' => $result['token'],
            'refresh_token' => $result['refresh_token'],
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ], 'Admin logged in successfully.');
    }
    // دالة الـ Refresh الجديدة
    public function refresh(Request $request): JsonResponse
    {
        // اليوزر بيبعت الـ refresh_token في الـ Body
        $request->validate([
            'refresh_token' => 'required|string'
        ]);
        $result = $this->authService->refreshToken($request->refresh_token);
        return $this->successResponse([
            'access_token' => $result['token'],
            'refresh_token' => $result['refresh_token'],
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ], 'Token refreshed successfully.');
    }
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return $this->successResponse(null, 'Logged out successfully.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ], 'User profile retrieved successfully.');
    }
}
