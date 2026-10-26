<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReorderFooterLinksRequest;
use App\Http\Requests\StoreFooterLinkRequest;
use App\Http\Requests\UpdateFooterLinkRequest;
use App\Http\Requests\UpdateFooterSettingsRequest;
use App\Http\Resources\FooterAdminResource;
use App\Http\Resources\FooterLinkResource;
use App\Models\FooterLink;
use App\Models\FooterSetting;
use App\Traits\ApiResponse;
use App\Traits\ImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFooterController extends Controller
{
    use ApiResponse, ImageTrait;

    /**
     * Retrieve complete editable footer settings and links.
     */
    public function show(): JsonResponse
    {
        $setting = FooterSetting::instance();

        return $this->successResponse(
            new FooterAdminResource($setting),
            'Footer settings and links retrieved successfully.'
        );
    }

    /**
     * Update footer settings (company info, copyright, floating contact).
     */
    public function update(UpdateFooterSettingsRequest $request): JsonResponse
    {
        $setting = FooterSetting::instance();
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $setting->logo_path = $this->uploadImage(
                $request->file('logo'),
                'footer',
                $setting->logo_path
            );
        } elseif ($request->boolean('remove_logo')) {
            if ($setting->logo_path) {
                $this->deleteImage($setting->logo_path);
                $setting->logo_path = null;
            }
        }

        if (array_key_exists('description', $validated)) {
            $setting->description = $validated['description'];
        }

        if (array_key_exists('copyright_text', $validated)) {
            $setting->copyright_text = $validated['copyright_text'];
        }

        if (array_key_exists('dynamic_year', $validated)) {
            $setting->dynamic_year = $request->boolean('dynamic_year');
        }

        if (array_key_exists('floating_contact_enabled', $validated)) {
            $setting->floating_contact_enabled = $request->boolean('floating_contact_enabled');
        }

        if (array_key_exists('floating_contact_url', $validated)) {
            $setting->floating_contact_url = $validated['floating_contact_url'];
        }

        if (array_key_exists('floating_contact_icon', $validated)) {
            $setting->floating_contact_icon = $validated['floating_contact_icon'] ?? 'whatsapp';
        }

        if (array_key_exists('floating_contact_label', $validated)) {
            $setting->floating_contact_label = $validated['floating_contact_label'] ?? 'Contact us on WhatsApp';
        }

        if (array_key_exists('metadata', $validated)) {
            $setting->metadata = $validated['metadata'];
        }

        $setting->save();

        return $this->successResponse(
            new FooterAdminResource($setting),
            'Footer settings updated successfully.'
        );
    }

    /**
     * Create a new footer link (navigation or contact).
     */
    public function storeLink(StoreFooterLinkRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Default sort order if not provided
        if (!isset($data['sort_order']) || $data['sort_order'] === null) {
            $maxOrder = FooterLink::where('category', $data['category'])->max('sort_order') ?? 0;
            $data['sort_order'] = $maxOrder + 1;
        }

        // Default target if omitted
        if (empty($data['target'])) {
            $data['target'] = ($data['category'] === FooterLink::CATEGORY_NAVIGATION)
                ? FooterLink::TARGET_SELF
                : FooterLink::TARGET_BLANK;
        }

        // Default type if omitted
        if (empty($data['type'])) {
            $data['type'] = ($data['category'] === FooterLink::CATEGORY_NAVIGATION)
                ? 'navigation'
                : 'custom';
        }

        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        $link = FooterLink::create($data);

        return $this->createdResponse(
            new FooterLinkResource($link),
            'Footer link created successfully.'
        );
    }

    /**
     * Update an existing footer link.
     */
    public function updateLink(UpdateFooterLinkRequest $request, int $id): JsonResponse
    {
        $link = FooterLink::find($id);
        if (!$link) {
            return $this->notFoundResponse('Footer link not found.');
        }

        $data = $request->validated();
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $link->update($data);

        return $this->successResponse(
            new FooterLinkResource($link),
            'Footer link updated successfully.'
        );
    }

    /**
     * Delete a footer link without affecting footer settings.
     */
    public function destroyLink(int $id): JsonResponse
    {
        $link = FooterLink::find($id);
        if (!$link) {
            return $this->notFoundResponse('Footer link not found.');
        }

        $link->delete();

        return $this->successResponse(
            null,
            'Footer link deleted successfully.'
        );
    }

    /**
     * Toggle or set the active status of a footer link.
     */
    public function updateLinkStatus(Request $request, int $id): JsonResponse
    {
        $link = FooterLink::find($id);
        if (!$link) {
            return $this->notFoundResponse('Footer link not found.');
        }

        if ($request->has('is_active')) {
            $link->is_active = $request->boolean('is_active');
        } else {
            $link->is_active = !$link->is_active;
        }

        $link->save();

        return $this->successResponse(
            new FooterLinkResource($link),
            'Footer link status updated successfully.'
        );
    }

    /**
     * Reorder footer links in batch.
     */
    public function reorderLinks(ReorderFooterLinksRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            if (!empty($validated['links'])) {
                foreach ($validated['links'] as $item) {
                    FooterLink::where('id', $item['id'])->update([
                        'sort_order' => (int) $item['sort_order'],
                    ]);
                }
            } elseif (!empty($validated['ids'])) {
                foreach ($validated['ids'] as $index => $id) {
                    FooterLink::where('id', $id)->update([
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        });

        // Mass (query-builder) updates bypass Eloquent events, so the static API
        // cache invalidation in AppServiceProvider never fires. Clear it manually.
        $docroot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        if ($docroot !== '') {
            \Illuminate\Support\Facades\File::deleteDirectory(rtrim($docroot, '/') . '/api/apicache');
        }

        $updatedLinks = FooterLink::ordered()->get();

        return $this->successResponse(
            FooterLinkResource::collection($updatedLinks),
            'Footer links reordered successfully.'
        );
    }
}
