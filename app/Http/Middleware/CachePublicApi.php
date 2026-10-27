<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Writes successful public (non-admin, unauthenticated) GET API responses to a
 * static JSON file inside the live document root, so the web server can serve
 * repeat requests directly without booting the framework.
 *
 * Fail-safe: if a cache file is missing the request simply falls through to the
 * application as usual; removing the matching rewrite rule disables it entirely.
 * Invalidation is handled centrally whenever any content model is written.
 */
class CachePublicApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldCache($request, $response)) {
            return $response;
        }

        $file = $this->cacheFile($request);

        if ($file !== null) {
            $dir = dirname($file);

            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            // Atomic write so a concurrent read never sees a half-written file.
            $tmp = $file . '.' . bin2hex(random_bytes(4)) . '.tmp';

            if (@file_put_contents($tmp, $response->getContent()) !== false) {
                @rename($tmp, $file);
            } else {
                @unlink($tmp);
            }
        }

        return $response;
    }

    /**
     * Only cache plain public reads: GET, no query string, 200, JSON body,
     * never admin routes and never authenticated requests.
     */
    private function shouldCache(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        // Allow only simple, filesystem-safe query strings (e.g. per_page=100);
        // anything else stays dynamic.
        $qs = $request->getQueryString();

        if ($qs !== null && ! preg_match('#^[A-Za-z0-9=_&-]+$#', $qs)) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        if ($request->user() !== null) {
            return false;
        }

        $path = $request->path();

        if (! str_starts_with($path, 'v1/') || str_starts_with($path, 'v1/admin')) {
            return false;
        }

        // Host-varying SEO endpoints must stay fresh per domain (canonical/base URL
        // depends on the current host); never share them across domains via cache.
        if ($path === 'v1/seo-settings') {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type');

        return str_contains($contentType, 'application/json');
    }

    /**
     * Resolve the static cache target inside the actual served document root,
     * mirroring the request path: <docroot>/api/apicache/<path>.json
     * Returns null when the document root is unknown (e.g. CLI).
     */
    private function cacheFile(Request $request): ?string
    {
        $docroot = $request->server('DOCUMENT_ROOT');

        if (! is_string($docroot) || $docroot === '') {
            return null;
        }

        $path = $request->path(); // e.g. v1/hero

        // Guard against path traversal; allow only safe URL characters.
        if (! preg_match('#^v1/[A-Za-z0-9/_-]+$#', $path)) {
            return null;
        }

        // Keyed by query string when present (already validated safe), so e.g.
        // projects?per_page=100 caches to v1/projects__per_page=100.json
        $qs = $request->getQueryString();
        $suffix = ($qs !== null && $qs !== '') ? '__' . $qs : '';

        return rtrim($docroot, '/') . '/api/apicache/' . $path . $suffix . '.json';
    }
}
