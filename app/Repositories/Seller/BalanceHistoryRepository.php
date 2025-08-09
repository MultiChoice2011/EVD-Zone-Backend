<?php

namespace App\Repositories\Seller;

use App\Models\BalanceHistory;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class BalanceHistoryRepository extends BaseRepository
{
    public function __construct(
        Application $app,
    )
    {
        parent::__construct($app);
    }

    public function store($requestData)
    {
        return $this->model->create($requestData);
    }


    public function model(): string
    {
        return BalanceHistory::class;
    }
}
