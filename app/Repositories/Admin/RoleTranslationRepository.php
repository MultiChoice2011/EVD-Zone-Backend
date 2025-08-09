<?php

namespace App\Repositories\Admin;

use App\Models\RoleTranslation;
use App\Models\SellerTranslations;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class RoleTranslationRepository extends BaseRepository
{
    public function __construct(
        Application $app,
        private LanguageRepository $languageRepository
    )
    {
        parent::__construct($app);
    }

    public function store($requestData, $id)
    {
        $languages = $this->languageRepository->getAllLanguages();
        foreach ($languages as $language) {
            $languageId = $language->id;
            $this->model->updateOrCreate(
                [
                    'role_id' => $id,
                    'language_id' => $languageId,
                ],
                [
                'seller_id' => $id,
                'language_id' => $languageId,
                'display_name' => $requestData->display_name[$languageId],
                ]
            );
        }
        return true;
    }

    public function deleteById($id)
    {
        return $this->model->where('role_id',$id)->delete();
    }


    public function model(): string
    {
        return RoleTranslation::class;
    }
}
