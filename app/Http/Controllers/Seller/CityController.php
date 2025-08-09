<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\Seller\CityService;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function __construct(public CityService $cityService){}
    public function index()
    {
        return $this->cityService->getAllCities();
    }
    public function getCitiesByRegion($regionId)
    {
        return $this->cityService->getCitiesByRegion($regionId);
    }
}
