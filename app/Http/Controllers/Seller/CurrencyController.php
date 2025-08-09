<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\CurrencyRequests\DefaultCurrencyRequest;
use App\Services\Seller\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct(public CurrencyService $currencyService){}
    public function index()
    {
        return $this->currencyService->index();
    }
    public function storeDefault(DefaultCurrencyRequest $request)
    {
        return $this->currencyService->storeDefault($request);
    }
}
