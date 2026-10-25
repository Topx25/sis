<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    use HasFactory;

    protected $table = 'redirects';

    protected $fillable = [
        'source_path',
        'target_path',
        'status_code',
        'is_active',
        'hit_count',
        'last_hit_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'status_code' => 'integer',
        'hit_count' => 'integer',
        'last_hit_at' => 'datetime',
    ];

    /**
     * Normalize a URL path:
     * - Strips protocol, domain, query strings, and fragments
     * - Trims whitespace and collapses redundant slashes
     * - Converts to lowercase
     * - Guarantees leading slash and strips trailing slashes (except root '/')
     */
    public static function normalizePath(string $path): string
    {
        $parsed = parse_url(trim($path), PHP_URL_PATH);
        $clean = ($parsed !== false && $parsed !== null) ? $parsed : $path;
        $clean = trim(strtolower($clean));
        $clean = preg_replace('#/+#', '/', $clean);

        if ($clean === '' || $clean === '/') {
            return '/';
        }

        return '/' . trim($clean, '/');
    }

    /**
     * Safely record or update a redirect:
     * - Normalizes source and target paths
     * - Prevents self-redirects
     * - Prevents immediate recursive loops (A -> B and B -> A)
     * - Flattens redirect chains (if existing redirects point to source, repoint to new target)
     * - Upserts by unique source_path
     */
    public static function recordRedirect(string $sourcePath, string $targetPath, int $statusCode = 301): ?self
    {
        $source = static::normalizePath($sourcePath);
        $target = static::normalizePath($targetPath);

        // 1. Prevent self-redirects
        if ($source === $target || $source === '/') {
            return null;
        }

        // 2. Prevent immediate loop: if a reverse redirect (target -> source) already exists, remove it
        static::where('source_path', $target)
            ->where('target_path', $source)
            ->delete();

        // 3. Flatten prior chains: if another URL was already redirecting to $source, repoint it directly to $target
        static::where('target_path', $source)
            ->where('source_path', '!=', $target)
            ->update(['target_path' => $target]);

        // 4. Upsert the redirect row
        return static::updateOrCreate(
            ['source_path' => $source],
            [
                'target_path' => $target,
                'status_code' => $statusCode,
                'is_active' => true,
            ]
        );
    }
}
