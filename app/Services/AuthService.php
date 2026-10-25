<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthService
{
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid login credentials.'],
            ]);
        }


        // هنا بنعمل Access Token بمدة صلاحية 60 دقيقة
        $accessToken = $user->createToken('admin_access_token', ['*'], now()->addMinutes(60))->plainTextToken;

        // بنعمل Refresh Token عشوائي، ونخزّن نسخته المشفّرة (hash) فقط في الداتابيز
        $refreshToken = Str::random(64);
        $user->update(['refresh_token' => hash('sha256', $refreshToken)]);
        return [
            'user' => $user,
            'token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }
    // دالة جديدة لاستخراج توكن جديد
    public function refreshToken(string $refreshToken): array
    {
        $user = User::where('refresh_token', hash('sha256', $refreshToken))->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'refresh_token' => ['Invalid or expired refresh token.'],
            ]);
        }
        // بنمسح التوكن القديم كنوع من الأمان
        $user->tokens()->delete();
        // نطلع توكنات جديدة
        $newAccessToken = $user->createToken('admin_access_token', ['*'], now()->addMinutes(60))->plainTextToken;
        $newRefreshToken = Str::random(64);

        $user->update(['refresh_token' => hash('sha256', $newRefreshToken)]);
        return [
            'token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
        ];
    }
    public function logout(User $user): void
    {
        $user->tokens()->delete();
        $user->update(['refresh_token' => null]);

    }
}
