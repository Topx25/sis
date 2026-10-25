<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales for the API.
     */
    protected array $supportedLocales = ['en', 'ar'];

    /**
     * Default locale when none is specified or unsupported.
     */
    protected string $defaultLocale = 'en';

    /**
     * Handle an incoming request.
     *
     * Reads the Accept-Language header and sets the application locale.
     * Supports: Accept-Language: ar | en
     * Falls back to 'en' if locale is missing or unsupported.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);

        $response = $next($request);

        // Attach Content-Language header to indicate response language
        $response->headers->set('Content-Language', $locale);

        return $response;
    }

    /**
     * Resolve the locale from the Accept-Language header.
     */
    protected function resolveLocale(Request $request): string
    {
        $header = $request->header('Accept-Language');

        if (!$header) {
            return $this->defaultLocale;
        }

        // Extract primary language tag (e.g. "ar-EG" => "ar", "en-US" => "en")
        $primaryLocale = strtolower(trim(explode(',', $header)[0]));
        $primaryLocale = strtolower(trim(explode('-', $primaryLocale)[0]));

        if (in_array($primaryLocale, $this->supportedLocales, true)) {
            return $primaryLocale;
        }

        return $this->defaultLocale;
    }
}
