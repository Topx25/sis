<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutSectionResource;
use App\Models\AboutSection;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AboutController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $locale = app()->getLocale();

        $aboutData = Cache::remember("sis_section_vision_data_{$locale}", 3600, function () {
            $about = AboutSection::where('is_active', true)->first();
            return $about ? (new AboutSectionResource($about))->resolve() : null;
        });

        if (!$aboutData) {
            return $this->errorResponse('About section data not found.', 404);
        }

        return $this->successResponse($aboutData, 'About section retrieved successfully.');
    }
}
