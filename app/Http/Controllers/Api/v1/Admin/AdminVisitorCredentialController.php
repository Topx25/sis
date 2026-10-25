<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorCredential;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminVisitorCredentialController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $credentials = VisitorCredential::latest()->get();
        return $this->successResponse($credentials, 'Visitor credentials retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'unique:visitor_credentials,username'],
            'password' => ['required', 'string', 'min:4'],
            'days_valid' => ['nullable', 'integer', 'min:1'],
        ]);

        $days = $validated['days_valid'] ?? 7;

        $credential = VisitorCredential::create([
            'username' => trim($validated['username']),
            'password' => Hash::make(trim($validated['password'])),
            'is_active' => true,
            'expires_at' => Carbon::now()->addDays($days),
        ]);

        return $this->createdResponse($credential, 'Visitor credential created successfully.');
    }

    public function update(Request $request, VisitorCredential $visitorCredential): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['nullable', 'string', 'unique:visitor_credentials,username,' . $visitorCredential->id],
            'password' => ['nullable', 'string', 'min:4'],
            'is_active' => ['nullable', 'boolean'],
            'days_valid' => ['nullable', 'integer', 'min:1'],
        ]);

        $data = [];
        if (isset($validated['username'])) $data['username'] = trim($validated['username']);
        if (!empty($validated['password'])) $data['password'] = Hash::make(trim($validated['password']));
        if (isset($validated['is_active'])) $data['is_active'] = $validated['is_active'];
        if (isset($validated['days_valid'])) $data['expires_at'] = Carbon::now()->addDays($validated['days_valid']);

        $visitorCredential->update($data);

        return $this->successResponse($visitorCredential->fresh(), 'Visitor credential updated successfully.');
    }

    public function destroy(VisitorCredential $visitorCredential): JsonResponse
    {
        $visitorCredential->delete();
        return $this->successResponse(null, 'Visitor credential deleted successfully.');
    }
}
