<?php

namespace App\Repositories\Admin;

use App\Models\SellerTranslations;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerTranslationRepository extends BaseRepository
{
    private $languageRepository;
    public function __construct(Application $app, LanguageRepository $languageRepository)
    {
        parent::__construct($app);
        $this->languageRepository = $languageRepository;

    }

    public function store($requestData, $sellerId)
    {
        $languages = $this->languageRepository->getAllLanguages();
        foreach ($languages as $language) {
            $languageId = $language->id;
            $this->model->updateOrCreate(
                [
                    'seller_id' => $sellerId,
                    'language_id' => $languageId,
                ],
                [
                'seller_id' => $sellerId,
                'language_id' => $languageId,
                'reject_reason' => $requestData->reject_reason[$languageId],
                ]
            );
        }
        return true;
    }

    public function deleteById($id)
    {
        return $this->model->where('seller_id',$id)->delete();
    }


    public function model(): string
    {
        return SellerTranslations::class;
    }
}
