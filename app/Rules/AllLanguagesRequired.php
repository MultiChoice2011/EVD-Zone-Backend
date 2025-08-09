<?php

namespace App\Rules;

use App\Enums\GeneralStatusEnum;
use App\Models\Language;
use App\Models\Region;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AllLanguagesRequired implements ValidationRule
{
    protected $languageIds;
    public function __construct()
    {
        $this->languageIds = Language::where('status', GeneralStatusEnum::getStatusActive())->pluck('id')->toArray();
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->languageIds as $id) {
            if (!array_key_exists($id, $value)) {
                $fail(__('validation.all_languages_required', ['id' => $id]));
            }
        }
    }
}
