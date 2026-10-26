<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutHero;
use App\Models\AboutSection;
use App\Models\CoreValue;
use App\Models\EmpoweredDomain;
use App\Models\HeroSection;
use App\Models\ItOtDivide;
use App\Traits\ApiResponse;
use App\Traits\ImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSectionController extends Controller
{
    use ApiResponse, ImageTrait;

    protected array $sectionMap = [
        'hero' => HeroSection::class,
        'vision' => AboutSection::class,
        'about' => AboutSection::class,
        'about-hero' => AboutHero::class,
        'it-ot' => ItOtDivide::class,
        'core-values' => CoreValue::class,
        'domains' => EmpoweredDomain::class,
        'home-why-sis' => \App\Models\HomeWhySisSection::class,
        'why-sis' => \App\Models\HomeWhySisSection::class,
        'services' => \App\Models\ServicesSection::class,
        'clients' => \App\Models\ClientsSection::class,
        'timeline' => \App\Models\TimelineSection::class,
    ];

    public function show(string $section): JsonResponse
    {
        if (!isset($this->sectionMap[$section])) {
            return $this->errorResponse("Unknown section resource: {$section}", 404);
        }

        $modelClass = $this->sectionMap[$section];
        
        // Assuming sections are singletons with ID 1
        $item = $modelClass::find(1) ?? $modelClass::first();

        if (!$item) {
            if ($section === 'services') {
                $item = $modelClass::firstOrCreate(
                    ['id' => 1],
                    [
                        'subtitle' => 'Services & Capabilities',
                        'title' => 'One platform,',
                        'title_highlight' => 'every',
                        'description' => 'industrial capability',
                    ]
                );
            } elseif ($section === 'clients') {
                $item = $modelClass::firstOrCreate(
                    ['id' => 1],
                    [
                        'subtitle' => 'Our Clients',
                        'title' => 'Trusted by',
                        'title_highlight' => 'Industry Leaders',
                        'description' => 'Partnering with leading organizations across the MENA region to deliver industrial excellence.',
                    ]
                );
            } elseif ($section === 'timeline') {
                $item = $modelClass::firstOrCreate(
                    ['id' => 1],
                    [
                        'tagline' => 'Our journey',
                        'title' => 'A trajectory of',
                        'title_highlight' => 'growth.',
                    ]
                );
            } else {
                return $this->errorResponse("{$section} record not found.", 404);
            }
        }

        return $this->successResponse($item, ucfirst($section) . ' section retrieved successfully.');
    }

    public function update(Request $request, string $section, int $id): JsonResponse
    {
        if (!isset($this->sectionMap[$section])) {
            return $this->errorResponse("Unknown section resource: {$section}", 404);
        }

        $modelClass = $this->sectionMap[$section];
        $item = $modelClass::find($id) ?? $modelClass::first();

        if (!$item) {
            if ($section === 'services' || $section === 'clients' || $section === 'timeline') {
                $item = new $modelClass();
                $item->id = $id;
            } else {
                return $this->errorResponse("{$section} record not found.", 404);
            }
        }

        $data = $request->except(['_method', '_token']);

        // Handle file uploads (image, logo, etc.)
        $fileFields = ['image', 'image_path', 'logo', 'logo_path'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $request->validate([
                    $field => ['file', 'mimes:jpeg,png,jpg,webp,svg', 'max:' . config('image.max_size_kb', 10240)],
                ]);

                $oldFilePath = $item->{$field} ?? null;
                $filePath = $this->uploadImage($request->file($field), 'sections', $oldFilePath);

                $data[$field] = $filePath;
                if ($field === 'image') $data['image_path'] = $filePath;
                if ($field === 'logo') $data['logo_path'] = $filePath;
            } elseif (array_key_exists($field, $data) && is_string($data[$field])) {
                // Ignore raw string URLs passed during update so existing DB values remain unchanged
                unset($data[$field]);
            }
        }

        if ($section === 'it-ot') {
            if (isset($data['ot']) && !isset($data['ot_data'])) {
                $data['ot_data'] = $data['ot'];
            }
            if (isset($data['it']) && !isset($data['it_data'])) {
                $data['it_data'] = $data['it'];
            }
        }

        if ($item->exists) {
            $item->update($data);
        } else {
            $item->fill($data);
            $item->save();
        }
        $sectionKeys = [
            "sis_section_{$section}",
            "sis_section_{$section}_data",
            "sis_section_" . str_replace('-', '_', $section) . "_data",
        ];
        if ($section === 'vision' || $section === 'about') {
            $sectionKeys[] = "sis_section_vision_data";
            $sectionKeys[] = "sis_section_about_data";
        }
        if ($section === 'about-hero') {
            $sectionKeys[] = "sis_section_about_hero_data";
        }
        // Clear each base key plus its per-locale variants (read paths cache by locale).
        foreach (array_unique($sectionKeys) as $key) {
            \Illuminate\Support\Facades\Cache::forget($key);
            foreach (['en', 'ar'] as $loc) {
                \Illuminate\Support\Facades\Cache::forget("{$key}_{$loc}");
            }
        }

        return $this->successResponse($item->fresh(), ucfirst($section) . ' section updated successfully.');
    }
}
