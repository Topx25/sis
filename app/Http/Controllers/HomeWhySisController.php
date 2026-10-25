<?php

namespace App\Http\Controllers;

use App\Models\HomeWhySis;
use Illuminate\Http\JsonResponse;

class HomeWhySisController extends Controller
{
    public function index(): JsonResponse
    {
        $section = \App\Models\HomeWhySisSection::find(1);
        
        $features = HomeWhySis::orderBy('sort_order')->get()->map(function ($feature) {
            return [
                'id' => $feature->id,
                'title' => $feature->title,
                'description' => $feature->description,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'section' => $section,
                'features' => $features,
            ],
            'message' => 'Why SIS data retrieved successfully.'
        ]);
    }
}
