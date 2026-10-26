<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Services\MailConfigService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

/**
 * Admin settings (role:admin): Cloudflare Turnstile + email (SMTP).
 * Secrets are stored encrypted and never returned in clear — the client
 * sees a mask ("********") and only overwrites a secret when it sends a new one.
 */
class AdminSettingsController extends Controller
{
    use ApiResponse;

    private const MASK = '********';

    // ---- Turnstile ----

    public function turnstile(): JsonResponse
    {
        return $this->successResponse($this->turnstilePayload(), 'Turnstile settings retrieved successfully.');
    }

    public function updateTurnstile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'enabled'    => ['required', 'boolean'],
            'site_key'   => ['nullable', 'string', 'max:255'],
            'secret_key' => ['nullable', 'string', 'max:255'],
        ]);

        AppSetting::setValue('turnstile', 'enabled', $data['enabled'] ? '1' : '0');
        AppSetting::setValue('turnstile', 'site_key', $data['site_key'] ?? '');

        // Only overwrite the secret when a real new value is provided (not the mask).
        if (! empty($data['secret_key']) && $data['secret_key'] !== self::MASK) {
            AppSetting::setValue('turnstile', 'secret_key', $data['secret_key'], true);
        }

        return $this->successResponse($this->turnstilePayload(), 'Turnstile settings saved successfully.');
    }

    private function turnstilePayload(): array
    {
        return [
            'enabled'    => AppSetting::getValue('turnstile', 'enabled', '0') === '1',
            'site_key'   => AppSetting::getValue('turnstile', 'site_key', ''),
            'secret_key' => ! empty(AppSetting::getValue('turnstile', 'secret_key')) ? self::MASK : '',
            'has_secret' => ! empty(AppSetting::getValue('turnstile', 'secret_key')),
        ];
    }

    // ---- Email (SMTP) ----

    public function mail(): JsonResponse
    {
        return $this->successResponse($this->mailPayload(), 'Mail settings retrieved successfully.');
    }

    public function updateMail(Request $request): JsonResponse
    {
        $data = $request->validate([
            'enabled'      => ['required', 'boolean'],
            'mailer'       => ['nullable', Rule::in(['smtp', 'sendmail'])],
            'host'         => ['nullable', 'string', 'max:255'],
            'port'         => ['nullable', 'integer', 'min:1', 'max:65535'],
            'username'     => ['nullable', 'string', 'max:255'],
            'password'     => ['nullable', 'string', 'max:255'],
            'encryption'   => ['nullable', Rule::in(['tls', 'ssl', 'none', ''])],
            'from_address' => ['nullable', 'email', 'max:255'],
            'from_name'    => ['nullable', 'string', 'max:255'],
            'to_address'   => ['nullable', 'email', 'max:255'],
        ]);

        AppSetting::setValue('mail', 'enabled', $data['enabled'] ? '1' : '0');
        foreach (['mailer', 'host', 'port', 'username', 'encryption', 'from_address', 'from_name', 'to_address'] as $key) {
            AppSetting::setValue('mail', $key, (string) ($data[$key] ?? ''));
        }
        if (! empty($data['password']) && $data['password'] !== self::MASK) {
            AppSetting::setValue('mail', 'password', $data['password'], true);
        }

        return $this->successResponse($this->mailPayload(), 'Mail settings saved successfully.');
    }

    public function testMail(Request $request, MailConfigService $mailConfig): JsonResponse
    {
        $to = $request->validate(['to' => ['required', 'email']])['to'];

        try {
            $mailConfig->apply();
            Mail::raw('This is a test email from the SiS admin panel. Your mail settings are working.', function ($message) use ($to) {
                $message->to($to)->subject('SiS — test email');
            });

            return $this->successResponse(null, 'Test email sent to ' . $to . '.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to send test email: ' . $e->getMessage(), 422);
        }
    }

    private function mailPayload(): array
    {
        $m = AppSetting::group('mail', true); // secrets masked

        return [
            'enabled'      => ($m['enabled'] ?? '0') === '1',
            'mailer'       => $m['mailer'] ?? 'smtp',
            'host'         => $m['host'] ?? '',
            'port'         => $m['port'] ?? '587',
            'username'     => $m['username'] ?? '',
            'password'     => $m['password'] ?? '',
            'has_password' => ! empty(AppSetting::getValue('mail', 'password')),
            'encryption'   => $m['encryption'] ?? 'tls',
            'from_address' => $m['from_address'] ?? '',
            'from_name'    => $m['from_name'] ?? '',
            'to_address'   => $m['to_address'] ?? '',
        ];
    }
}
