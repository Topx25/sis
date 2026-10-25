<?php

namespace App\Traits;

trait HasStorageUrl
{
    /**
     * Format an image or file storage path into a clean absolute URL.
     *
     * @param string|null $path
     * @return string|null
     */
    protected function formatStorageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // Return as-is if already a full HTTP/HTTPS URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');

        // If path already starts with storage/, wrap with asset() directly
        if (str_starts_with($cleanPath, 'storage/')) {
            return asset($cleanPath);
        }

        return asset('storage/' . $cleanPath);
    }
}
