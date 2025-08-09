<?php
namespace App\Repositories\Seller;

use App\Models\RoleTranslation;
use App\Repositories\Admin\LanguageRepository;

class RoleTranslationRepository
{
    public function __construct(public LanguageRepository $languageRepository){}
    public function store($requestData, $id)
    {
        $languages = $this->languageRepository->getAllLanguages();
        foreach ($languages as $language) {
            $languageId = $language->id;
            $this->getModel()::updateOrCreate(
                [
                    'role_id' => $id,
                    'language_id' => $languageId,
                ],
                [
                'display_name' => $requestData->display_name[$languageId],
                ]
            );
        }
        return true;
    }
    private function getModel() : String
    {
        return RoleTranslation::class;
    }
}
