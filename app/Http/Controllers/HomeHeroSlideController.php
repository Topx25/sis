<?php

namespace App\Http\Controllers;

use App\Models\HomeHeroSlide;
use Illuminate\Http\JsonResponse;

class HomeHeroSlideController extends Controller
{
    public function index(): JsonResponse
    {
        $slides = HomeHeroSlide::orderBy('sort_order')->get()->map(function ($slide) {
            return [
                'id' => $slide->id,
                'src' => $slide->src,
                'alt' => $slide->alt,
                'title' => $slide->title,
                'description' => $slide->description,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $slides,
            'message' => 'Hero slides retrieved successfully.'
        ]);
    }
}
