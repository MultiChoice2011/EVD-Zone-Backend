<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\Seller\CountryService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function __construct(public CountryService $countryService){}
    public function index()
    {
        return $this->countryService->getAllCountries();
    }
}
