<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TimelineEvent extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'title',
        'description',
    ];

    protected $fillable = [
        'year',
        'title',
        'description',
        'icon',
    ];
}
