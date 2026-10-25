<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Resources\ContactRequestResource;
use App\Services\ContactService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    use ApiResponse;

    public function __construct(protected ContactService $contactService) {}

    public function store(StoreContactRequest $request): JsonResponse
    {
        $contactRequest = $this->contactService->createContactRequest($request->validated());
        return $this->createdResponse(new ContactRequestResource($contactRequest), 'Thank you for reaching out to SiS. Our engineering team will contact you shortly.');
    }
}
