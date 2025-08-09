<?php
namespace App\Repositories\Seller;

use App\Models\Language;
use App\Models\Setting;
use App\Repositories\Admin\LanguageRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class SettingRepository extends BaseRepository
{
    public function __construct(public LanguageRepository $languageRepository,public CountryRepository $countryRepository,
        public CityRepository $cityRepository,
        public SettingTranslationRepository $settingTranslationRepository,
        public RegionRepository $regionRepository,public CurrencyRepository $currencyRepository
    ){}
    public function mainSettings()
    {
        $settings = $this->model()
            ::whereIn('key',[
                'store_name',
                'manager_name',
                'phone',
                'email',
                'address_line',
                'main_image',
                'maintenance_mode',
                'dashboard_default_language',
                'website_default_language',
                'country_id',
                'region_id',
                'city_id',
                'currency_id',
                'postal_code',
                'meta_title',
                'meta_description',
                'meta_keywords',
            ])
            ->get();

        $local = app()->getLocale() ??'ar';
        $langId = Language::where('code',$local)->first()->id;
        foreach ($settings as $setting) {
            if ($setting->is_translatable == 1) {
                $setting->translations = $this->settingTranslationRepository->getSettingTranslations($setting->id);
            }
            switch ($setting->key) {
                case 'main_image':
                    if (isset($setting->plain_value))
                        $setting->plain_value = $this->retrieveFile($setting->plain_value, 'settings');
                    break;
                case 'country_id':
                    $relatedModel = $this->countryRepository->show((int)$setting->plain_value);
                    $setting->model = $relatedModel ?? null;
                    break;
                case 'region_id':
                    $relatedModel = $this->regionRepository->show((int)$setting->plain_value);
                    $setting->model = $relatedModel ?? null;
                    break;
                case 'city_id':
                    $relatedModel = $this->cityRepository->show((int)$setting->plain_value);
                    $setting->model = $relatedModel ?? null;
                    break;
                case 'currency_id':
                    $relatedModel = $this->currencyRepository->show((int)$setting->plain_value);
                    $setting->model = $relatedModel ?? null;
                    break;
                case 'dashboard_default_language':
                case 'website_default_language':
                    $relatedModel = $this->languageRepository->show((int)$setting->plain_value);
                    $setting->model = $relatedModel ?? null;
                    break;
                default:
                    $setting->model = null;
                    break;
            }
        }

        return $settings;
    }
    public function getTaxesKeys()
    {
        $settings = $this->model()::whereIn('key',[
                'tax_number',
                'show_tax_number'
            ])
            ->get();

        $data = [];
        foreach ($settings as $setting) {
            if ($setting->key == 'show_tax_number')
                $data[$setting->key] = (int)$setting->plain_value;
            else
                $data[$setting->key] = $setting->plain_value;
        }
        return $data;
    }
    public function getSettingByKeyword($key)
    {
        return Setting::where('key', $key)->value('plain_value');
    }
    public function model() : String
    {
        return Setting::class;
    }
}
