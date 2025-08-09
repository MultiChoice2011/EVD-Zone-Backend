<?php

namespace App\Repositories\Admin;

use App\Helpers\FileUpload;
use App\Models\SliderTranslation;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class SliderTranslationRepository extends BaseRepository
{
    use FileUpload;

    public function __construct(
        Application $app,
        private LanguageRepository $languageRepository
    )
    {
        parent::__construct($app);
    }


    public function storeOrUpdate($requestData, $sliderId)
    {
        $languages = $this->languageRepository->getAllLanguages();
        foreach ($languages as $language) {
            $languageId = $language->id;
            $imagePath = null;
            if (isset($requestData->images[$languageId]))
                $imagePath = $requestData->images[$languageId];
            Log::info($imagePath);
            $data = [
                'title' => $requestData->title[$languageId] ?? null,
                'description' => $requestData->description[$languageId] ?? null,
                'redirect_url' => $requestData->redirect_url[$languageId] ?? null,
                'alt_name' => $requestData->alt_name[$languageId] ?? $requestData->title[$languageId] ?? null,
            ];
            if ($imagePath)
                $data['image'] = $imagePath;
            $this->model->updateOrCreate(
                [
                    'slider_id' => $sliderId,
                    'language_id' => $languageId,
                ],
                $data
            );
        }
    }


    public function model(): string
    {
        return SliderTranslation::class;
    }
}
