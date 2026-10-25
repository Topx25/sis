<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ContactPage extends Model
{
    use HasTranslations;

    public array $translatable = [
        'cta_title',
        'cta_description',
        'cta_link_text',
    ];

    protected $fillable = [
        'website',
        'email',
        'emails',
        'phones',
        'cta_title',
        'cta_description',
        'cta_link_text',
        'cta_link',
    ];

    protected $casts = [
        'phones' => 'array',
        'emails' => 'array',
    ];
}
