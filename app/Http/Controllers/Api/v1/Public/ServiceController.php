<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $services = Service::with('seoMetadata')->get();
        $defaultSection = [
            'id' => 1,
            'subtitle' => 'Services & Capabilities',
            'title' => 'One platform,',
            'title_highlight' => 'every',
            'description' => 'industrial capability',
        ];

        try {
            // Read-only: never create a row during a GET (breaks on read-only/replica DBs).
            $section = \App\Models\ServicesSection::first() ?: (object) $defaultSection;
        } catch (\Throwable $e) {
            $section = (object) $defaultSection;
        }

        return $this->successResponse([
            'section' => $section,
            'services' => ServiceResource::collection($services),
        ], 'Services retrieved successfully.');
    }

    public function show(string $slug): JsonResponse
    {
        $service = Service::where('slug', $slug)->firstOrFail();
        return $this->successResponse(new ServiceResource($service), 'Service details retrieved successfully.');
    }
}
