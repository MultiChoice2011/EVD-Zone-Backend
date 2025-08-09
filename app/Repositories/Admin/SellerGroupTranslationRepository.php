<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Str;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerGroupTranslationRepository extends BaseRepository
{

    public function __construct(
        Application $app,
        private LanguageRepository $languageRepository
    )
    {
        parent::__construct($app);
    }

    public function store($requestData, $sellerGroupId)
    {
        $languages = $this->languageRepository->getAllLanguages();
        foreach ($languages as $language) {
            $languageId = $language->id;
            $this->model->updateOrCreate(
                [
                    'seller_group_id' => $sellerGroupId,
                    'language_id' => $languageId,
                ],
                [
                    'name' => $requestData['name'][$languageId],
                    'description' => $requestData['description'][$languageId] ?? null,
                ]
            );
        }
        return true;
    }

    public function deleteBySellerGroupId($sellerGroup_id)
    {
        return $this->model->where('seller_group_id',$sellerGroup_id)->delete();
    }
    /**
     * SellerGroup Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\SellerGroupTranslation";
    }
}
