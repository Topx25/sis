<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class HomeWhySisSection extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'left_title',
        'right_subtitle',
        'right_title',
        'right_description',
    ];

    protected $fillable = [
        'left_title',
        'right_subtitle',
        'right_title',
        'right_description',
    ];
}
