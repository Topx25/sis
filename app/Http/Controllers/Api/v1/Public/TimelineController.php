<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\TimelineEventResource;
use App\Models\TimelineEvent;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class TimelineController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $timeline = TimelineEvent::all()->sortBy(function ($event) {
            if (preg_match('/\d+/', (string)$event->year, $matches)) {
                $num = (int)$matches[0];
                return $num < 100 ? $num + 2000 : $num;
            }
            return 0;
        })->values();

        return $this->successResponse(TimelineEventResource::collection($timeline), 'Timeline events retrieved successfully.');
    }

    public function section(): JsonResponse
    {
        $section = \App\Models\TimelineSection::find(1) ?? \App\Models\TimelineSection::first();

        if (!$section) {
            $section = (object)[
                'id' => 1,
                'tagline' => 'Our journey',
                'title' => 'A trajectory of',
                'title_highlight' => 'growth.',
            ];
        }

        return $this->successResponse($section, 'Timeline section header retrieved successfully.');
    }
}
