<?php
namespace App\Repositories\Seller;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;

class CountryRepository
{
    public function getAllCountries()
    {
        return $this->getModel()::with('translations')->get();
    }
    public function show($id)
    {
        return $this->getModel()::where('id',$id)->first();
    }

    public function showCurrencyRelatedById($id): Model|null
    {
        return $this->getModel()::query()
            ->where('id', $id)
            ->whereHas('currency', function ($query) {
                $query->active();
            })
            ->with('currency')
            ->first();
    }

    private function getModel()
    {
        return Country::class;
    }
}
