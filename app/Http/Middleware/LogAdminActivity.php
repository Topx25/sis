<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records a trail of state-changing admin actions (who / what / when / from where).
 * Only non-GET admin requests that completed without a client/server error are
 * logged. Logging never breaks the request.
 */
class LogAdminActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            ! $request->isMethod('GET')
            && $request->is('v1/admin/*')
            && $response->getStatusCode() < 400
        ) {
            try {
                $user = $request->user();
                ActivityLog::create([
                    'user_id'   => $user?->id,
                    'user_name' => $user?->name,
                    'method'    => $request->method(),
                    'path'      => $request->path(),
                    'ip'        => $request->ip(),
                    'status'    => $response->getStatusCode(),
                ]);
            } catch (\Throwable $e) {
                // Never let auditing interfere with the actual request.
            }
        }

        return $response;
    }
}
