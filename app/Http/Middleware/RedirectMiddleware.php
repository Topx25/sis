<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedirectMiddleware
{
    /**
     * Handle an incoming request and issue 301/302 redirects for altered slugs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();

        // Skip internal/system prefixes
        if (
            str_starts_with($path, 'api') ||
            str_starts_with($path, 'build') ||
            str_starts_with($path, 'storage') ||
            str_starts_with($path, 'dashboard') ||
            $path === 'sitemap.xml' ||
            $path === 'robots.txt'
        ) {
            return $next($request);
        }

        try {
            $normalizedPath = '/' . ltrim($path, '/');

            /** @var Redirect|null $redirect */
            $redirect = Redirect::where('is_active', true)
                ->where(function ($q) use ($normalizedPath) {
                    $q->where('source_path', $normalizedPath)
                      ->orWhere('source_path', ltrim($normalizedPath, '/'));
                })
                ->first();

            if ($redirect && !empty($redirect->target_path)) {
                $target = $redirect->target_path;

                // Avoid infinite redirect loop
                if ($target === $normalizedPath || $target === ltrim($normalizedPath, '/')) {
                    return $next($request);
                }

                // Increment hit telemetry safely without blocking redirect execution
                try {
                    $redirect->increment('hit_count', 1, ['last_hit_at' => now()]);
                } catch (\Throwable $te) {
                    // Ignore telemetry failures if column/table is unmigrated
                }

                $status = (int) ($redirect->status_code ?: 301);
                $destination = str_starts_with($target, 'http') ? $target : url($target);

                return redirect()->to($destination, $status);
            }
        } catch (\Throwable $e) {
            // Failsafe: if the redirects table has not yet been migrated, do not disrupt traffic
            Log::debug('RedirectMiddleware lookup bypassed: ' . $e->getMessage());
        }

        return $next($request);
    }
}
