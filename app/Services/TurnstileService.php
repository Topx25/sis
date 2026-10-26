<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;

/**
 * Cloudflare Turnstile verification. Enforcement is settings-driven and
 * off unless it is enabled AND a secret key is configured.
 */
class TurnstileService
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function enabled(): bool
    {
        return AppSetting::getValue('turnstile', 'enabled', '0') === '1'
            && ! empty(AppSetting::getValue('turnstile', 'secret_key'));
    }

    public function verify(?string $token, ?string $ip = null): bool
    {
        // When protection is off, every request passes.
        if (! $this->enabled()) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(8)->post(self::VERIFY_URL, array_filter([
                'secret'   => AppSetting::getValue('turnstile', 'secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ]));

            return (bool) ($response->json('success') ?? false);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
