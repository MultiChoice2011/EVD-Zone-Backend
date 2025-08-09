<?php

namespace App\Rules;

use App\Models\Region;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ValidConditionKeys implements ValidationRule
{
    public function __construct()
    {}
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

    }
}
