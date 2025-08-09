<?php
namespace App\Repositories\Seller;

use App\Models\Currency;
use Prettus\Repository\Eloquent\BaseRepository;

class CurrencyRepository extends BaseRepository
{
    public function getAllCurrencies()
    {
        return $this->model->with('translations')->get();
    }
    public function show($currency_id)
    {
        return $this->model->with('translations')->find($currency_id);
    }

    public function storeDefault($requestData, $authSeller)
    {
        $currency =  $this->model
            ->where('id', $requestData->currency_id)
            ->active()
            ->first();
        if (! $currency){
            return false;
        }
        $authSeller->currency()->associate($currency);
        $authSeller->save();
        return true;
    }


    public function model(): string
    {
        return Currency::class;
    }
}
