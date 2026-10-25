<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $section = \App\Models\ClientsSection::first();
        $clients = Client::orderBy('sort_order')->get();

        return $this->successResponse([
            'section' => $section,
            'clients' => ClientResource::collection($clients),
        ], 'Clients retrieved successfully.');
    }
}
