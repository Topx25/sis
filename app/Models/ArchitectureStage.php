<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ArchitectureStage extends Model
{
    use HasTranslations;

    public array $translatable = [
        'title',
        'body',
    ];

    protected $fillable = [
        'stage',
        'title',
        'body',
        'layer',
        'icon',
        'sort_order',
    ];
}
