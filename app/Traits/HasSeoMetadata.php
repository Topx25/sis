<?php

namespace App\Traits;

use App\Models\Redirect;
use App\Models\SeoMetadata;
use App\Models\SeoSetting;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\UploadedFile;

trait HasSeoMetadata
{
    /**
     * Boot the trait to hook into Eloquent model lifecycle events safely.
     * All database operations are wrapped to guard against unmigrated tables.
     */
    protected static function bootHasSeoMetadata(): void
    {
        // 1. Delete associated polymorphic metadata when model is deleted
        static::deleting(function ($model) {
            try {
                $model->seoMetadata()->delete();
            } catch (\Throwable $e) {
                // Ignore if table is not yet migrated
            }
        });

        // 2. Record slug history when slug changes (safe observation, no immediate redirect execution)
        static::updating(function ($model) {
            if ($model->isDirty('slug')) {
                $oldSlug = (string) $model->getOriginal('slug');
                $newSlug = (string) $model->slug;

                if ($oldSlug !== '' && $newSlug !== '' && $oldSlug !== $newSlug) {
                    $oldPath = $model->getPublicUrlPath($oldSlug);
                    $newPath = $model->getPublicUrlPath($newSlug);

                    if ($oldPath && $newPath && $oldPath !== $newPath) {
                        try {
                            Redirect::recordRedirect($oldPath, $newPath, 301);
                        } catch (\Throwable $e) {
                            // Ignore if redirects table is not yet migrated
                        }
                    }
                }
            }
        });
    }

