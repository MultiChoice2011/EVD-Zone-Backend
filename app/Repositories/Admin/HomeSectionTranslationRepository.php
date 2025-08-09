<?php

namespace App\Repositories\Admin;

use App\Enums\HomeSectionType;
use App\Helpers\FileUpload;
use App\Models\HomeSectionTranslation;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class HomeSectionTranslationRepository extends BaseRepository
{
    use FileUpload;

    public function __construct(
        Application $app,
        private LanguageRepository $languageRepository
    )
    {
        parent::__construct($app);
    }


    public function storeOrUpdate($requestData, $homeSectionId)
    {
        $languages = $this->languageRepository->getAllLanguages();
        foreach ($languages as $language) {
            $languageId = $language->id;
            $imagePath = null;
            if (isset($requestData->images[$languageId]))
                $imagePath = $requestData->images[$languageId];
            $data = [
                'name' => $requestData->name[$languageId],
                'title' => $requestData->title[$languageId],
                'display' => $requestData->display[$languageId],
                'redirect_url' => ($requestData->type == HomeSectionType::getTypeBanner() && $requestData->display_in == 1)
                        ? $requestData->redirect_url[$languageId]
                        : null,
                'alt_name' => $requestData->type == HomeSectionType::getTypeBanner()
                    ? ($requestData->alt_name[$languageId] ?? $requestData->title[$languageId] ?? null)
                    : null,
            ];
            if ($imagePath)
                $data['image'] = $imagePath;
            $this->model->updateOrCreate(
                [
                    'home_section_id' => $homeSectionId,
                    'language_id' => $languageId,
                ],
                $data
            );
        }
    }


    public function model(): string
    {
        return HomeSectionTranslation::class;
    }
}
