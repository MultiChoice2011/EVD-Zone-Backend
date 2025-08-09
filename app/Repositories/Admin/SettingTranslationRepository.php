<?php

namespace App\Repositories\Admin;

use App\Models\Language;
use App\Models\Setting;
use App\Models\SettingTranslation;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class SettingTranslationRepository extends BaseRepository
{
    public function __construct(Application $app, private LanguageRepository $languageRepository)
    {
        parent::__construct($app);
    }

    public function getSettingTranslate($settingId, $langId)
    {
        $settingTraslate =  $this->model
            ->where('setting_id', $settingId)
            ->where('language_id', $langId)
            ->first();

        return $settingTraslate->value;
    }

    public function getSettingTranslations($settingId)
    {
        return $this->model
            ->where('setting_id', $settingId)
            ->get();
    }

    public function store(Setting $setting, $requestData)
    {
        $languages = $this->languageRepository->getAllLanguages();
        foreach ($languages as $language) {
            $languageId = $language->id;
            $inputName = $setting->key;
            $this->model->updateOrCreate(
                [
                    'setting_id' => $setting->id,
                    'language_id' => $languageId,
                ],
                [
                    'setting_id' => $setting->id,
                    'language_id' => $languageId,
                    'value' => $requestData->{$inputName}[$languageId] ?? null,
                ]
            );
        }
    }


    public function model(): string
    {
        return SettingTranslation::class;
    }
}
