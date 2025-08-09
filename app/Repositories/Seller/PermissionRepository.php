<?php
namespace App\Repositories\Seller;

use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class PermissionRepository
{
    public function index()
    {
        $currentGuard = Auth::getDefaultDriver();
        return $this->Model()::with('translations')->where('guard_name',$currentGuard)->get();
    }

    public function model(): string
    {
        return Permission::class;
    }
}
