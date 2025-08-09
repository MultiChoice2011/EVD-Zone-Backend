<?php

namespace App\Repositories\Admin;

use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class PermissionRepository extends BaseRepository
{

    public function index()
    {
        $currentGuard = Auth::getDefaultDriver();
        return $this->model->where('guard_name', $currentGuard)->get();
    }

    public function model(): string
    {
        return Permission::class;
    }
}
