<?php

namespace App\Repositories\Admin;

use App\Models\OrderComplaintUser;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class OrderComplaintUserRepository extends BaseRepository
{
    public function __construct(
        Application                          $app,
    )
    {
        parent::__construct($app);
    }


    public function showByOrderComplaintId($orderComplaintId)
    {
        return $this->model->where('order_Complaint_id', $orderComplaintId)->first();
    }

    public function checkByOrderComplaintIdAndUserId($orderComplaintId, $userId)
    {
        return $this->model
            ->where('order_complaint_id', $orderComplaintId)
            ->where('user_id', $userId)
            ->first();
    }

    public function store($orderComplaintId, $userId)
    {
        return $this->model->create([
            'order_complaint_id' => $orderComplaintId,
            'user_id' => $userId,
        ]);
    }


    public function model(): string
    {
        return OrderComplaintUser::class;
    }
}
