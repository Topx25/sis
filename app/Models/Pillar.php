<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Pillar extends Model
{
    use HasTranslations;

    public array $translatable = [
        'title',
        'body',
    ];

    protected $fillable = [
        'step',
        'title',
        'body',
        'icon',
        'sort_order',
    ];
}
