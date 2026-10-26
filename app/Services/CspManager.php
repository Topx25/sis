<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\File;

/**
 * Keeps the public docroot .htaccess Content-Security-Policy in sync with the
 * admin-configured integration domains. No provider is hard-coded: the admin
 * lists the domains their chat/analytics code needs (Settings → Integrations),
 * and the CSP is rebuilt from that list. Turnstile's domain is always included
 * because it is a built-in feature.
 */
class CspManager
{
    /** Always-allowed domains for built-in features. */
    private const BASE_SCRIPT = ['https://challenges.cloudflare.com'];
    private const BASE_FRAME  = ['https://challenges.cloudflare.com'];

    /**
     * Rebuild the CSP header in the docroot .htaccess from current settings.
     * Safe: validates input, backs up once, and only replaces the CSP line.
     */
    public static function rebuild(): bool
    {
        $path = self::htaccessPath();
        if (! $path || ! File::exists($path) || ! File::isWritable($path)) {
            return false;
        }

        $enabled = AppSetting::getValue('integrations', 'enabled', '0') === '1';
        $domains = $enabled ? self::parseDomains(AppSetting::getValue('integrations', 'allowed_domains', '')) : [];

        $scriptSrc  = array_merge(["'self'"], self::BASE_SCRIPT, self::httpsList($domains));
        $frameSrc   = array_merge(["'self'"], self::BASE_FRAME, self::httpsList($domains));
        $imgSrc     = array_merge(["'self'", 'data:'], self::httpsList($domains));
        $connectSrc = array_merge(["'self'"], self::httpsList($domains), self::wssList($domains));

        $csp = implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            'script-src ' . implode(' ', self::uniq($scriptSrc)),
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            'img-src ' . implode(' ', self::uniq($imgSrc)),
            "media-src 'self'",
            'connect-src ' . implode(' ', self::uniq($connectSrc)),
            'frame-src ' . implode(' ', self::uniq($frameSrc)),
            'upgrade-insecure-requests',
        ]);

        $content = File::get($path);
        $pattern = '/Header always set Content-Security-Policy "[^"]*"/';
        $replacement = 'Header always set Content-Security-Policy "' . $csp . '"';

        if (! preg_match($pattern, $content)) {
            return false;
        }

        // One-time safety backup, then write.
        $backup = $path . '.csp-bak';
        if (! File::exists($backup)) {
            File::copy($path, $backup);
        }

        File::put($path, preg_replace($pattern, $replacement, $content, 1));

        return true;
    }

    private static function htaccessPath(): ?string
    {
        $docroot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        if ($docroot === '') {
            return null;
        }

        return rtrim($docroot, '/') . '/.htaccess';
    }

    /**
     * Accept a space/comma/newline separated list; keep only safe domain tokens.
     * Allows bare domains and a single leading wildcard label, e.g. *.fariek.com.
     */
    private static function parseDomains(?string $raw): array
    {
        $out = [];
        foreach (preg_split('/[\s,]+/', (string) $raw) as $token) {
            $token = trim($token);
            if ($token === '') {
                continue;
            }
            // strip any scheme the admin may have typed
            $token = preg_replace('#^[a-z]+://#i', '', $token);
            $token = rtrim($token, '/');
            if (preg_match('/^(\*\.)?[A-Za-z0-9][A-Za-z0-9.\-]*\.[A-Za-z]{2,}$/', $token)) {
                $out[] = $token;
            }
        }

        return array_values(array_unique($out));
    }

    private static function httpsList(array $domains): array
    {
        return array_map(fn ($d) => 'https://' . $d, $domains);
    }

    private static function wssList(array $domains): array
    {
        return array_map(fn ($d) => 'wss://' . $d, $domains);
    }

    private static function uniq(array $items): array
    {
        return array_values(array_unique(array_filter($items, fn ($i) => $i !== '')));
    }
}
