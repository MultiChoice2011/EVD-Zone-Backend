<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\Seller\RegionService;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function __construct(public RegionService $regionService){}
    public function index(Request $request)
    {
        return $this->regionService->index($request);
    }
    public function getRegionsByCountry($countryId)
    {
        return $this->regionService->getRegionsByCountry($countryId);
    }
}
