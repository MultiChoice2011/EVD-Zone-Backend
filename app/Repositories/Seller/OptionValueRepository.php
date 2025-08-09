<?php

namespace App\Repositories\Seller;

use App\Models\OptionValue;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class OptionValueRepository extends BaseRepository
{
    public function __construct(Application $app)
    {
        parent::__construct($app);

    }

    public function optionValueIds($ids, $optionId)
    {
        return $this->model
            ->whereIn('id', $ids)
            ->where('option_id', $optionId)
            ->pluck('id')
            ->toArray();
    }




    public function model(): string
    {
        return OptionValue::class;
    }
}
