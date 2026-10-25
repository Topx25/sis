<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PartnerController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        // Older imports may contain more than one row for the same partner.
        // Return the canonical row only, preferring records with a configured
        // website and then the earliest record. This keeps the public API (and
        // dashboard) from showing the same partner more than once.
        $partners = Partner::query()
            ->orderByRaw('CASE WHEN website_url IS NULL THEN 1 ELSE 0 END')
            ->orderBy('id')
            ->get()
            ->unique(fn (Partner $partner) => Str::lower(trim($partner->name)))
            ->sortBy('sort_order')
            ->values()
            ->groupBy('type');

        $grouped = [
            'technology_partners' => PartnerResource::collection($partners->get('technology_partner', collect())),
            'ecosystem_networks' => PartnerResource::collection($partners->get('ecosystem_network', collect())),
        ];

        return $this->successResponse($grouped, 'Partners retrieved successfully.');
    }
}
