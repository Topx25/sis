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
        // Apache may strip the Authorization header; the SPA sends the token via
        // X-Authorization as a fallback. No cookie-based auth is accepted (avoids CSRF).
        $value = $request->headers->get('Authorization')
            ?: $request->server->get('HTTP_AUTHORIZATION')
            ?: $request->server->get('REDIRECT_HTTP_AUTHORIZATION')
            ?: $request->headers->get('X-Authorization');

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
