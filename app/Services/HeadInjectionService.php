<?php

namespace App\Services;

use App\Models\Project;
use App\Models\SeoSetting;
use App\Models\SolutionPage;
use Illuminate\Support\Facades\Log;

class HeadInjectionService
{
    /**
     * Regex pattern matching social media bots and search engine preview crawlers.
     */
    protected const CRAWLER_USER_AGENTS = '/facebookexternalhit|LinkedInBot|Twitterbot|WhatsApp|Slackbot|TelegramBot|Discordbot|SkypeUriPreview|Google-InspectionTool|Googlebot|bingbot|Baiduspider|yandex|duckduckbot|slurp|Pinterest|Applebot/i';

    /**
     * Determine if the incoming User-Agent belongs to a social media or preview crawler.
     */
    public function isCrawler(?string $userAgent): bool
    {
        if (empty($userAgent)) {
            return false;
        }

        return (bool) preg_match(self::CRAWLER_USER_AGENTS, $userAgent);
    }

    /**
     * Resolve metadata dictionary for a given URI path.
     */
    public function resolveMetadata(string $path, ?string $locale = 'en'): array
    {
        $setting = SeoSetting::instance();
        $baseUrl = rtrim($setting->canonical_base_url ?: ('https://' . request()->getHttpHost()), '/');
        $siteName = $setting->getTranslation('site_name', $locale) ?: 'SiS - Superior Integrated Solutions';
        $titleSuffix = $setting->getTranslation('title_suffix', $locale) ?: ' | SiS';
        $defaultDesc = $setting->getTranslation('default_meta_description', $locale)
            ?: 'Superior Integrated Solutions (SiS) bridges OT and IT across the MENA region with industrial automation, IIoT, and AI.';

        $defaultImage = $setting->default_og_image
            ? (str_starts_with($setting->default_og_image, 'http') ? $setting->default_og_image : $baseUrl . '/' . ltrim($setting->default_og_image, '/'))
            : $baseUrl . '/sis-logo.png';

        $cleanPath = trim(explode('?', explode('#', $path)[0])[0], '/');
        $canonicalUrl = $cleanPath === '' ? $baseUrl : $baseUrl . '/' . $cleanPath;
        $robots = $setting->default_noindex ? 'noindex, nofollow' : 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';

        $defaultKeywords = method_exists($setting, 'getTranslation')
            ? ($setting->getTranslation('default_meta_keywords', $locale, false) ?: $setting->getTranslation('default_meta_keywords', 'en', false) ?: '')
            : '';

        $metadata = [
            'title' => $siteName,
            'description' => $defaultDesc,
            'meta_keywords' => $defaultKeywords,
            'image' => $defaultImage,
            'canonical' => $canonicalUrl,
            'robots' => $robots,
            'og_type' => 'website',
            'is_home' => false,
            'structured_data' => null,
            'google_verification' => $setting->google_site_verification,
            'bing_verification' => $setting->bing_site_verification,
        ];

        // 1. Home Route
        if ($cleanPath === '') {
            $metadata['is_home'] = true;
            $homeResolved = \App\Models\StaticPageSeo::getResolvedPayload('home', $locale);
            if ($homeResolved) {
                if (!empty($homeResolved['meta_title'])) {
                    $metadata['title'] = $homeResolved['meta_title'];
                }
                if (!empty($homeResolved['meta_description'])) {
                    $metadata['description'] = mb_substr(strip_tags($homeResolved['meta_description']), 0, 160);
                }
                if (!empty($homeResolved['og_image'])) {
                    $metadata['image'] = $homeResolved['og_image'];
                }
                if (!empty($homeResolved['canonical_url'])) {
                    $metadata['canonical'] = $homeResolved['canonical_url'];
                }
                if (!empty($homeResolved['is_noindex'])) {
                    $metadata['robots'] = 'noindex, nofollow';
                }
            }
            return $metadata;
        }

        // 2. Private Dashboard Routes
        if (str_starts_with($cleanPath, 'dashboard')) {
            $metadata['title'] = 'SiS Admin Dashboard';
            $metadata['robots'] = 'noindex, nofollow';
            return $metadata;
        }

        // 3. Dynamic Solution Detail Pages
        if (preg_match('/^solution-pages\/([^\/]+)$/', $cleanPath, $matches)) {
            $slug = $matches[1];
            try {
                /** @var SolutionPage|null $solution */
                $solution = SolutionPage::where('slug', $slug)->first();
                if ($solution) {
                    $seo = method_exists($solution, 'getSeoPayload')
                        ? $solution->getSeoPayload($locale)
                        : [];

                    $solTitle = $seo['meta_title'] ?? ($solution->title ? $solution->title . $titleSuffix : null);
                    $solDesc = $seo['meta_description'] ?? $solution->subtitle ?? $solution->description ?? $defaultDesc;
                    $solImage = $seo['og_image'] ?? $solution->hero_image_url ?? $solution->hero_image ?? $defaultImage;

                    if ($solTitle) $metadata['title'] = $solTitle;
                    if ($solDesc) $metadata['description'] = mb_substr(strip_tags($solDesc), 0, 160);
                    if ($solImage) {
                        $metadata['image'] = str_starts_with($solImage, 'http') ? $solImage : $baseUrl . '/' . ltrim($solImage, '/');
                    }
                    if (!empty($seo['canonical_url'])) {
                        $metadata['canonical'] = $seo['canonical_url'];
                    }
                    if (!empty($seo['is_noindex'])) {
                        $metadata['robots'] = 'noindex, nofollow';
                    }
                    $metadata['og_type'] = 'article';
                    return $metadata;
                }
            } catch (\Throwable $e) {
                Log::warning('Head injection solution query fallback: ' . $e->getMessage());
            }
        }

        // 4. Dynamic Project / Use Case Detail Pages
        if (preg_match('/^projects\/([^\/]+)$/', $cleanPath, $matches)) {
            $slugOrId = $matches[1];
            try {
                /** @var Project|null $project */
                $project = Project::where('slug', $slugOrId)
                    ->orWhere('id', is_numeric($slugOrId) ? (int)$slugOrId : 0)
                    ->first();

                if ($project) {
                    $seo = method_exists($project, 'getSeoPayload')
                        ? $project->getSeoPayload($locale)
                        : [];

                    $projTitle = $seo['meta_title'] ?? ($project->title ? $project->title . $titleSuffix : null);
                    $projDesc = $seo['meta_description'] ?? $project->summary ?? $project->description ?? $defaultDesc;
                    $projImage = $seo['og_image'] ?? $project->cover_image ?? $defaultImage;

                    if ($projTitle) $metadata['title'] = $projTitle;
                    if ($projDesc) $metadata['description'] = mb_substr(strip_tags($projDesc), 0, 160);
                    if ($projImage) {
                        $metadata['image'] = str_starts_with($projImage, 'http') ? $projImage : $baseUrl . '/' . ltrim($projImage, '/');
                    }
                    if (!empty($seo['canonical_url'])) {
                        $metadata['canonical'] = $seo['canonical_url'];
                    }
                    if (!empty($seo['is_noindex'])) {
                        $metadata['robots'] = 'noindex, nofollow';
                    }
                    $metadata['og_type'] = 'article';
                    return $metadata;
                }
            } catch (\Throwable $e) {
                Log::warning('Head injection project query fallback: ' . $e->getMessage());
            }
        }

        // 5. Core Static Public Pages & Home Route
        $staticPage = \App\Models\StaticPageSeo::getPageByPath($cleanPath === '' ? '/' : '/' . $cleanPath);
        if ($staticPage) {
            if ($cleanPath === '') {
                $metadata['is_home'] = true;
            }

            $resolved = \App\Models\StaticPageSeo::getResolvedPayload($staticPage['key'], $locale);
            if ($resolved) {
                if (!empty($resolved['meta_title'])) {
                    $metadata['title'] = $resolved['meta_title'];
                }
                if (!empty($resolved['meta_description'])) {
                    $metadata['description'] = mb_substr(strip_tags($resolved['meta_description']), 0, 160);
                }
                if (!empty($resolved['og_image'])) {
                    $metadata['image'] = $resolved['og_image'];
                }
                if (!empty($resolved['canonical_url'])) {
                    $metadata['canonical'] = $resolved['canonical_url'];
                }
                if (!empty($resolved['is_noindex'])) {
                    $metadata['robots'] = 'noindex, nofollow';
                }
            }

            return $metadata;
        }

        return $metadata;
    }

