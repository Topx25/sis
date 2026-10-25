<?php

namespace App\Models;

use App\Traits\HasSeoMetadata;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
    use HasFactory, HasTranslations, HasSeoMetadata;

    public array $translatable = [
        'title',
        'client_name',
        'location',
        'summary',
        'description',
        'metrics',
        'results',
    ];

    protected $fillable = [
        'sector_id',
        'title',
        'slug',
        'client_name',
        'location',
        'summary',
        'description',
        'cover_image',
        'tags',
        'is_featured',
        'metrics',
        'scope',
        'technologies',
        'results',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_featured' => 'boolean',
        'metrics' => 'array',
        'scope' => 'array',
        'technologies' => 'array',
        'results' => 'array',
    ];

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class);
    }

    public static function healMetricsList($rawMetrics): array
    {
        if (empty($rawMetrics)) {
            return [];
        }

        if (is_string($rawMetrics)) {
            $decoded = json_decode($rawMetrics, true);
            $rawMetrics = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($rawMetrics)) {
            return [];
        }

        $healed = [];
        $count = count($rawMetrics);
        for ($i = 0; $i < $count; $i++) {
            $curr = $rawMetrics[$i] ?? [];
            if (!is_array($curr)) {
                continue;
            }

            $val = trim((string)($curr['value'] ?? ''));
            $lbl = trim((string)($curr['label'] ?? ''));

            if ($val !== '' && $lbl === '' && isset($rawMetrics[$i + 1]) && is_array($rawMetrics[$i + 1])) {
                $next = $rawMetrics[$i + 1];
                $nextVal = trim((string)($next['value'] ?? ''));
                $nextLbl = trim((string)($next['label'] ?? ''));

                if ($nextVal === '' && $nextLbl !== '') {
                    $healed[] = ['value' => $val, 'label' => $nextLbl];
                    $i++;
                    continue;
                }
            }

            if ($val !== '' || $lbl !== '') {
                $healed[] = ['value' => $val, 'label' => $lbl];
            }
        }

        return $healed;
    }

    public function getMetricsAttribute($value): array
    {
        $metrics = $value;
        if (is_string($metrics)) {
            $metrics = json_decode($metrics, true);
        }
        return self::healMetricsList(is_array($metrics) ? $metrics : []);
    }

    protected static function booted()
    {
        static::saved(function ($project) {
            \App\Support\CacheManager::invalidateUseCases();
        });

        static::deleted(function ($project) {
            \App\Support\CacheManager::invalidateUseCases();
        });
    }
}
