<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactPageResource;
use App\Models\ContactPage;
use App\Traits\ApiResponse;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminContactPageController extends Controller
{
    use ApiResponse;

    public function show(): JsonResponse
    {
        $page = ContactPage::first();
        if (!$page) {
            return $this->successResponse(null, 'Contact page data not found.');
        }
        return $this->successResponse(new ContactPageResource($page), 'Contact page retrieved successfully.');
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'website' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'emails' => 'nullable|array',
            'emails.*' => 'string|max:255',
            'phones' => 'nullable|array',
            'phones.*' => 'string|max:255',
            'cta_title' => 'nullable|array',
            'cta_description' => 'nullable|array',
            'cta_link_text' => 'nullable|array',
            'cta_link' => 'nullable|string|max:255',
        ]);

        $page = ContactPage::first();

        // Check or auto-provision the emails column if missing
        $hasEmailsColumn = false;
        try {
            if (Schema::hasTable('contact_pages')) {
                if (!Schema::hasColumn('contact_pages', 'emails')) {
                    Schema::table('contact_pages', function (Blueprint $table) {
                        $table->json('emails')->nullable()->after('email');
                    });
                }
                $hasEmailsColumn = Schema::hasColumn('contact_pages', 'emails');
            }
        } catch (\Throwable $e) {
            $hasEmailsColumn = false;
        }

        // Clean and prepare emails list
        $emailsList = [];
        if (!empty($validated['emails']) && is_array($validated['emails'])) {
            $emailsList = array_values(array_filter(array_map('trim', $validated['emails'])));
        } elseif (!empty($validated['email'])) {
            $emailsList = array_values(array_filter(array_map('trim', preg_split('/[,\n;]+/', (string) $validated['email']))));
        }

        if ($hasEmailsColumn) {
            $validated['emails'] = $emailsList;
            $validated['email'] = $emailsList[0] ?? null;
        } else {
            // Safe fallback if column doesn't exist: store all emails joined by comma
            $validated['email'] = !empty($emailsList) ? implode(', ', $emailsList) : null;
            unset($validated['emails']);
        }
        
        if ($page) {
            $page->update($validated);
        } else {
            $page = ContactPage::create($validated);
        }

        return $this->successResponse(new ContactPageResource($page), 'Contact page updated successfully.');
    }
    
    public function destroy(): JsonResponse
    {
        $page = ContactPage::first();
        if ($page) {
            $page->delete();
        }
        return $this->successResponse(null, 'Contact page data cleared successfully.');
    }
}
