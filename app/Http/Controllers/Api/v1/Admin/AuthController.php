<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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
                'role' => $result['user']->role,
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
            'role' => $request->user()->role,
        ], 'User profile retrieved successfully.');
    }

    /**
     * Update the currently authenticated user's own account (any role).
     * Changing the password requires the current password.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'             => ['sometimes', 'string', 'max:255'],
            'email'            => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required_with:password', 'current_password'],
            'password'         => ['nullable', 'string', Password::min(8)],
        ]);

        unset($data['current_password']);
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ], 'Profile updated successfully.');
    }
}
