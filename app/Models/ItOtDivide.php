<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ItOtDivide extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'tagline',
        'headline',
        'subheadline',
    ];

    protected $fillable = [
        'tagline',
        'headline',
        'subheadline',
        'ot_data',
        'it_data',
        'is_active',
    ];

    protected $casts = [
        'ot_data' => 'array',
        'it_data' => 'array',
        'is_active' => 'boolean',
    ];
}
