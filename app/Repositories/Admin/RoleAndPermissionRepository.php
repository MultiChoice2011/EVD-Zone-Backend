<?php

namespace App\Repositories\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Eloquent\BaseRepository;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionRepository extends BaseRepository
{


    public function getAllVendors(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }

    public function store($data_request)
    {
        $vendor = $this->model->create($data_request);
        if ($vendor)
            return $vendor;

        return false;
    }

    public function update($data_request, $vendor_id)
    {
        $vendor = $this->model->find($vendor_id);
        $vendor->update($data_request);
        return $vendor;
    }

    public function show($id)
    {
        return $this->model->find($id);
    }
    public function banned($id)
    {
        $vendor =  $this->model->find($id);
        $vendor->banned = $vendor->banned == 0 ? 1 : 0;
        $vendor->save();
        return $vendor;
    }

    public function destroy($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    /**
     * Vendor Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\Vendor";
    }

    public function addRole(string $roleName, $guard_name): \Illuminate\Database\Eloquent\Builder|Model
    {
        $role = Role::where('name', $roleName)->first();

        if(!$role) {
            $role = Role::create(
                [
                    'guard_name' => $guard_name,
                    'name' => $roleName
                ]);
            return $role;
        }
        return $role;
    }

    public function createPermissions(array $permissions, $guard_name): void
    {
        foreach ($permissions as $permission) {
            if (!Permission::where(
                [
                    'name' => $permission,
                    'guard_name' => $guard_name
                ])->first()){
                Permission::create([
                    'guard_name' => $this->guard_name,
                    'name' => $permission
                ]);
            }
        }
    }
}
