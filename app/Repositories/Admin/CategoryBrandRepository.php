<?php

namespace App\Repositories\Admin;

use App\Enums\GeneralStatusEnum;
use App\Models\Category;
use App\Models\CategoryBrand;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class CategoryBrandRepository extends BaseRepository
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function updateRelatedBrands(Category $category, array $brandIds)
    {
        $category->brands()->sync($brandIds);
        return true;
    }


    public function model(): string
    {
        return CategoryBrand::class;
    }
}
