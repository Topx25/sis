<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\HeaderResource;
use App\Models\HeaderSetting;
use App\Traits\ApiResponse;
use App\Traits\ImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminHeaderController extends Controller
{
    use ApiResponse, ImageTrait;

    /**
     * Retrieve complete editable header settings.
     */
    public function show(): JsonResponse
    {
        $setting = HeaderSetting::instance();

        return $this->successResponse(
            new HeaderResource($setting),
            'Header settings retrieved successfully.'
        );
    }

    /**
     * Update header settings (logo, navigation links, and CTA button).
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'logo'           => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp', 'max:' . config('image.max_size_kb', 10240)],
            'button_text'    => ['nullable', 'string', 'max:255'],
            'button_link'    => ['nullable', 'string', 'max:500'],
            'button_enabled' => ['nullable'],
            'nav_links'      => ['nullable'],
        ]);

        $setting = HeaderSetting::instance();

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            $setting->logo_path = $this->uploadImage(
                $request->file('logo'),
                'header',
                $setting->logo_path
            );
        } elseif ($request->boolean('remove_logo')) {
            if ($setting->logo_path) {
                $this->deleteImage($setting->logo_path);
                $setting->logo_path = null;
            }
        }

        // Handle CTA Button
        if ($request->has('button_text')) {
            $setting->button_text = (string) $request->input('button_text');
        }

        if ($request->has('button_link')) {
            $setting->button_link = (string) $request->input('button_link');
        }

        if ($request->has('button_enabled')) {
            $setting->button_enabled = $request->boolean('button_enabled');
        }

        // Handle Nav Links
        if ($request->has('nav_links')) {
            $navLinks = $request->input('nav_links');
            if (is_string($navLinks)) {
                $decoded = json_decode($navLinks, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $navLinks = $decoded;
                }
            }

            if (is_array($navLinks)) {
                $setting->nav_links = $navLinks;
            }
        }

        $setting->save();

        Cache::forget('api_header_settings');

        return $this->successResponse(
            new HeaderResource($setting),
            'Header settings updated successfully.'
        );
    }
}
