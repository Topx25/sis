<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExtractAdminToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $value = $request->headers->get('Authorization')
            ?: $request->server->get('HTTP_AUTHORIZATION')
            ?: $request->server->get('REDIRECT_HTTP_AUTHORIZATION')
            ?: $request->headers->get('X-Authorization');

        if (! $value && $request->headers->has('X-Auth-Token')) {
            $value = 'Bearer '.$request->headers->get('X-Auth-Token');
        }

        if (! $value && $request->headers->has('X-Auth')) {
            $value = 'Bearer '.$request->headers->get('X-Auth');
        }

        if (! $value && $request->cookies->has('drosat_token')) {
            $value = 'Bearer '.$request->cookie('drosat_token');
        }

        if (is_string($value) && trim($value) !== '') {
            $value = trim($value);

            if (preg_match('/^Bearer\s+(.+)$/i', $value, $matches)) {
                $value = 'Bearer '.$matches[1];
            } elseif (! preg_match('/^[A-Za-z]+\s+.+$/', $value)) {
                $value = 'Bearer '.$value;
            }

            $request->headers->set('Authorization', $value);
            $request->server->set('HTTP_AUTHORIZATION', $value);
        }

        return $next($request);
    }
}
