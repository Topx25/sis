<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Models\VisitorCredential;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VisitorAccessController extends Controller
{
    use ApiResponse;

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $inputUsername = trim($request->username);
        $inputPassword = trim($request->password);

        $credentials = VisitorCredential::where('username', $inputUsername)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', Carbon::now());
            })
            ->get();

        $validCredential = null;
        foreach ($credentials as $credential) {
            if (Hash::check($inputPassword, $credential->password)) {
                $validCredential = $credential;
                break;
            }
        }

        if (!$validCredential) {
            return $this->errorResponse('Invalid or expired credentials.', 422, [
                'credentials' => ['The provided username or password is invalid or has expired.']
            ]);
        }

        return $this->successResponse([
            'valid' => true,
            'expires_at' => $validCredential->expires_at?->toIso8601String(),
        ], 'Credential verified successfully.');
    }
}
