<?php

namespace App\Repositories\Admin;

use App\Enums\GeneralStatusEnum;
use App\Services\General\CurrencyService;
use Laravel\Telescope\Telescope;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CurrencyRepository extends BaseRepository
{

    public function __construct(
        Application $app ,
        private CurrencyTranslationRepository $currencyTranslationRepository,
        private LanguageRepository $languageRepository,
    )
    {
        parent::__construct($app);
        Log::info("line 22");
    }

    public function getAllCurrencies(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->with(['translations','country'])->orderByDesc('id')->get();
    }


    public function store($requestData)
    {
        $currency =  $this->model->create($requestData->all());
        if ($currency){
            $this->currencyTranslationRepository->storeOrUpdate($requestData, $currency->id);
        }

        return $currency->load('translations');
    }

    public function defaultCurrency()
    {
        $authSeller = Auth::guard('sellerApi')->user();
        return CurrencyService::getCurrentCurrency($authSeller);
    }

    public function changeDefaultCurrency($id)
    {
        Log::info("line 50");
        $currency = $this->model
            ->where('id', $id)
            ->where('status', GeneralStatusEnum::getStatusActive())
            ->first();
        if (!$currency){
            return false;
        }
        $this->model
            ->where('is_default', 1)
            ->where('id', '!=', $currency->id)
            ->update(['is_default' => 0]);
        $currency->update(['is_default' => 1]);
        Telescope::tag(function ($entry) use($currency) {
            return ['currency'.$currency->id];
        });
        return true;
    }

    public function show($currency_id)
    {
        return $this->model->with('translations')->find($currency_id);
    }

    public function showByLanguageCode($langCode)
    {
        $lang = $this->languageRepository->getLangByCode($langCode);
        return $this->model
        ->where('is_default', 1)
        ->with(['translations' => function ($query) use($lang){
            $query->where('language_id', $lang->id)->first();
        }])
        ->first();
    }

    public function get_currency()
    {
        return $this->model->where('id',1)->first();
    }

    public function updateCurrency($requestData, $currencyId)
    {
        $currency = $this->model->find($currencyId);
        if($currency?->is_default == 1){
            $requestData->status = 1;
        }
        $currency->update([
            'value' => $requestData->value,
            'decimal_place' => $requestData->decimal_place ?? $currency->decimal_place,
            'status' => $requestData->status,
            'country_id' => $requestData->country_id
        ]);
        $this->currencyTranslationRepository->storeOrUpdate($requestData, $currency->id);
        return $currency->load('translations');
    }

    public function destroy($currency_id)
    {
        return $this->model->find($currency_id)->delete();
    }

    /**
     * Currency Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\Currency";
    }
}
