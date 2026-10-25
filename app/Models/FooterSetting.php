<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    use HasFactory;

    protected $table = 'footer_settings';

    protected $fillable = [
        'logo_path',
        'description',
        'copyright_text',
        'dynamic_year',
        'floating_contact_enabled',
        'floating_contact_url',
        'floating_contact_icon',
        'floating_contact_label',
        'metadata',
    ];

    protected $casts = [
        'dynamic_year' => 'boolean',
        'floating_contact_enabled' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get or create the default singleton footer settings.
     */
    public static function instance(): self
    {
        return static::firstOrCreate([], [
            'description' => 'Bridging OT and IT across the MENA region with industrial automation and IIoT excellence.',
            'copyright_text' => 'Superior Integrated Solutions. All rights reserved.',
            'dynamic_year' => true,
            'floating_contact_enabled' => true,
            'floating_contact_url' => 'https://wa.me/201007568651',
            'floating_contact_icon' => 'whatsapp',
            'floating_contact_label' => 'Contact us on WhatsApp',
        ]);
    }
}
