<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hasUppercase = preg_match('/[A-Z]/', $value);
        $hasLowercase = preg_match('/[a-z]/', $value);
        $hasDigit = preg_match('/\d/', $value);
        $hasSpecialChar = preg_match('/[@$!%*?&#]/', $value);
        $minLength = strlen($value) >= 8;

        // Enforce strength based on these factors
        $strength = $hasUppercase + $hasLowercase + $hasDigit + $hasSpecialChar + $minLength;

        if ($strength < 4) {
            $fail('The password must be stronger. Include uppercase, lowercase, digits, special characters, and at least 8 characters.');
        }
    }
}
