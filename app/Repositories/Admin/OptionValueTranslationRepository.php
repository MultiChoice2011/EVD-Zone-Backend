<?php

namespace App\Repositories\Admin;

use Illuminate\Container\Container as Application;
use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class OptionValueTranslationRepository extends BaseRepository
{
    public function __construct(
        Application $app,
        private LanguageRepository $languageRepository
    )
    {
        parent::__construct($app);
    }

    public function store($data_request, $optionValue__id)
    {
        $languages = $this->languageRepository->getAllLanguages();
        foreach ($languages as $language) {
            $languageId = $language->id;
            $this->model->create([
                'option_value_id' => $optionValue__id,
                'language_id' => $languageId,
                'name' => $data_request['name'][$languageId],
            ]);
        }

        return true;
    }

    public function deleteByOptionValueId($optionValue__id)
    {
        return $this->model->where('option_value_id',$optionValue__id)->delete();
    }
    /**
     * OptionValue Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\OptionValueTranslation";
    }
}
