<?php

namespace App\Repositories\Seller;

use App\Models\Notification;
use App\Models\Seller;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class NotificationRepository extends BaseRepository
{

    public function __construct(
        Application $app,
    )
    {
        parent::__construct($app);
    }


    public function index($sellerId)
    {
        return $this->model
            ->where('notifiable_id', $sellerId)
            ->where('notifiable_type', Seller::class)
            ->orderBy('created_at', 'desc')
            ->paginate(PAGINATION_COUNT_APP);
    }

    public function NotificationsCount($sellerId)
    {
        $customer = Seller::where('id', $sellerId)->first();
        return $customer->notifications()->where('read_at', null)->count();
    }

    public function read($id, $sellerId)
    {
        $notification = $this->model
            ->where('id', $id)
            ->where('notifiable_id', $sellerId)
            ->where('notifiable_type', Seller::class)
            ->first();
        if (! $notification)
            return false;
        $notification->read_at = now();
        $notification->save();
        return true;
    }

    public function showByKey($key)
    {
        return $this->model
            ->where('key', $key)
            ->first();
    }


    public function model(): string
    {
        return Notification::class;
    }
}
