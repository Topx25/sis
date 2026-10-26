<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicesSection extends Model
{
    protected $table = 'services_sections';

    protected $fillable = [
        'subtitle',
        'title',
        'title_highlight',
        'description',
    ];
}
