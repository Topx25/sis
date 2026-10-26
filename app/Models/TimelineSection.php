<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimelineSection extends Model
{
    protected $table = 'timeline_sections';

    protected $fillable = [
        'tagline',
        'title',
        'title_highlight',
    ];
}
