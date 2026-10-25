<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectorResource;
use App\Models\Sector;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class SectorController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $sectors = Sector::withCount('projects')->get();

        $capabilities = [
            'title' => 'The SiS Capability Matrix',
            'columns' => [
                'SCADA Upgrades',
                'ERP Integration',
                'Hardware Commissioning',
                'Safety & Shutdown Systems',
                'Network Cybersecurity'
            ],
            'rows' => [
                ['client' => 'Henkel', 'capabilities' => [true, false, true, false, false]],
                ['client' => 'BariQ', 'capabilities' => [true, true, true, true, false]],
                ['client' => 'Gas Cool', 'capabilities' => [true, false, true, false, true]],
                ['client' => 'Emisal', 'capabilities' => [true, true, true, true, false]],
                ['client' => 'Saline Water', 'capabilities' => [true, false, true, true, true]],
                ['client' => 'Saudi Aramco', 'capabilities' => [true, false, true, true, true]],
                ['client' => 'Béjaïa RO', 'capabilities' => [true, false, true, true, true]],
            ]
        ];

        return $this->successResponse([
            'sectors' => SectorResource::collection($sectors),
            'capabilities' => $capabilities
        ], 'Sectors retrieved successfully.');
    }
}
