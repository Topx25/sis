<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeIconIdentifier implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            $fail("The {$attribute} must be a valid icon name string.");
            return;
        }

        if (strlen($value) > 50) {
            $fail("The {$attribute} may not be greater than 50 characters.");
            return;
        }

        // Only allow safe alphanumeric, hyphens, and underscores (e.g. linkedin, whatsapp, phone, email, arrow-right, mail_outline)
        if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $value)) {
            $fail("The {$attribute} must contain only letters, numbers, dashes, and underscores. HTML or special script characters are not allowed.");
        }
    }
}
