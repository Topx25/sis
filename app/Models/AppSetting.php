<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * Generic key/value application settings store (grouped), with transparent
 * encryption for secret values (e.g. Turnstile secret, SMTP password).
 */
class AppSetting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['group', 'key', 'value', 'is_encrypted'];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    /**
     * Read a single setting, decrypting it if it was stored encrypted.
     */
    public static function getValue(string $group, string $key, mixed $default = null): mixed
    {
        $row = static::query()->where('group', $group)->where('key', $key)->first();

        if (! $row) {
            return $default;
        }

        $value = $row->value;

        if ($row->is_encrypted && $value !== null && $value !== '') {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Throwable $e) {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Create or update a single setting. Secrets are encrypted at rest.
     */
    public static function setValue(string $group, string $key, mixed $value, bool $encrypted = false): void
    {
        $stored = ($encrypted && $value !== null && $value !== '')
            ? Crypt::encryptString((string) $value)
            : $value;

        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $stored, 'is_encrypted' => $encrypted],
        );
    }

    /**
     * Return a group's settings as an associative array.
     * When $maskSecrets is true, encrypted values are returned as a fixed mask
     * (so secrets are never exposed to the client) — an empty secret stays empty.
     */
    public static function group(string $group, bool $maskSecrets = false): array
    {
        $out = [];

        foreach (static::query()->where('group', $group)->get() as $row) {
            if ($row->is_encrypted) {
                if ($maskSecrets) {
                    $out[$row->key] = ($row->value !== null && $row->value !== '') ? '********' : '';
                    continue;
                }
                try {
                    $out[$row->key] = ($row->value !== null && $row->value !== '')
                        ? Crypt::decryptString($row->value)
                        : '';
                } catch (\Throwable $e) {
                    $out[$row->key] = '';
                }
            } else {
                $out[$row->key] = $row->value;
            }
        }

        return $out;
    }
}
