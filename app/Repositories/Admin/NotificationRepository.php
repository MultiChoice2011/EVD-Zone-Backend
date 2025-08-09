<?php

namespace App\Repositories\Admin;

use App\Models\Notification;
use App\Models\User;
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


    public function index($adminId)
    {
        return $this->model
            ->where('notifiable_id', $adminId)
            ->where('notifiable_type', User::class)
            ->orderBy('created_at', 'desc')
            ->paginate(PAGINATION_COUNT_APP);
    }

    public function NotificationsCount($adminId)
    {
        $admin = User::where('id', $adminId)->first();
        return $admin->notifications()->where('read_at', null)->count();
    }

    public function read($id, $adminId)
    {
        $notification = $this->model
            ->where('id', $id)
            ->where('notifiable_id', $adminId)
            ->where('notifiable_type', User::class)
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
