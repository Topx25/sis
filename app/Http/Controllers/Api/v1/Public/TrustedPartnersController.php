<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerResource;
use App\Http\Resources\PartnersPageResource;
use App\Services\PartnersPageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class TrustedPartnersController extends Controller
{
    use ApiResponse;

    public function __construct(protected PartnersPageService $service) {}

    public function index(): JsonResponse
    {
        $cacheKey = 'api_partners_page_v2_' . app()->getLocale();

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () {
            $data = $this->service->getPageData();

            return [
                'header'            => $data['page'] ? (new PartnersPageResource($data['page']))->resolve() : null,
                'featured_partners' => PartnerResource::collection($data['featured_partners'])->resolve(),
                'partners'          => PartnerResource::collection($data['partners'])->resolve(),
            ];
        });

        return $this->successResponse($data, 'Trusted partners retrieved successfully.');
    }
}
