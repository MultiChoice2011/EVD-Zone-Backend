<?php

namespace App\Repositories\Option;

use App\Models\ProductOption;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Prettus\Repository\Eloquent\BaseRepository;

class ProductOptionRepository extends BaseRepository
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }


    public function show($id)
    {
        return $this->model
            ->where('id', $id)
            ->first();
    }

    public function model(): string
    {
        return ProductOption::class;
    }
}
