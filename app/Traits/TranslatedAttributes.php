<?php
namespace App\Traits;

use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

trait TranslatedAttributes
{
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        $hiddenAttributes = $this->getHidden();

        $locale = app()->getLocale() ?? 'ar';
        $langId = session('client_lang') ?? Language::where('code', $locale)->first()->id;

        $tableTranslations = $this->translations()->where('language_id', $langId)->first();

        foreach ($this->translatedAttributes as $field) {
            if (in_array($field, $hiddenAttributes)) {
                continue;
            }

            $attributes[$field] = $tableTranslations?->{$field};
        }

        return $attributes;
    }

    public function getAttribute($key)
    {
        if (in_array($key, $this->translatedAttributes)) {
            $locale = app()->getLocale() ?? 'ar';
            $langId = session('client_lang') ?? Language::where('code', $locale)->first()->id;

            $translation = $this->translations()->where('language_id', $langId)->first();

            if ($translation) {
                return $translation->{$key};
            }
        }

        return parent::getAttribute($key);
    }
}
