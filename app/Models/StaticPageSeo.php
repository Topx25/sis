<?php

namespace App\Models;

use App\Support\CacheManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StaticPageSeo
{
    /**
     * Complete registry of all public static pages across the SiS website.
     * Mapped to stable integer IDs within the existing `seo_metadata` table.
     */
    public const PAGES = [
        'home' => [
            'id' => 1,
            'key' => 'home',
            'path' => '/',
            'name' => 'Home Page',
            'schema_type' => 'Organization',
            'default_title' => [
                'en' => 'SiS - Superior Integrated Solutions',
                'ar' => 'سوبيريور للحلول المتكاملة (SiS)',
            ],
            'default_description' => [
                'en' => 'Superior Integrated Solutions (SiS) bridges OT and IT across the MENA region with industrial automation, IIoT, and AI.',
                'ar' => 'حلول سوبيريور المتكاملة (SiS) تربط بين التكنولوجيا التشغيلية وتكنولوجيا المعلومات في منطقة الشرق الأوسط وشمال أفريقيا من خلال الأتمتة الصناعية والتحول الرقمي.',
            ],
        ],
        'about' => [
            'id' => 2,
            'key' => 'about',
            'path' => '/about',
            'name' => 'About Us',
            'schema_type' => 'AboutPage',
            'default_title' => [
                'en' => 'About Us | SiS',
                'ar' => 'من نحن | سوبيريور للحلول المتكاملة',
            ],
            'default_description' => [
                'en' => 'Discover SiS - our mission, engineering pillars, industrial automation heritage, and leadership bridging operational technology with enterprise digital solutions.',
                'ar' => 'اكتشف سوبيريور للحلول المتكاملة - رسالتنا وركائزنا الهندسية وتاريخنا في الأتمتة الصناعية وربط التكنولوجيا التشغيلية بحلول المؤسسات الرقمية.',
            ],
        ],
        'services' => [
            'id' => 3,
            'key' => 'services',
            'path' => '/services',
            'name' => 'Services Catalog',
            'schema_type' => 'CollectionPage',
            'default_title' => [
                'en' => 'Services & Solutions | SiS',
                'ar' => 'الخدمات والحلول | سوبيريور للحلول المتكاملة',
            ],
            'default_description' => [
                'en' => 'Explore turnkey industrial engineering and automation services delivered by SiS, including DCS migrations, cybersecurity, and systems integration.',
                'ar' => 'استكشف خدمات الهندسة الصناعية والأتمتة المتكاملة المقدمة من SiS، بما في ذلك ترقية أنظمة DCS والأمن السيبراني وتكامل الأنظمة.',
            ],
        ],
        'projects' => [
            'id' => 4,
            'key' => 'projects',
            'path' => '/projects',
            'name' => 'Projects & Case Studies',
            'schema_type' => 'CollectionPage',
            'default_title' => [
                'en' => 'Projects & Case Studies | SiS',
                'ar' => 'المشاريع ودراسات الحالة | سوبيريور للحلول المتكاملة',
            ],
            'default_description' => [
                'en' => 'Proven industrial automation, SCADA, and OT projects engineered for mission-critical operations across key industrial sectors.',
                'ar' => 'مشاريع أتمتة صناعية وأنظمة SCADA وتكنولوجيا تشغيلية مثبتة ومصممة للعمليات الحيوية عبر القطاعات الصناعية الكبرى.',
            ],
        ],
        'use-cases' => [
            'id' => 5,
            'key' => 'use-cases',
            'path' => '/use-cases',
            'name' => 'Use Cases',
            'schema_type' => 'CollectionPage',
            'default_title' => [
                'en' => 'Use Cases | SiS',
                'ar' => 'حالات الاستخدام | سوبيريور للحلول المتكاملة',
            ],
            'default_description' => [
                'en' => 'Real-world industrial automation and digital transformation case studies delivered across multiple industries using leading technologies.',
                'ar' => 'دراسات حالة واقعية للأتمتة الصناعية والتحول الرقمي تم تنفيذها في مختلف الصناعات باستخدام أحدث التقنيات.',
            ],
        ],
        'partner-ecosystem' => [
            'id' => 6,
            'key' => 'partner-ecosystem',
            'path' => '/partner-ecosystem',
            'name' => 'Partner Ecosystem',
            'schema_type' => 'CollectionPage',
            'default_title' => [
                'en' => 'Partner Ecosystem | SiS',
                'ar' => 'منظومة الشركاء | سوبيريور للحلول المتكاملة',
            ],
            'default_description' => [
                'en' => 'Explore the global industrial automation and technology leaders partnering with Superior Integrated Solutions.',
                'ar' => 'استكشف قادة الأتمتة الصناعية والتكنولوجيا العالميين الذين يتعاونون مع شركة سوبيريور للحلول المتكاملة.',
            ],
        ],
        'contact' => [
            'id' => 7,
            'key' => 'contact',
            'path' => '/contact',
            'name' => 'Contact Us',
            'schema_type' => 'ContactPage',
            'default_title' => [
                'en' => 'Contact Us | SiS',
                'ar' => 'اتصل بنا | سوبيريور للحلول المتكاملة',
            ],
            'default_description' => [
                'en' => 'Contact SiS engineering and sales for consultations, industrial automation inquiries, and mission-critical technical support.',
                'ar' => 'اتصل بفريق الهندسة والمبيعات في SiS للاستشارات واستفسارات الأتمتة الصناعية والدعم الفني للعمليات الحيوية.',
            ],
        ],
    ];

    /**
     * Get page configuration by key.
     */
    public static function getPage(string $key): ?array
    {
        return self::PAGES[$key] ?? null;
    }

    /**
     * Get page configuration by URI path.
     */
    public static function getPageByPath(string $path): ?array
    {
        $clean = trim(explode('?', explode('#', $path)[0])[0], '/');
        $normalizedPath = $clean === '' ? '/' : '/' . $clean;

        foreach (self::PAGES as $page) {
            if ($page['path'] === $normalizedPath) {
                return $page;
            }
        }

        return null;
    }

    /**
     * Retrieve the SeoMetadata Eloquent record for a static page.
     */
    public static function getMetadataRecord(int $pageId): ?SeoMetadata
    {
        try {
            return SeoMetadata::where('seoable_type', 'static_page')
                ->where('seoable_id', $pageId)
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Retrieve raw bilingual dictionary format expected by SeoFormSection.vue.
     */
    public static function getRawSeoData(string $key): ?array
    {
        $config = self::getPage($key);
        if (!$config) {
            return null;
        }

        $seo = self::getMetadataRecord($config['id']);

        return [
            'key' => $config['key'],
            'name' => $config['name'],
            'path' => $config['path'],
            'fallback_title' => $config['default_title'],
            'fallback_description' => $config['default_description'],
            'seo' => [
                'meta_title' => [
                    'en' => $seo?->getTranslation('meta_title', 'en', false) ?? '',
                    'ar' => $seo?->getTranslation('meta_title', 'ar', false) ?? '',
                ],
                'meta_description' => [
                    'en' => $seo?->getTranslation('meta_description', 'en', false) ?? '',
                    'ar' => $seo?->getTranslation('meta_description', 'ar', false) ?? '',
                ],
                'og_title' => [
                    'en' => $seo?->getTranslation('og_title', 'en', false) ?? '',
                    'ar' => $seo?->getTranslation('og_title', 'ar', false) ?? '',
                ],
                'og_description' => [
                    'en' => $seo?->getTranslation('og_description', 'en', false) ?? '',
                    'ar' => $seo?->getTranslation('og_description', 'ar', false) ?? '',
                ],
                'meta_keywords' => [
                    'en' => $seo?->schema_overrides['keywords']['en'] ?? '',
                    'ar' => $seo?->schema_overrides['keywords']['ar'] ?? '',
                ],
                'og_image' => $seo?->og_image ?? '',
                'canonical_url' => $seo?->canonical_url ?? '',
                'is_noindex' => (bool) ($seo?->is_noindex ?? false),
                'is_nofollow' => (bool) ($seo?->is_nofollow ?? false),
                'schema_type' => $seo?->schema_type ?: $config['schema_type'],
                'schema_overrides' => $seo?->schema_overrides,
            ],
        ];
    }

    /**
     * Retrieve resolved public SEO payload for a specific locale.
     */
    public static function getResolvedPayload(string $key, string $locale = 'en'): ?array
    {
        $config = self::getPage($key);
        if (!$config) {
            return null;
        }

        $seo = self::getMetadataRecord($config['id']);
        $globalSetting = SeoSetting::instance();
        $baseUrl = rtrim($globalSetting->canonical_base_url ?: config('app.url'), '/');

        // Title resolution
        $manualTitle = $seo?->getTranslation('meta_title', $locale, false)
            ?: $seo?->getTranslation('meta_title', 'en', false);
        $title = $manualTitle
            ?: ($config['default_title'][$locale] ?? $config['default_title']['en']);

        // Description resolution
        $manualDesc = $seo?->getTranslation('meta_description', $locale, false)
            ?: $seo?->getTranslation('meta_description', 'en', false);
        $desc = $manualDesc
            ?: ($config['default_description'][$locale] ?? $config['default_description']['en']);

        // OG Title & Description
        $ogTitle = $seo?->getTranslation('og_title', $locale, false)
            ?: $seo?->getTranslation('og_title', 'en', false)
            ?: $title;

        $ogDesc = $seo?->getTranslation('og_description', $locale, false)
            ?: $seo?->getTranslation('og_description', 'en', false)
            ?: $desc;

        // OG Image
        $ogImage = $seo?->og_image;
        if ($ogImage) {
            if (!str_starts_with($ogImage, 'http://') && !str_starts_with($ogImage, 'https://')) {
                $ogImage = $baseUrl . '/' . ltrim($ogImage, '/');
            }
        } else {
            $ogImage = $globalSetting->default_og_image
                ? (str_starts_with($globalSetting->default_og_image, 'http') ? $globalSetting->default_og_image : $baseUrl . '/' . ltrim($globalSetting->default_og_image, '/'))
                : $baseUrl . '/sis-logo.png';
        }

        // Canonical URL
        $canonical = $seo?->canonical_url
            ?: ($config['path'] === '/' ? $baseUrl . '/' : $baseUrl . $config['path']);

        // Keywords
        $keywords = $seo?->schema_overrides['keywords'][$locale]
            ?? $seo?->schema_overrides['keywords']['en']
            ?? '';

        return [
            'key' => $config['key'],
            'path' => $config['path'],
            'meta_title' => (string) $title,
            'meta_description' => (string) $desc,
            'meta_keywords' => (string) $keywords,
            'og_title' => (string) $ogTitle,
            'og_description' => (string) $ogDesc,
            'og_image' => (string) $ogImage,
            'canonical_url' => (string) $canonical,
            'is_noindex' => (bool) ($seo?->is_noindex ?? $globalSetting->default_noindex ?? false),
            'is_nofollow' => (bool) ($seo?->is_nofollow ?? false),
            'schema_type' => (string) ($seo?->schema_type ?: $config['schema_type']),
        ];
    }
}
