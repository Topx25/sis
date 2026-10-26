<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict a route to users whose role is in the given list.
 * Usage: ->middleware('role:admin')  (after auth:sanctum)
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ($roles && ! in_array($user->role, $roles, true))) {
            return response()->json([
                'success' => false,
                'message' => 'This action requires elevated privileges.',
            ], 403);
        }

        return $next($request);
    }
}
