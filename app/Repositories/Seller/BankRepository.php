<?php
namespace App\Repositories\Seller;

use App\Models\Bank;

class BankRepository
{
    public function getAllBanks()
    {
        return $this->getModel()::with('translations')->get();
    }
    public function getBankWithCountries()
    {
        $authCountry = auth('sellerApi')->user()->sellerAddress->country_id;
        $banksWithCountry = $this->getModel()::whereHas('countries', function ($query) use ($authCountry) {
            $query->where('country_id',$authCountry);
        })->with('translations')->get();
        return $banksWithCountry;
    }
    public function getModeById($id)
    {
        return $this->getModel()::find(intval($id));
    }
    private function getModel() : String
    {
        return Bank::class;
    }
}
