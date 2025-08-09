<?php

namespace App\Repositories\Admin;

use App\Helpers\FileUpload;
use App\Models\BrandImage;
use App\Models\ProductImage;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;
class BrandImageRepository extends BaseRepository
{
    use FileUpload;
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }


    public function storeOrUpdate($requestData, $brandId)
    {
        if (isset($requestData->images_data))
            foreach ($requestData->images_data as $imageData) {
                $this->model->updateOrCreate(
                    [
                        'brand_id' => $brandId,
                        'key' => $imageData['key'],
                    ],
                    [
                        'image' => $imageData['image'],
                    ]
                );
            }
    }





    public function model(): string
    {
        return BrandImage::class;
    }
}
