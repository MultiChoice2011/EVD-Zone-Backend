<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RedirectUrlRequired implements ValidationRule
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ((isset($this->data['display_in']) && $this->data['display_in'] == 1) ||
            (isset($this->data['type']) && $this->data['type'] == 'banner')) {
            if (empty($value)) {
                $fail(__('validation.required'));
            }
        }
    }
}
