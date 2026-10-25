<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactPageResource;
use App\Models\ContactPage;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ContactPageController extends Controller
{
    use ApiResponse;

    public function show(): JsonResponse
    {
        $page = ContactPage::first();
        if (!$page) {
            return $this->successResponse(null, 'Contact page data not found.');
        }
        
        // Optionally format for public consumption if Spatie handles it automatically,
        // we can just return the resource.
        $resource = new ContactPageResource($page);
        
        return $this->successResponse(json_decode($resource->toJson(), true), 'Contact page retrieved successfully.');
    }
}
