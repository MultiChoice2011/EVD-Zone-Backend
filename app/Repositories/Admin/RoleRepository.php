<?php

namespace App\Repositories\Admin;

use App\Models\Role;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class RoleRepository extends BaseRepository
{
    public function __construct(
        Application $app,
        private RoleTranslationRepository $roleTranslationRepository
    )
    {
        parent::__construct($app);
    }

    public function getRolesPaginate($requestData)
    {
        $currentGuard = Auth::getDefaultDriver();
        // Default to search value
        $searchTerm = $requestData->input('search', '');
        // Build the base query
        $query = $this->model->query();
        $query->with(['permissions', 'translations']);
        // Apply searching
        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }
        // Retrieve paginated results
        return $query->where('guard_name', $currentGuard)
            ->where('name', '<>','Super Admin')
            ->latest()
            ->paginate(PAGINATION_COUNT_ADMIN);
    }
    public function getOneRole($id)
    {
        $currentGuard = Auth::getDefaultDriver();
        return $this->model
            ->with(['translations', 'permissions'])
            ->where('guard_name', $currentGuard)
            ->where('name', '<>','Super Admin')
            ->where('id', $id)
            ->first();
    }
    public function getAllRoles()
    {
        $currentGuard = Auth::getDefaultDriver();
        return $this->model
            ->with(['permissions','translations'])
            ->where('guard_name', $currentGuard)
            ->where('name', '<>','Super Admin')
            ->get();
    }
    public function store($requestData)
    {
        $currentGuard = Auth::getDefaultDriver();
        $role = $this->model->create([
            'name' => $requestData->name,
            'guard_name' => $currentGuard
        ]);
        // store role translations
        if ($role) {
            $this->roleTranslationRepository->store($requestData, $role->id);
            $role->syncPermissions($requestData->permissions);
        }
        return $role;
    }
    public function updateRole($requestData, $id)
    {
        $role = $this->getOneRole($id);
        if (! $role)
            return false;
        $role->name = $requestData->name;
        $this->roleTranslationRepository->store($requestData, $role->id);
        $role->syncPermissions($requestData->permissions);
        $role->save();
        return $role;
    }
    public function changeStatus($requestData, $id)
    {
        $role = $this->model
            ->where('id', $id)
            ->where('name', '<>', 'Super Admin')
            ->first();
        if(!$role){
            return false;
        }
        // change status
        $role->status = $requestData->status;
        $role->save();

        return $role;
    }
    public function deleteRole($id)
    {
        $role = $this->getOneRole($id);
        if (! $role)
            return false;
        $role->delete();
        return true;
    }


    public function model()
    {
        return Role::class;
    }



}
