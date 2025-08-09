<?php

namespace App\Rules;

use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

class CloudinaryUrlValidatorRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Basic URL validation
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $fail(__('validation.url'));
            return;
        }

        // Parse the URL
        $parsedUrl = parse_url($value);

        // Check domain
        if (!in_array($parsedUrl['host'], [
            'res.cloudinary.com',
            'cloudinary.com',
        ])) {
            $fail(__('validation.url'));
            return;
        }

        // Check file extension
        $extension = pathinfo($value, PATHINFO_EXTENSION);

        // Extensions validation
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'xlx', 'csv', 'docx'];
        if ($extension && !in_array(strtolower($extension), $allowedExtensions)) {
            $fail(__('validation.url'));
        }
    }
}
