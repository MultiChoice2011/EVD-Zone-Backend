<?php

namespace App\Repositories\Admin;

use App\Models\StaticPage;
use App\Models\StaticPageTranslation;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class StaticPageTranslationRepository extends BaseRepository
{
    public function __construct(Application $app, private LanguageRepository $languageRepository)
    {
        parent::__construct($app);
    }


    public function store(StaticPage $page, $requestData)
    {
        $languages = $this->languageRepository->getAllLanguages();
        foreach ($languages as $language) {
            $languageId = $language->id;
            $this->model->updateOrCreate(
                [
                    'static_page_id' => $page->id,
                    'language_id' => $languageId,
                ],
                [
                    'static_page_id' => $page->id,
                    'language_id' => $languageId,
                    'title' => $requestData->title[$languageId],
                    'meta_title' => $requestData->meta_title[$languageId],
                    'meta_description' => $requestData->meta_description[$languageId],
                    'meta_keywords' => $requestData->meta_keywords[$languageId],
                    'content' => $requestData->content[$languageId],
                ]
            );
        }
    }

    public function deleteByStaticPageId($staticPageId)
    {
        return $this->model->where('static_page_id',$staticPageId)->delete();
    }



    public function model(): string
    {
        return StaticPageTranslation::class;
    }
}