    /**
     * Polymorphic relation to SeoMetadata.
     */
    public function seoMetadata(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    /**
     * Safe accessor for seoMetadata that never throws if table is not yet migrated.
     */
    public function safeSeoMetadata(): ?SeoMetadata
    {
        try {
            if ($this->relationLoaded('seoMetadata')) {
                return $this->getRelation('seoMetadata');
            }
            return $this->seoMetadata;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Public route prefix mapped to the entity.
     */
    public function getPublicUrlPrefix(): string
    {
        return match (static::class) {
            \App\Models\SolutionPage::class => '/solution-pages',
            \App\Models\Project::class => '/projects',
            \App\Models\Sector::class => '/sectors',
            \App\Models\Service::class => '/services',
            default => '',
        };
    }

    /**
     * Construct the full public path for this entity and slug.
     */
    public function getPublicUrlPath(?string $slug = null): ?string
    {
        $targetSlug = $slug ?? $this->slug;
        if (empty($targetSlug)) {
            return null;
        }

        $prefix = rtrim($this->getPublicUrlPrefix(), '/');
        $cleanSlug = ltrim($targetSlug, '/');

        return $prefix ? "{$prefix}/{$cleanSlug}" : "/{$cleanSlug}";
    }

    /**
     * Safe helper to read a translatable field in a specific locale, falling back gracefully.
     */
    protected function getEntityTranslation(string $attribute, string $locale): ?string
    {
        if (method_exists($this, 'getTranslation') && in_array($attribute, $this->translatable ?? [], true)) {
            $val = $this->getTranslation($attribute, $locale, false);
            if (!empty($val) && is_string($val)) {
                return $val;
            }
        } elseif (isset($this->{$attribute}) && is_string($this->{$attribute})) {
            return $this->{$attribute};
        }

        return null;
    }

    /**
     * Helper to format an image path into a fully qualified absolute URL.
     */
    protected function resolveAbsoluteImageUrl(?string $path, string $baseUrl): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            return rtrim($baseUrl, '/') . '/' . $cleanPath;
        }

        return rtrim($baseUrl, '/') . '/storage/' . $cleanPath;
    }

    /**
     * Compute fallback SEO payload when no manual override exists.
     * Implements a 5-step fallback hierarchy:
     * 1. Manual SEO override in requested locale
     * 2. Entity content in requested locale
     * 3. Manual SEO override in English
     * 4. Entity content in English
     * 5. Global site defaults from SeoSetting
     */
    public function getSeoPayload(string $locale = 'en'): array
    {
        $seo = $this->safeSeoMetadata();
        $settings = SeoSetting::instance();
        $baseUrl = $settings->canonical_base_url ?: 'https://sis.iptvpro.org';

        // 1. Resolve Title
        $title = $seo?->getTranslation('meta_title', $locale, false)
            ?: $this->getEntityTranslation('title', $locale)
            ?: $this->getEntityTranslation('name', $locale)
            ?: $seo?->getTranslation('meta_title', 'en', false)
            ?: $this->getEntityTranslation('title', 'en')
            ?: $this->getEntityTranslation('name', 'en')
            ?: ($this->title ?? $this->name ?? '');

        // 2. Resolve Description
        $rawDesc = $seo?->getTranslation('meta_description', $locale, false)
            ?: $this->getEntityTranslation('description', $locale)
            ?: $this->getEntityTranslation('subtitle', $locale)
            ?: $this->getEntityTranslation('summary', $locale)
            ?: $this->getEntityTranslation('headline', $locale)
            ?: $seo?->getTranslation('meta_description', 'en', false)
            ?: $this->getEntityTranslation('description', 'en')
            ?: $this->getEntityTranslation('subtitle', 'en')
            ?: $this->getEntityTranslation('summary', 'en')
            ?: $this->getEntityTranslation('headline', 'en')
            ?: $settings->getTranslation('default_meta_description', $locale, false)
            ?: $settings->getTranslation('default_meta_description', 'en', false)
            ?: '';

        $cleanDesc = trim(strip_tags((string) $rawDesc));
        if (mb_strlen($cleanDesc) > 160) {
            $cleanDesc = mb_substr($cleanDesc, 0, 157) . '...';
        }

        // 3. Resolve Open Graph Title & Description
        $ogTitle = $seo?->getTranslation('og_title', $locale, false)
            ?: $seo?->getTranslation('og_title', 'en', false)
            ?: $title;

        $rawOgDesc = $seo?->getTranslation('og_description', $locale, false)
            ?: $seo?->getTranslation('og_description', 'en', false)
            ?: $cleanDesc;

        $ogDesc = trim(strip_tags((string) $rawOgDesc));
        if (mb_strlen($ogDesc) > 200) {
            $ogDesc = mb_substr($ogDesc, 0, 197) . '...';
        }

        // 4. Resolve Open Graph Image (Guarantee absolute URL)
        $rawOgImage = $seo?->og_image
            ?: ($this->hero_image ?? $this->cover_image ?? $this->image ?? $settings->default_og_image);
        $ogImage = $this->resolveAbsoluteImageUrl($rawOgImage, $baseUrl);

        // 5. Canonical URL
        $canonicalUrl = $seo?->canonical_url;

        // 6. Schema Type
        $defaultSchema = match (static::class) {
            \App\Models\SolutionPage::class => 'Service',
            \App\Models\Project::class => 'Article',
            \App\Models\Sector::class => 'CollectionPage',
            \App\Models\Service::class => 'Service',
            default => 'WebPage',
        };
        $schemaType = $seo?->schema_type ?: $defaultSchema;

        // 7. Standardized Breadcrumbs
        $breadcrumbs = [
            ['name' => 'Home', 'url' => rtrim($baseUrl, '/') . '/'],
        ];

        $entitySlug = (string) ($this->slug ?? '');
        if (static::class === \App\Models\SolutionPage::class) {
            $breadcrumbs[] = ['name' => 'Services & Solutions', 'url' => rtrim($baseUrl, '/') . '/services'];
            if ($entitySlug !== '') {
                $breadcrumbs[] = ['name' => (string) $title, 'url' => rtrim($baseUrl, '/') . "/solution-pages/{$entitySlug}"];
            }
        } elseif (static::class === \App\Models\Project::class) {
            $breadcrumbs[] = ['name' => 'Projects & Case Studies', 'url' => rtrim($baseUrl, '/') . '/projects'];
            if ($entitySlug !== '') {
                $breadcrumbs[] = ['name' => (string) $title, 'url' => rtrim($baseUrl, '/') . "/projects/{$entitySlug}"];
            }
        } elseif (static::class === \App\Models\Sector::class) {
            $breadcrumbs[] = ['name' => 'Industries', 'url' => rtrim($baseUrl, '/') . '/projects'];
            if ($entitySlug !== '') {
                $breadcrumbs[] = ['name' => (string) $title, 'url' => rtrim($baseUrl, '/') . "/sectors/{$entitySlug}"];
            }
        } elseif (static::class === \App\Models\Service::class) {
            $breadcrumbs[] = ['name' => 'Services', 'url' => rtrim($baseUrl, '/') . '/services'];
            if ($entitySlug !== '') {
                $breadcrumbs[] = ['name' => (string) $title, 'url' => rtrim($baseUrl, '/') . "/services/{$entitySlug}"];
            }
        }

        $keywords = $seo?->schema_overrides['keywords'][$locale]
            ?? $seo?->schema_overrides['keywords']['en']
            ?? null;

        return [
            'meta_title' => (string) $title,
            'meta_description' => (string) $cleanDesc,
            'meta_keywords' => $keywords ? (string) $keywords : null,
            'og_title' => (string) $ogTitle,
            'og_description' => (string) $ogDesc,
            'og_image' => $ogImage,
            'canonical_url' => $canonicalUrl ? (string) $canonicalUrl : null,
            'is_noindex' => (bool) ($seo?->is_noindex ?? false),
            'is_nofollow' => (bool) ($seo?->is_nofollow ?? false),
            'schema_type' => (string) $schemaType,
            'schema_overrides' => $seo?->schema_overrides,
            'breadcrumbs' => $breadcrumbs,
        ];
    }

    /**
     * Retrieve raw bilingual dictionaries for administrative dashboard forms.
     */
    public function getRawSeoData(): array
    {
        $seo = $this->safeSeoMetadata();

        return [
            'meta_title' => [
                'en' => $seo?->getTranslation('meta_title', 'en', false) ?? '',
                'ar' => $seo?->getTranslation('meta_title', 'ar', false) ?? '',
            ],
            'meta_description' => [
                'en' => $seo?->getTranslation('meta_description', 'en', false) ?? '',
                'ar' => $seo?->getTranslation('meta_description', 'ar', false) ?? '',
            ],
            'meta_keywords' => [
                'en' => $seo?->schema_overrides['keywords']['en'] ?? '',
                'ar' => $seo?->schema_overrides['keywords']['ar'] ?? '',
            ],
            'og_title' => [
                'en' => $seo?->getTranslation('og_title', 'en', false) ?? '',
                'ar' => $seo?->getTranslation('og_title', 'ar', false) ?? '',
            ],
            'og_description' => [
                'en' => $seo?->getTranslation('og_description', 'en', false) ?? '',
                'ar' => $seo?->getTranslation('og_description', 'ar', false) ?? '',
            ],
            'og_image' => $seo?->og_image ?? '',
            'canonical_url' => $seo?->canonical_url,
            'is_noindex' => (bool) ($seo?->is_noindex ?? false),
            'is_nofollow' => (bool) ($seo?->is_nofollow ?? false),
            'schema_type' => $seo?->schema_type ?? '',
            'schema_overrides' => $seo?->schema_overrides,
        ];
    }

    /**
     * Synchronize incoming SEO metadata from admin requests.
     * Automatically handles JSON decoding and file uploads.
     */
    public function syncSeoMetadata(mixed $seoData): ?SeoMetadata
    {
        if (empty($seoData)) {
            return null;
        }

        if (is_string($seoData)) {
            $decoded = json_decode($seoData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $seoData = $decoded;
            } else {
                return null;
            }
        }

        if (!is_array($seoData)) {
            return null;
        }

        try {
            /** @var SeoMetadata $metadata */
            $metadata = $this->seoMetadata()->firstOrNew([
                'seoable_type' => get_class($this),
                'seoable_id'   => $this->id,
            ]);

            // 1. Bilingual Meta Titles
            if (isset($seoData['meta_title'])) {
                if (is_array($seoData['meta_title'])) {
                    $metadata->setTranslations('meta_title', array_filter($seoData['meta_title']));
                } elseif (is_string($seoData['meta_title'])) {
                    $metadata->setTranslation('meta_title', 'en', $seoData['meta_title']);
                }
            }

            // 2. Bilingual Meta Descriptions
            if (isset($seoData['meta_description'])) {
                if (is_array($seoData['meta_description'])) {
                    $metadata->setTranslations('meta_description', array_filter($seoData['meta_description']));
                } elseif (is_string($seoData['meta_description'])) {
                    $metadata->setTranslation('meta_description', 'en', $seoData['meta_description']);
                }
            }

            // 3. Bilingual OG Titles
            if (isset($seoData['og_title'])) {
                if (is_array($seoData['og_title'])) {
                    $metadata->setTranslations('og_title', array_filter($seoData['og_title']));
                } elseif (is_string($seoData['og_title'])) {
                    $metadata->setTranslation('og_title', 'en', $seoData['og_title']);
                }
            }

            // 4. Bilingual OG Descriptions
            if (isset($seoData['og_description'])) {
                if (is_array($seoData['og_description'])) {
                    $metadata->setTranslations('og_description', array_filter($seoData['og_description']));
                } elseif (is_string($seoData['og_description'])) {
                    $metadata->setTranslation('og_description', 'en', $seoData['og_description']);
                }
            }

            // 5. Media & Canonical Directives
            if (isset($seoData['og_image'])) {
                if ($seoData['og_image'] instanceof UploadedFile) {
                    $path = $seoData['og_image']->store('seo', 'public');
                    $metadata->og_image = $path;
                } elseif (is_string($seoData['og_image'])) {
                    $metadata->og_image = $seoData['og_image'];
                }
            }

            if (array_key_exists('canonical_url', $seoData)) {
                $metadata->canonical_url = $seoData['canonical_url'] ?: null;
            }

            if (array_key_exists('is_noindex', $seoData)) {
                $metadata->is_noindex = filter_var($seoData['is_noindex'], FILTER_VALIDATE_BOOLEAN);
            }

            if (array_key_exists('is_nofollow', $seoData)) {
                $metadata->is_nofollow = filter_var($seoData['is_nofollow'], FILTER_VALIDATE_BOOLEAN);
            }

            if (array_key_exists('schema_type', $seoData)) {
                $metadata->schema_type = $seoData['schema_type'] ?: null;
            }

            if (array_key_exists('schema_overrides', $seoData)) {
                $metadata->schema_overrides = is_array($seoData['schema_overrides'])
                    ? $seoData['schema_overrides']
                    : (json_decode($seoData['schema_overrides'] ?? '', true) ?: null);
            }

            // 6. Keywords (persisted cleanly in schema_overrides['keywords'])
            if (isset($seoData['meta_keywords'])) {
                $rawKeywords = $seoData['meta_keywords'];
                if (is_string($rawKeywords)) {
                    $decoded = json_decode($rawKeywords, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $rawKeywords = $decoded;
                    }
                }
                $overrides = $metadata->schema_overrides ?? [];
                if (is_array($rawKeywords)) {
                    $overrides['keywords'] = [
                        'en' => trim($rawKeywords['en'] ?? ''),
                        'ar' => trim($rawKeywords['ar'] ?? ''),
                    ];
                } elseif (is_string($rawKeywords)) {
                    $overrides['keywords'] = [
                        'en' => trim($rawKeywords),
                        'ar' => $overrides['keywords']['ar'] ?? '',
                    ];
                }
                $metadata->schema_overrides = $overrides;
            }

            $metadata->save();

            return $metadata;
        } catch (\Throwable $e) {
            // Guard against unmigrated database table
            return null;
        }
    }
}
