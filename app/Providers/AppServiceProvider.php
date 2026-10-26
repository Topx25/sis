<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Invalidate the static public-API cache whenever any content model is
        // written, so edits made in the dashboard appear on the next request.
        Event::listen(['eloquent.saved: *', 'eloquent.deleted: *'], function (): void {
            // Only a mutating request (dashboard save/delete) should invalidate.
            // Read requests may lazily create settings rows via firstOrCreate;
            // those must not wipe the cache on every page view.
            $req = request();

            if ($req === null || $req->isMethod('GET') || $req->isMethod('HEAD') || $req->isMethod('OPTIONS')) {
                return;
            }

            $docroot = $_SERVER['DOCUMENT_ROOT'] ?? '';

            if ($docroot === '') {
                return;
            }

            $dir = rtrim($docroot, '/') . '/api/apicache';

            if (is_dir($dir)) {
                File::deleteDirectory($dir);
            }
        });
    }
}
