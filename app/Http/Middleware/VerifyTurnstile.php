<?php

namespace App\Http\Middleware;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies a Cloudflare Turnstile token when protection is enabled.
 * No-op (pass-through) when Turnstile is disabled, so it is safe to attach
 * to routes before any keys are configured.
 */
class VerifyTurnstile
{
    public function __construct(private TurnstileService $turnstile) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->turnstile->enabled()) {
            $token = $request->input('turnstile_token')
                ?: $request->input('cf-turnstile-response')
                ?: $request->header('CF-Turnstile-Response');

            if (! $this->turnstile->verify($token, $request->ip())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Captcha verification failed. Please try again.',
                ], 422);
            }
        }

        return $next($request);
    }
}
