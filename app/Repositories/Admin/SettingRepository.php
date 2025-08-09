<?php

namespace App\Repositories\Admin;

use App\Helpers\FileUpload;
use App\Models\Language;
use App\Models\Setting;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class SettingRepository extends BaseRepository
{
    use FileUpload;

    public function __construct(
        Application $app,
        private SettingTranslationRepository $settingTranslationRepository,
        private CountryRepository $countryRepository,
        private RegionRepository $regionRepository,
        private CityRepository $cityRepository,
        private CurrencyRepository $currencyRepository,
        private LanguageRepository $languageRepository,
    )
    {
        parent::__construct($app);
    }

    public function getSettingByKeyword($key)
    {
        return $this->model->where('key', $key)->value('plain_value');
    }

    public function mainSettings()
    {
        $settings = $this->model
            ->whereIn('key',[
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
                // 'time_zone',
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
    public function updateMainSettings($requestData)
    {
        $defaultLanguages = [];
        $settings = $this->model->all();
        foreach ($settings as $setting) {
            $inputName = $setting->key;
            if(! $requestData->has($setting->key)){
                continue;
            }
            if($setting->is_translatable){
                $this->settingTranslationRepository->store($setting, $requestData);
            }else{
                switch ($setting->key) {
                    case 'main_image':
                        if (isset($requestData->main_image))
                            $setting->plain_value = $requestData->main_image;
                        break;
                    case 'country_id':
                        $relatedModel = $this->countryRepository->show($requestData->{$inputName});
                        if ($relatedModel)
                            $setting->plain_value = $requestData->{$inputName};
                        break;
                    case 'city_id':
                        $relatedModel = $this->cityRepository->show($requestData->{$inputName});
                        if ($relatedModel)
                            $setting->plain_value = $requestData->{$inputName};
                        break;
                    case 'region_id':
                        $relatedModel = $this->regionRepository->show($requestData->{$inputName});
                        if ($relatedModel)
                            $setting->plain_value = $requestData->{$inputName};
                        break;
                    case 'currency_id':
                        $relatedModel = $this->currencyRepository->changeDefaultCurrency($requestData->{$inputName});
                        if ($relatedModel)
                            $setting->plain_value = $requestData->{$inputName};
                        break;
                    case 'dashboard_default_language':
                    case 'website_default_language':
                        $relatedModel = $this->languageRepository->activeLanguage($requestData->{$inputName});
                        if ($relatedModel)
                            $setting->plain_value = $requestData->{$inputName};
                        $defaultLanguages[] = $setting->plain_value;
                        break;
                    default:
                        $setting->plain_value = $requestData->{$inputName};
                        break;
                }
            }
            $setting->save();

        }

        return $defaultLanguages;
    }

    public function updatePricesDisplay($requestData)
    {
        $setting = $this->model->where('key', 'prices_include_tax')->first();
        if (! $setting)
            return false;
        $setting->plain_value = $requestData->prices_include_tax;
        $setting->save();
        return $setting;
    }

    public function updateTaxNumber($requestData)
    {
        $settings = $this->model
            ->whereIn('key',[
                'tax_number',
                'show_tax_number',
                'tax_files'
            ])
            ->get();

        foreach ($settings as $setting) {
            switch ($setting->key) {
                case 'tax_number':
                    $setting->plain_value = $requestData->tax_number;
                    break;
                case 'show_tax_number':
                    $setting->plain_value = $requestData->show_tax_number;
                    break;
                case 'tax_files':
                    $filesPath = [];
                    if (isset($requestData->tax_files)) {
                        foreach ($requestData->tax_files as $taxFile)
                            $filesPath[] = $taxFile;
                    }
                    $setting->plain_value = json_encode($filesPath);
                    break;
                default:
                    break;
            }
            $setting->save();
        }
        return $settings;
    }

    public function getTaxesKeys()
    {
        $settings = $this->model
            ->whereIn('key',[
                'tax_number',
                'prices_include_tax',
                'show_tax_number',
                'tax_files'
            ])
            ->get();

        $data = [];
        foreach ($settings as $setting) {
            if ($setting->key != 'tax_files')
                $data[$setting->key] = $setting->plain_value;
            elseif (isset($setting->plain_value) && is_array(json_decode($setting->plain_value)) && count(json_decode($setting->plain_value)) > 0) {
                foreach (json_decode($setting->plain_value) as $file){
                    $data['tax_files'][] = $this->retrieveFile($file, 'settings');
                }
            }else{
                $data['tax_files'] = [];
            }
        }
        return $data;
    }


    public function model(): string
    {
        return Setting::class;
    }
}
