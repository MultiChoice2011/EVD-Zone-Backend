<?php
namespace App\Repositories\Admin;

use App\Models\BankTranslation;

class BankTranslationRepository
{
    public function __construct(public LanguageRepository $languageRepository){}
    public function storeOrUpdate($requestData, $bankId)
    {
        $languages = $this->languageRepository->getAllLanguages();
        foreach ($languages as $language) {
            $languageId = $language->id;
            $this->getModel()::updateOrCreate(
                [
                    'bank_id' => $bankId,
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
    private function getModel(): String
    {
        return BankTranslation::class;
    }

}
