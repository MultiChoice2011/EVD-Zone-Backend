<?php
namespace App\Repositories\Seller;

use App\Models\City;
use PhpParser\Node\Expr\Cast\String_;

class CityRepository
{
    public function getAllCities()
    {
        return $this->getModel()::with('translations')->get();
    }
    public function getCitiesByRegion($regionId)
    {
        return $this->getModel()::with('translations')->where('region_id',$regionId)->get();
    }
    public function show($id)
    {
        return $this->getModel()::where('id',$id)->first();
    }
    private function getModel() : String
    {
        return City::class;
    }
}

