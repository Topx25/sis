<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SeoSetting extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'seo_settings';

    public array $translatable = [
        'site_name',
        'title_suffix',
        'default_meta_description',
        'default_meta_keywords',
    ];

    protected $fillable = [
        'canonical_base_url',
        'google_site_verification',
        'bing_site_verification',
        'default_noindex',
        'site_name',
        'title_suffix',
        'default_meta_description',
        'default_meta_keywords',
        'default_og_image',
        'organization_legal_name',
        'organization_phone',
        'organization_email',
        'social_links',
    ];

    protected $casts = [
        'default_noindex' => 'boolean',
        'social_links' => 'array',
    ];

    /**
     * Singleton instance accessor with safe bilingual fallbacks and production canonical domain.
     */
    public static function instance(): self
    {
        try {
            return static::firstOrCreate(
                ['id' => 1],
                [
                    'canonical_base_url' => 'https://sis.iptvpro.org',
                    'google_site_verification' => null,
                    'bing_site_verification' => null,
                    'default_noindex' => false,
                    'site_name' => [
                        'en' => 'SiS - Superior Integrated Solutions',
                        'ar' => 'سوبيريور للحلول المتكاملة (SiS)',
                    ],
                    'title_suffix' => [
                        'en' => ' | SiS - Superior Integrated Solutions',
                        'ar' => ' | سوبيريور للحلول المتكاملة',
                    ],
                    'default_meta_description' => [
                        'en' => 'Superior Integrated Solutions (SiS) bridges OT and IT across the MENA region with industrial automation, IIoT, and digital transformation.',
                        'ar' => 'حلول سوبيريور المتكاملة (SiS) تربط بين التكنولوجيا التشغيلية وتكنولوجيا المعلومات في منطقة الشرق الأوسط وشمال أفريقيا من خلال الأتمتة الصناعية والتحول الرقمي.',
                    ],
                    'default_meta_keywords' => [
                        'en' => 'industrial automation, OT, IT, SCADA, PLC, DCS, IIoT, digital transformation, MENA, Egypt',
                        'ar' => 'أتمتة صناعية, تحول رقمي, أنظمة تحكم, سكادا, إنترنت الأشياء الصناعي, مصر, الشرق الأوسط',
                    ],
                    'default_og_image' => 'https://sis.iptvpro.org/sis-logo.png',
                    'organization_legal_name' => 'Superior Integrated Solutions S.A.E.',
                    'organization_phone' => '+20 1007 568 651',
                    'organization_email' => 'sales@sis-holding.com',
                    'social_links' => [
                        'https://www.linkedin.com/company/superior-for-integrated-solutions',
                    ],
                ]
            );
        } catch (\Throwable $e) {
            // Return an in-memory fallback instance so application never crashes if table is not yet migrated
            $fallback = new static([
                'canonical_base_url' => 'https://sis.iptvpro.org',
                'google_site_verification' => null,
                'bing_site_verification' => null,
                'default_noindex' => false,
                'default_og_image' => 'https://sis.iptvpro.org/sis-logo.png',
                'organization_legal_name' => 'Superior Integrated Solutions S.A.E.',
                'organization_phone' => '+20 1007 568 651',
                'organization_email' => 'sales@sis-holding.com',
                'social_links' => [
                    'https://www.linkedin.com/company/superior-for-integrated-solutions',
                ],
            ]);
            $fallback->id = 1;
            $fallback->setTranslations('site_name', [
                'en' => 'SiS - Superior Integrated Solutions',
                'ar' => 'سوبيريور للحلول المتكاملة (SiS)',
            ]);
            $fallback->setTranslations('title_suffix', [
                'en' => ' | SiS - Superior Integrated Solutions',
                'ar' => ' | سوبيريور للحلول المتكاملة',
            ]);
            $fallback->setTranslations('default_meta_description', [
                'en' => 'Superior Integrated Solutions (SiS) bridges OT and IT across the MENA region with industrial automation, IIoT, and digital transformation.',
                'ar' => 'حلول سوبيريور المتكاملة (SiS) تربط بين التكنولوجيا التشغيلية وتكنولوجيا المعلومات في منطقة الشرق الأوسط وشمال أفريقيا من خلال الأتمتة الصناعية والتحول الرقمي.',
            ]);
            return $fallback;
        }
    }

    /**
     * Enforce singleton behavior and invalidate cache on changes.
     */
    protected static function booted(): void
    {
        static::creating(function (SeoSetting $setting) {
            if (static::where('id', '!=', 1)->exists() || (static::where('id', 1)->exists() && (int) $setting->id !== 1)) {
                throw new \RuntimeException('SeoSetting is a singleton. Only one record (id = 1) is allowed.');
            }
            $setting->id = 1;
        });

        static::saved(function () {
            \App\Support\CacheManager::invalidateSeoSettings();
        });

        static::deleted(function () {
            \App\Support\CacheManager::invalidateSeoSettings();
        });
    }
}
