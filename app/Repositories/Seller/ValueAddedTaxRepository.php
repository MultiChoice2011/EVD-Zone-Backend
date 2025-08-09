<?php

namespace App\Repositories\Seller;

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

    public function show($id)
    {
        return $this->model->where('id', $id)->active()->first();
    }


    public function model(): string
    {
        return ValueAddedTax::class;
    }
}
