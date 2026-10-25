<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\HeroSectionResource;
use App\Models\HeroSection;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HeroController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $heroData = Cache::remember('sis_section_hero_data', 3600, function () {
            $hero = HeroSection::where('is_active', true)->first();
            return $hero ? (new HeroSectionResource($hero))->resolve() : null;
        });

        if (!$heroData) {
            return $this->errorResponse(__('messages.responses.hero_not_found'), 404);
        }

        return $this->successResponse($heroData, __('messages.responses.hero_retrieved'));
    }
}
