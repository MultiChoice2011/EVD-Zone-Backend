<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TaxRequests\TaxRequest;
use App\Http\Requests\Admin\TaxRequests\TaxStatusRequest;
use App\Http\Requests\Admin\TaxRequests\UpdatePricesDisplayRequest;
use App\Http\Requests\Admin\TaxRequests\UpdateTaxNumberRequest;
use App\Services\Admin\ValueAddedTaxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValueAddedTaxController extends Controller
{

    public function __construct(private ValueAddedTaxService $valueAddedTaxService)
    {}

    public function index()
    {
        return $this->valueAddedTaxService->index();
    }

    public function store(TaxRequest $request)
    {
        return $this->valueAddedTaxService->store($request);
    }

    public function update(TaxRequest $request, int $id)
    {
        return $this->valueAddedTaxService->update($request, $id);
    }

    public function changeStatus(TaxStatusRequest $request, int $id)
    {
        return $this->valueAddedTaxService->changeStatus($request, $id);
    }

    public function delete(int $id)
    {
        return $this->valueAddedTaxService->delete($id);
    }


    public function updatePricesDisplay(UpdatePricesDisplayRequest $request)
    {
        return $this->valueAddedTaxService->updatePricesDisplay($request);
    }

    public function updateTaxNumber(UpdateTaxNumberRequest $request)
    {
        return $this->valueAddedTaxService->updateTaxNumber($request);
    }



}
