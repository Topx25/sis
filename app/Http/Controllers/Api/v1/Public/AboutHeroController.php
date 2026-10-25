<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutHeroResource;
use App\Models\AboutHero;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AboutHeroController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $aboutHeroData = Cache::remember('sis_section_about_hero_data', 3600, function () {
            $aboutHero = AboutHero::where('is_active', true)->first();
            return $aboutHero ? (new AboutHeroResource($aboutHero))->resolve() : null;
        });

        if (!$aboutHeroData) {
            return $this->errorResponse('About hero section data not found.', 404);
        }

        return $this->successResponse($aboutHeroData, 'About hero section retrieved successfully.');
    }
}
