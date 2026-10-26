<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SeoSetting;
use App\Models\SolutionPage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SitemapController extends Controller
{
    /**
     * Generate and serve dynamic XML sitemap with caching, disk fallback, and transparent status.
     */
    public function index(): Response
    {
        $status = 'cached';
        $xml = null;

        try {
            $xml = Cache::get('sis_sitemap_xml');
        } catch (\Throwable $e) {
            Log::warning('Sitemap cache retrieval bypassed: ' . $e->getMessage());
        }

        if (empty($xml)) {
            try {
                $xml = $this->generateSitemapXml();
                $status = 'generated';

                // Cache for 1 hour on 100% successful dynamic generation
                try {
                    Cache::put('sis_sitemap_xml', $xml, 3600);
                } catch (\Throwable $ce) {
                    Log::warning('Sitemap cache storage bypassed: ' . $ce->getMessage());
                }

                // Synchronize single canonical physical file in public_path('sitemap.xml')
                $this->syncPhysicalFile($xml);

            } catch (\Throwable $e) {
                Log::error('SitemapController dynamic XML generation failed: ' . $e->getMessage(), [
                    'exception' => $e,
                ]);

                // 1. Attempt to serve the last known good physical file from disk
                $publicPath = public_path('sitemap.xml');
                if (is_file($publicPath) && filesize($publicPath) > 100) {
                    $diskContent = @file_get_contents($publicPath);
                    if (!empty($diskContent) && str_starts_with(trim($diskContent), '<?xml')) {
                        $xml = $diskContent;
                        $status = 'disk-fallback';
                        Log::warning('SitemapController serving last known good disk-cached sitemap.xml.');
                    }
                }

                // 2. If no valid disk file exists, generate minimal emergency fallback
                if (empty($xml)) {
                    $xml = $this->generateFallbackSitemapXml();
                    $status = 'emergency-fallback';
                    Log::error('SitemapController serving minimal emergency fallback sitemap (database unavailable).');
                }
            }
        }

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex', // Prevents indexing the sitemap file itself as a content page
            'X-Sitemap-Status' => $status,
        ]);
    }

    /**
     * Synchronize the single canonical physical file in public_path('sitemap.xml').
     */
    protected function syncPhysicalFile(string $xml): void
    {
        try {
            $publicPath = public_path('sitemap.xml');
            if (!is_file($publicPath) || (time() - filemtime($publicPath) > 1800)) {
                $dir = dirname($publicPath);
                if (is_writable($dir) || (is_file($publicPath) && is_writable($publicPath))) {
                    @file_put_contents($publicPath, $xml, LOCK_EX);
                    @chmod($publicPath, 0644);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('SitemapController physical file sync bypassed: ' . $e->getMessage());
        }
    }

    /**
     * Build the dynamic XML string from database and active content models.
     */
    protected function generateSitemapXml(): string
    {
        $setting = SeoSetting::instance();
        $baseUrl = rtrim($setting->canonical_base_url ?: config('app.url'), '/');
        $defaultNoindex = (bool) $setting->default_noindex;

        $urls = [];

        // 1. Core Static Public Pages
        $staticPages = [
            ['path' => '/', 'priority' => '1.0', 'changefreq' => 'weekly', 'has_bilingual' => true],
            ['path' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly', 'has_bilingual' => false],
            ['path' => '/services', 'priority' => '0.9', 'changefreq' => 'weekly', 'has_bilingual' => false],
            ['path' => '/projects', 'priority' => '0.8', 'changefreq' => 'weekly', 'has_bilingual' => false],
            ['path' => '/use-cases', 'priority' => '0.8', 'changefreq' => 'weekly', 'has_bilingual' => false],
            ['path' => '/partner-ecosystem', 'priority' => '0.7', 'changefreq' => 'monthly', 'has_bilingual' => false],
            ['path' => '/contact', 'priority' => '0.7', 'changefreq' => 'monthly', 'has_bilingual' => false],
        ];

        $nowIso = now()->toIso8601String();

        if (!$defaultNoindex) {
            $suppressedIds = [];
            try {
                $suppressedIds = \App\Models\SeoMetadata::where('seoable_type', 'static_page')
                    ->where('is_noindex', true)
                    ->pluck('seoable_id')
                    ->all();
            } catch (\Throwable $e) {
                Log::error('Sitemap generation static page suppression lookup error: ' . $e->getMessage());
            }

            foreach ($staticPages as $page) {
                try {
                    $staticConfig = \App\Models\StaticPageSeo::getPageByPath($page['path']);
                    if ($staticConfig && in_array($staticConfig['id'], $suppressedIds, true)) {
                        continue;
                    }
                } catch (\Throwable $e) {
                    Log::warning('Sitemap static config resolution error for ' . $page['path'] . ': ' . $e->getMessage());
                }

                $loc = $page['path'] === '/' ? $baseUrl . '/' : $baseUrl . $page['path'];
                $urls[] = [
                    'loc' => $loc,
                    'lastmod' => $nowIso,
                    'changefreq' => $page['changefreq'],
                    'priority' => $page['priority'],
                    'has_bilingual' => $page['has_bilingual'],
                ];
            }
        }

        // 2. Dynamic Active Solution Pages (Only genuinely routed detail pages)
        try {
            $solutionPages = SolutionPage::where('is_active', true)->get();
            foreach ($solutionPages as $sol) {
                if (empty($sol->slug)) continue;

                // Check noindex directive (individual override or site default)
                $seo = method_exists($sol, 'getSeoPayload') ? $sol->getSeoPayload('en') : [];
                $isNoindex = !empty($seo['is_noindex']) || $defaultNoindex;
                if ($isNoindex) {
                    continue;
                }

                $lastMod = $sol->updated_at ? $sol->updated_at->toIso8601String() : $nowIso;
                $urls[] = [
                    'loc' => $baseUrl . '/solution-pages/' . $sol->slug,
                    'lastmod' => $lastMod,
                    'changefreq' => 'weekly',
                    'priority' => '0.9',
                    'has_bilingual' => true,
                ];
            }
        } catch (\Throwable $e) {
            Log::error('Sitemap generation solution query failed: ' . $e->getMessage());
            throw $e;
        }

        return $this->formatXml($urls);
    }

    /**
     * Reliable minimal fallback sitemap XML when runtime database errors occur.
     */
    public function generateFallbackSitemapXml(): string
    {
        $baseUrl = config('app.url');
        $nowIso = now()->toIso8601String();

        $urls = [
            ['loc' => $baseUrl . '/', 'lastmod' => $nowIso, 'changefreq' => 'weekly', 'priority' => '1.0', 'has_bilingual' => true],
            ['loc' => $baseUrl . '/about', 'lastmod' => $nowIso, 'changefreq' => 'monthly', 'priority' => '0.8', 'has_bilingual' => false],
            ['loc' => $baseUrl . '/services', 'lastmod' => $nowIso, 'changefreq' => 'weekly', 'priority' => '0.9', 'has_bilingual' => false],
            ['loc' => $baseUrl . '/projects', 'lastmod' => $nowIso, 'changefreq' => 'weekly', 'priority' => '0.8', 'has_bilingual' => false],
            ['loc' => $baseUrl . '/use-cases', 'lastmod' => $nowIso, 'changefreq' => 'weekly', 'priority' => '0.8', 'has_bilingual' => false],
            ['loc' => $baseUrl . '/partner-ecosystem', 'lastmod' => $nowIso, 'changefreq' => 'monthly', 'priority' => '0.7', 'has_bilingual' => false],
            ['loc' => $baseUrl . '/contact', 'lastmod' => $nowIso, 'changefreq' => 'monthly', 'priority' => '0.7', 'has_bilingual' => false],
        ];

        return $this->formatXml($urls);
    }

    /**
     * Format a structured URL list into an XML sitemap complying with Sitemaps.org 0.9 schema.
     */
    protected function formatXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($urls as $item) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($item['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . $item['lastmod'] . "</lastmod>\n";
            $xml .= "    <changefreq>" . $item['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $item['priority'] . "</priority>\n";
            if (!empty($item['has_bilingual'])) {
                $xml .= '    <xhtml:link rel="alternate" hreflang="en" href="' . htmlspecialchars($item['loc'], ENT_XML1, 'UTF-8') . '" />' . "\n";
                $xml .= '    <xhtml:link rel="alternate" hreflang="ar" href="' . htmlspecialchars($item['loc'], ENT_XML1, 'UTF-8') . '?lang=ar" />' . "\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>\n";

        return $xml;
    }
}

