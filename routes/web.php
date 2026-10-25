<?php

use App\Http\Controllers\SitemapController;
use App\Services\HeadInjectionService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

// ==========================================
// 🗺️ XML SITEMAP & ROBOTS.TXT
// ==========================================
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::redirect('/sitemap', '/sitemap.xml', 301);
Route::get('/robots.txt', function () {
    $path = public_path('robots.txt');
    if (is_file($path)) {
        return response(File::get($path), 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
    return response("User-agent: *\nAllow: /\nDisallow: /dashboard/\nDisallow: /api/\n\nSitemap: https://sis.iptvpro.org/sitemap.xml\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
});

// ==========================================
// 🌐 CLIENT SPA & HYBRID HEAD INJECTION
// ==========================================
Route::get('/{any?}', function (HeadInjectionService $headService, $any = null) {
    $headers = [
        'Content-Type' => 'text/html; charset=UTF-8',
    ];

    $uri = request()->getRequestUri();
    $path = ltrim(explode('?', $uri)[0], '/');

    // 1. Direct static asset resolution
    if ($path !== '' && is_file(public_path("build/{$path}"))) {
        $filePath = public_path("build/{$path}");
    } elseif ($path !== '' && is_file(public_path($path))) {
        $filePath = public_path($path);
    } else {
        $filePath = null;
    }

    if ($filePath) {
        if (str_ends_with($filePath, '.js')) {
            $headers['Content-Type'] = 'text/javascript';
        } elseif (str_ends_with($filePath, '.css')) {
            $headers['Content-Type'] = 'text/css';
        }
        return response(File::get($filePath))->withHeaders($headers);
    }

    // 2. SPA index.html rendering
    $htmlPath = public_path('build/index.html');
    if (!is_file($htmlPath)) {
        $htmlPath = public_path('index.html');
    }

    if (!is_file($htmlPath)) {
        return response('SiS Application is loading...', 503);
    }

    $html = File::get($htmlPath);
    $userAgent = request()->userAgent();

    // 3. Head Injection for Crawlers and Initial HTML Delivery (Non-Dashboard)
    if (!str_starts_with($path, 'dashboard') || $headService->isCrawler($userAgent)) {
        $metadata = $headService->resolveMetadata($path);
        $html = $headService->injectIntoHtml($html, $metadata);
    }

    return response($html)->withHeaders($headers);
})->where('any', '^(?!(api|sitemap\.xml|robots\.txt)).*$');