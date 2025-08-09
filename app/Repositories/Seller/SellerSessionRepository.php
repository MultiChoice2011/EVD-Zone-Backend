<?php
namespace  App\Repositories\Seller;
use App\Models\SellerSession;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerSessionRepository extends BaseRepository
{
    public function store($requestData)
    {
        return $this->model->create($requestData);
    }

    public function lastSession($sellerId)
    {
        return $this->model->where('seller_id', $sellerId)
            ->whereNull('expired_at')
            ->latest('created_at')
            ->first();
    }


    public function model(): string
    {
        return SellerSession::class;
    }
}
