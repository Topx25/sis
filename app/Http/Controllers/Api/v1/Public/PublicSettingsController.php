<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * Exposes only the non-secret settings the public SPA needs
 * (e.g. the Turnstile site key). Never exposes secrets.
 */
class PublicSettingsController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $siteKey = AppSetting::getValue('turnstile', 'site_key', '');
        $turnstileOn = AppSetting::getValue('turnstile', 'enabled', '0') === '1'
            && ! empty($siteKey)
            && ! empty(AppSetting::getValue('turnstile', 'secret_key'));

        $integrations = AppSetting::group('integrations');
        $integrationsOn = ($integrations['enabled'] ?? '0') === '1';

        return $this->successResponse([
            'turnstile' => [
                'enabled'  => $turnstileOn,
                'site_key' => $turnstileOn ? $siteKey : '',
            ],
            'integrations' => [
                'enabled'   => $integrationsOn,
                'head_code' => $integrationsOn ? ($integrations['head_code'] ?? '') : '',
                'body_code' => $integrationsOn ? ($integrations['body_code'] ?? '') : '',
                'chat_code' => $integrationsOn ? ($integrations['chat_code'] ?? '') : '',
            ],
        ], 'Site configuration retrieved successfully.');
    }
}
