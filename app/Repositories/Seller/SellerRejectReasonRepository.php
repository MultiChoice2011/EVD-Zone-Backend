<?php

namespace App\Repositories\Seller;

use App\Models\SellerRejectReason;
use App\Models\SellerTranslations;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerRejectReasonRepository extends BaseRepository
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function makeResolved($sellerId)
    {
        $this->model->where('seller_id', $sellerId)->update(['resolved_at' => now()]);
        return true;
    }


    public function model(): string
    {
        return SellerRejectReason::class;
    }
}
