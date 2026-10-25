<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallSiteAssets extends Command
{
    protected $signature = 'sis:install-assets
        {--source= : Path to the frontend public/images directory}';

    protected $description = 'Copy bundled site images into storage/app/public at seed-expected paths';

    public function handle(): int
    {
        $source = $this->option('source');

        if (!$source) {
            $possiblePaths = [
                base_path('../public/images'),
                base_path('../public'),
                base_path('../src/public/images'),
                base_path('public/images'),
            ];

            foreach ($possiblePaths as $path) {
                if (File::isDirectory($path)) {
                    $source = realpath($path);
                    break;
                }
            }
        }

        if (!$source || !File::isDirectory($source)) {
            $this->error("Source directory not found: {$source}");
            return self::FAILURE;
        }

        // Mapping: from frontend source => to storage_path('app/public/<to>')
        $mapping = [
            'partners'            => 'partners',
            'ourClints'           => 'ourClints',
            'work-samples-images' => 'work-samples', // seed expects work-samples/*.png
            'work-samples'        => 'work-samples',
            'about-hero.webp'     => 'images/about-hero.webp',
        ];

        $copied = 0;
        foreach ($mapping as $from => $to) {
            $fromPath = rtrim($source, '/') . '/' . $from;
            if (!File::exists($fromPath)) {
                $this->warn("Missing source entry: {$fromPath}");
                continue;
            }

            $dest = storage_path('app/public/' . $to);

            if (File::isFile($fromPath)) {
                File::ensureDirectoryExists(dirname($dest));
                File::copy($fromPath, $dest);
                $copied++;
                continue;
            }

            File::ensureDirectoryExists($dest);
            $count = 0;
            foreach (File::allFiles($fromPath) as $file) {
                File::copy($file->getPathname(), $dest . '/' . $file->getFilename());
                $count++;
            }
            $copied += $count;
        }

        $this->info("Copied {$copied} file(s) into storage/app/public.");

        if (!File::exists(public_path('storage'))) {
            $this->warn('Run `php artisan storage:link` if the public/storage symlink is missing.');
        }

        return self::SUCCESS;
    }
}
