<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactRequestResource;
use App\Models\ContactRequest;
use App\Services\ContactService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    use ApiResponse;

    public function __construct(protected ContactService $contactService) {}

    public function index(): JsonResponse
    {
        $requests = $this->contactService->getPaginatedRequests();
        return $this->successResponse([
            'requests' => ContactRequestResource::collection($requests),
            'pagination' => [
                'total' => $requests->total(),
                'current_page' => $requests->currentPage(),
                'total_pages' => $requests->lastPage(),
            ],
        ], 'Contact inquiries retrieved successfully.');
    }

    public function updateStatus(Request $request, ContactRequest $contactRequest): JsonResponse
    {
        $request->validate(['status' => ['required', 'in:unread,read,archived']]);
        $updated = $this->contactService->updateStatus($contactRequest, $request->status);
        return $this->successResponse(new ContactRequestResource($updated), 'Inquiry status updated successfully.');
    }

    public function reply(Request $request, ContactRequest $contactRequest): JsonResponse
    {
        $request->validate(['message' => ['required', 'string']]);
        $updated = $this->contactService->replyToRequest($contactRequest, $request->message);
        return $this->successResponse(new ContactRequestResource($updated), 'Reply sent successfully.');
    }
}

