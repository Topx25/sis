<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;

trait ImageTrait
{
    /**
     * Process, resize, compress, and save an uploaded image using Intervention Image v3.
     *
     * @param UploadedFile|mixed $file
     * @param string $folder
     * @param string|null $oldPath
     * @param int|string|null $id
     * @param string|null $prefix
     * @return string Relative storage path (e.g. "projects/1727612345_abc.jpg")
     */
    public function uploadImage($file, string $folder, ?string $oldPath = null, $id = null, ?string $prefix = null): string
    {
        if (! ($file instanceof UploadedFile)) {
            return is_string($file) ? $file : '';
        }

        // Determine destination folder
        $targetFolder = trim($folder, '/');
        if ($id !== null) {
            $subFolder = $prefix ? "{$prefix}_{$id}" : (string) $id;
            $targetFolder .= "/{$subFolder}";
        }

        $directory = storage_path('app/public/' . $targetFolder);
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $filename = time() . '_' . Str::random(12) . '.' . $extension;
        $destinationPath = $directory . DIRECTORY_SEPARATOR . $filename;
        $relativePath = $targetFolder . '/' . $filename;

        // SVGs cannot be parsed by GD raster engines, move directly
        if ($extension === 'svg') {
            $file->move($directory, $filename);
            
            // Delete old image only after successful move
            $this->deleteImage($oldPath);
            
            return $relativePath;
        }

        // Intervention Image v3 processing
        $driverClass = config('image.driver') === 'imagick' ? ImagickDriver::class : GdDriver::class;
        $manager = new ImageManager(new $driverClass());
        $image = $manager->read($file->getRealPath());

        $maxWidth = (int) config('image.resize.width', 1200);
        $maxHeight = (int) config('image.resize.height', 1200);
        $quality = (int) config('image.quality', 80);

        // scaleDown preserves aspect ratio and prevents upsizing smaller images
        $image->scaleDown(width: $maxWidth, height: $maxHeight);

        // Save compressed & resized image
        $image->save($destinationPath, quality: $quality);

        // Delete old image only after successful processing and save
        $this->deleteImage($oldPath);

        return $relativePath;
    }

    /**
     * Upload and process multiple images.
     *
     * @param array $files
     * @param string $folder
     * @param array $oldPaths
     * @return array
     */
    public function uploadMultiImages(array $files, string $folder, array $oldPaths = []): array
    {
        $this->deleteMultiImages($oldPaths);

        $uploadedPaths = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadedPaths[] = $this->uploadImage($file, $folder);
            } elseif (is_string($file) && ! empty($file)) {
                $uploadedPaths[] = $file;
            }
        }

        return $uploadedPaths;
    }

    /**
     * Delete an existing image from public storage.
     *
     * @param string|null $path
     * @return void
     */
    public function deleteImage(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        // Normalize to a path relative to the public disk root, handling
        // "storage/...", "/storage/...", and bare "projects/..." inputs alike.
        $relative = preg_replace('#^storage/#', '', ltrim($path, '/'));

        if (Storage::disk('public')->exists($relative)) {
            Storage::disk('public')->delete($relative);
        } elseif (File::exists(storage_path('app/public/' . $relative))) {
            File::delete(storage_path('app/public/' . $relative));
        }
    }

    /**
     * Delete multiple images from storage.
     *
     * @param array $paths
     * @return void
     */
    public function deleteMultiImages(array $paths): void
    {
        foreach ($paths as $path) {
            if (is_string($path)) {
                $this->deleteImage($path);
            }
        }
    }
}
