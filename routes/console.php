<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sitemap:generate', function () {
    $this->info('Generating XML sitemap...');
    $response = app(\App\Http\Controllers\SitemapController::class)->index();
    $this->info('Sitemap generated successfully (status ' . $response->getStatusCode() . ').');
})->purpose('Generate, cache, and physically sync sitemap.xml');
