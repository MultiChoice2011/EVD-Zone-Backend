<?php
namespace App\Repositories\Seller;

use App\Models\SettingTranslation;

class SettingTranslationRepository
{
    public function getSettingTranslate($settingId, $langId)
    {
        $settingTranslate =  $this->getModel()::where('setting_id', $settingId)
            ->where('language_id', $langId)
            ->first();

        return $settingTranslate->value;
    }
    public function getSettingTranslations($settingId)
    {
        return $this->getModel()
            ::where('setting_id', $settingId)
            ->get();
    }
    private function getModel(): string
    {
        return SettingTranslation::class;
    }
}
