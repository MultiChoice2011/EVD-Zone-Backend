<?php

namespace App\Repositories\Admin;

use App\Models\ValueAddedTax;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class ValueAddedTaxRepository extends BaseRepository
{
    public function __construct(
        Application $app,
    )
    {
        parent::__construct($app);
    }

    public function index()
    {
        return $this->model->with(['country'])->get();
    }

    public function storeTax($requestData)
    {
        return $this->model->create([
            'country_id' => $requestData->country_id,
            'tax_rate' => $requestData->tax_rate,
        ]);
    }

    public function updateTax($requestData, $id)
    {
        $tax = $this->model->where('id', $id)->first();
        if (! $tax)
            return false;
        $tax->country_id = $requestData->country_id;
        $tax->tax_rate = $requestData->tax_rate;
        $tax->save();
        return $tax;
    }

    public function changeStatus($requestData, $id)
    {
        $tax = $this->model->where('id', $id)->first();
        if(!$tax){
            return false;
        }
        // change status
        $tax->status = $requestData->status;
        $tax->save();

        return $tax;
    }

    public function show($id)
    {
        return $this->model->where('id', $id)->active()->first();
    }

    public function deleteTax($id)
    {
        $tax = $this->model->where('id',$id)->first();
        if(! $tax){
            return false;
        }
        $tax->forceDelete();
        return true;
    }



    public function model(): string
    {
        return ValueAddedTax::class;
    }
}
