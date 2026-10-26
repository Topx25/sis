<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Admin-only user management (role:admin). Supports roles: admin | editor.
 */
class AdminUserController extends Controller
{
    use ApiResponse;

    private const ROLES = ['admin', 'editor'];

    public function index(): JsonResponse
    {
        return $this->successResponse(
            UserResource::collection(User::orderBy('id')->get()),
            'Users retrieved successfully.'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', Password::min(8)],
            'role'     => ['required', Rule::in(self::ROLES)],
        ]);

        $user = User::create($data);

        return $this->successResponse(new UserResource($user), 'User created successfully.', 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', Password::min(8)],
            'role'     => ['sometimes', Rule::in(self::ROLES)],
        ]);

        // Never let the last admin be demoted (would lock everyone out of admin-only areas).
        if (
            array_key_exists('role', $data)
            && $user->role === 'admin'
            && $data['role'] !== 'admin'
            && User::where('role', 'admin')->count() <= 1
        ) {
            return $this->errorResponse('Cannot change the role of the last remaining admin.', 422);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return $this->successResponse(new UserResource($user->fresh()), 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->id === $user->id) {
            return $this->errorResponse('You cannot delete your own account.', 422);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return $this->errorResponse('Cannot delete the last remaining admin.', 422);
        }

        $user->delete();

        return $this->successResponse(null, 'User deleted successfully.');
    }
}
