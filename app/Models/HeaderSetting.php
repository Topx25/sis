<?php

namespace App\Models;

use App\Traits\HasStorageUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderSetting extends Model
{
    use HasFactory, HasStorageUrl;

    protected $table = 'header_settings';

    protected $fillable = [
        'logo_path',
        'nav_links',
        'button_text',
        'button_link',
        'button_enabled',
    ];

    protected $casts = [
        'nav_links' => 'array',
        'button_enabled' => 'boolean',
    ];

    /**
     * Default navigation links in the header.
     */
    public static function defaultNavLinks(): array
    {
        return [
            [
                'id' => 1,
                'key' => 'home',
                'label' => 'Home',
                'url' => '/',
                'has_dropdown' => false,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'key' => 'services',
                'label' => 'Services & Solutions',
                'url' => '/services',
                'has_dropdown' => true,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'key' => 'industries',
                'label' => 'Industries',
                'url' => '/industries',
                'has_dropdown' => true,
                'is_active' => true,
            ],
            [
                'id' => 4,
                'key' => 'partner-ecosystem',
                'label' => 'Partner Ecosystem',
                'url' => '/partner-ecosystem',
                'has_dropdown' => false,
                'is_active' => true,
            ],
            [
                'id' => 5,
                'key' => 'use-cases',
                'label' => 'Use Cases',
                'url' => '/use-cases',
                'has_dropdown' => false,
                'is_active' => true,
            ],
        ];
    }

    /**
     * Get or create the singleton instance of HeaderSetting.
     */
    public static function instance(): self
    {
        return static::firstOrCreate([], [
            'logo_path' => null,
            'nav_links' => static::defaultNavLinks(),
            'button_text' => 'Get in Touch',
            'button_link' => '/contact',
            'button_enabled' => true,
        ]);
    }

    /**
     * Get the full URL for the logo or null.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->formatStorageUrl($this->logo_path);
    }
}
