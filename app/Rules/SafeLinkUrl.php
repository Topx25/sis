<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeLinkUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || trim($value) === '') {
            $fail("The {$attribute} must be a valid URL string.");
            return;
        }

        $trimmed = trim($value);

        // Disallow dangerous HTML tags or script injection
        if (preg_match('/<[^>]*>|javascript:|data:|vbscript:/i', $trimmed)) {
            $fail("The {$attribute} contains an unsafe URL scheme or characters.");
            return;
        }

        // Allowed schemes: http, https, tel, mailto, or relative path starting with / or #
        if (
            str_starts_with($trimmed, 'http://') ||
            str_starts_with($trimmed, 'https://') ||
            str_starts_with($trimmed, 'tel:') ||
            str_starts_with($trimmed, 'mailto:') ||
            str_starts_with($trimmed, '/') ||
            str_starts_with($trimmed, '#')
        ) {
            // For http/https, validate standard URL format
            if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
                if (filter_var($trimmed, FILTER_VALIDATE_URL) === false) {
                    $fail("The {$attribute} must be a valid web URL.");
                    return;
                }
            }

            // For tel:, ensure valid phone characters after prefix
            if (str_starts_with($trimmed, 'tel:')) {
                $tel = substr($trimmed, 4);
                if (!preg_match('/^\+?[0-9\-\s\(\)\.]{3,30}$/', $tel)) {
                    $fail("The {$attribute} contains an invalid telephone number format.");
                    return;
                }
            }

            // For mailto:, ensure valid email after prefix
            if (str_starts_with($trimmed, 'mailto:')) {
                $email = substr($trimmed, 7);
                // Strip query string if any e.g. mailto:test@example.com?subject=...
                $emailOnly = explode('?', $email)[0];
                if (!filter_var($emailOnly, FILTER_VALIDATE_EMAIL)) {
                    $fail("The {$attribute} contains an invalid email address format.");
                    return;
                }
            }

            return;
        }

        $fail("The {$attribute} must be a valid URL (http/https), a relative link (/ or #), or a contact scheme (tel: or mailto:).");
    }
}