    /**
     * Inject metadata tags directly into an HTML string.
     */
    public function injectIntoHtml(string $html, array $metadata): string
    {
        $escapedTitle = htmlspecialchars($metadata['title'] ?? 'SiS', ENT_QUOTES, 'UTF-8');
        $escapedDesc = htmlspecialchars($metadata['description'] ?? '', ENT_QUOTES, 'UTF-8');
        $canonical = htmlspecialchars($metadata['canonical'] ?? '', ENT_QUOTES, 'UTF-8');
        $image = htmlspecialchars($metadata['image'] ?? '', ENT_QUOTES, 'UTF-8');
        $robots = htmlspecialchars($metadata['robots'] ?? 'index, follow', ENT_QUOTES, 'UTF-8');
        $ogType = htmlspecialchars($metadata['og_type'] ?? 'website', ENT_QUOTES, 'UTF-8');

        // Replace <title>
        $html = preg_replace('/<title>.*?<\/title>/i', "<title>{$escapedTitle}</title>", $html, 1);

        // Replace <meta name="description" ...>
        $html = preg_replace('/<meta\s+name=["\']description["\']\s+content=["\'].*?["\']\s*\/?>/i', "<meta name=\"description\" content=\"{$escapedDesc}\" />", $html, 1);

        // Replace or inject <meta name="keywords" ...>
        if (!empty($metadata['meta_keywords'])) {
            $escapedKeywords = htmlspecialchars($metadata['meta_keywords'], ENT_QUOTES, 'UTF-8');
            if (preg_match('/<meta\s+name=["\']keywords["\']\s+content=["\'].*?["\']\s*\/?>/i', $html)) {
                $html = preg_replace('/<meta\s+name=["\']keywords["\']\s+content=["\'].*?["\']\s*\/?>/i', "<meta name=\"keywords\" content=\"{$escapedKeywords}\" />", $html, 1);
            } else {
                $html = preg_replace('/(<meta\s+name=["\']description["\'][^>]*>)/i', "$1\n    <meta name=\"keywords\" content=\"{$escapedKeywords}\" />", $html, 1);
            }
        }

        // Replace <meta name="robots" ...>
        $html = preg_replace('/<meta\s+name=["\']robots["\']\s+content=["\'].*?["\']\s*\/?>/i', "<meta name=\"robots\" content=\"{$robots}\" />", $html, 1);

        // Replace <link rel="canonical" ...>
        $html = preg_replace('/<link\s+rel=["\']canonical["\']\s+href=["\'].*?["\'].*?\/?>/i', "<link rel=\"canonical\" href=\"{$canonical}\" />", $html, 1);

        // Replace Open Graph Tags
        $html = preg_replace('/<meta\s+property=["\']og:title["\']\s+content=["\'].*?["\']\s*\/?>/i', "<meta property=\"og:title\" content=\"{$escapedTitle}\" />", $html, 1);
        $html = preg_replace('/<meta\s+property=["\']og:description["\']\s+content=["\'].*?["\']\s*\/?>/i', "<meta property=\"og:description\" content=\"{$escapedDesc}\" />", $html, 1);
        $html = preg_replace('/<meta\s+property=["\']og:image["\']\s+content=["\'].*?["\'].*?\/?>/i', "<meta property=\"og:image\" content=\"{$image}\" />", $html, 1);
        $html = preg_replace('/<meta\s+property=["\']og:url["\']\s+content=["\'].*?["\'].*?\/?>/i', "<meta property=\"og:url\" content=\"{$canonical}\" />", $html, 1);
        $html = preg_replace('/<meta\s+property=["\']og:type["\']\s+content=["\'].*?["\']\s*\/?>/i', "<meta property=\"og:type\" content=\"{$ogType}\" />", $html, 1);

        // Replace Twitter Tags
        $html = preg_replace('/<meta\s+name=["\']twitter:title["\']\s+content=["\'].*?["\']\s*\/?>/i', "<meta name=\"twitter:title\" content=\"{$escapedTitle}\" />", $html, 1);
        $html = preg_replace('/<meta\s+name=["\']twitter:description["\']\s+content=["\'].*?["\']\s*\/?>/i', "<meta name=\"twitter:description\" content=\"{$escapedDesc}\" />", $html, 1);
        $html = preg_replace('/<meta\s+name=["\']twitter:image["\']\s+content=["\'].*?["\'].*?\/?>/i', "<meta name=\"twitter:image\" content=\"{$image}\" />", $html, 1);

        // Inject Search Engine Verification tags on homepage
        if (!empty($metadata['is_home'])) {
            $extraHead = '';
            if (!empty($metadata['google_verification'])) {
                $gToken = htmlspecialchars($metadata['google_verification'], ENT_QUOTES, 'UTF-8');
                $extraHead .= "\n    <meta name=\"google-site-verification\" content=\"{$gToken}\" />";
            }
            if (!empty($metadata['bing_verification'])) {
                $bToken = htmlspecialchars($metadata['bing_verification'], ENT_QUOTES, 'UTF-8');
                $extraHead .= "\n    <meta name=\"msvalidate.01\" content=\"{$bToken}\" />";
            }
            if ($extraHead !== '') {
                $html = str_replace('</head>', $extraHead . "\n  </head>", $html);
            }
        }

        return $html;
    }
}
