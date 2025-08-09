<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CurrencyRequest;
use App\Services\Admin\CurrencyService;
use Illuminate\Http\Request;
use Prettus\Validator\Exceptions\ValidatorException;


class CurrencyController extends Controller
{
    public $currencyService;

    /**
     * Currency  Constructor.
     */
    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }


    /**
     * All Cats
     */
    public function index(Request $request)
    {
        return $this->currencyService->getAllCurrencies($request);
    }

    /**
     * create currency page
     */
    public function create()
    {
        return $this->currencyService->create();
    }

    /**
     *  Store Currency
     */
    public function store(CurrencyRequest $request)
    {

        return $this->currencyService->storeCurrency($request);
    }

    /**
     * show the currency..
     *
     */
    public function show(int $id)
    {
        return $this->currencyService->show($id);
    }
    /**
     * show the currency..
     *
     */
    public function get_currency()
    {
        return $this->currencyService->get_currency();
    }

    /**
     * edit the currency..
     *
     */
    public function edit(int $id)
    {
        return $this->currencyService->edit($id);

    }

    /**
     * Update the currency..
     *
     * @throws ValidatorException
     */
    public function update(CurrencyRequest $request, int $id)
    {
        return $this->currencyService->updateCurrency($request, $id);
    }

    /**
     *
     * Delete Currency Using ID.
     *
     */
    public function destroy(int $id)
    {
        return $this->currencyService->deleteCurrency($id);

    }

}
