<?php

namespace App\Repositories\Admin;

use Illuminate\Container\Container as Application;
use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class CategoryTranslationRepository extends BaseRepository
{
    public function __construct(
        Application $app,
        private LanguageRepository $languageRepository
    )
    {
        parent::__construct($app);
    }


    public function storeOrUpdate($requestData, $categoryId)
    {
        $languages = $this->languageRepository->getAllLanguages();
        foreach ($languages as $language) {
            $languageId = $language->id;
            $this->model->updateOrCreate(
                [
                    'category_id' => $categoryId,
                    'language_id' => $languageId,
                ],
                [
                    'name' => $requestData['name'][$languageId],
                    'description' => $requestData['description'][$languageId] ?? null,
                    'meta_title' => $requestData['meta_title'][$languageId],
                    'meta_keyword' => $requestData['meta_keyword'][$languageId] ?? null,
                    'meta_description' => $requestData['meta_description'][$languageId] ?? null,
                ]
            );
        }
        return true;
    }

    public function deleteByCategoryId($category_id)
    {
        return $this->model->where('category_id',$category_id)->delete();
    }
    /**
     * Category Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\CategoryTranslation";
    }
}
