<?php
namespace App\Repositories\Seller;

use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class RoleRepository
{
    public function __construct(public RoleTranslationRepository $roleTranslationRepository){}
    public function getAllRoles($requestData)
    {
        $authSeller = Auth::guard('sellerApi')->user();
        $currentGuard = Auth::getDefaultDriver();
        // Default to search value
        $searchTerm = $requestData->input('name', '');
        // Build the base query
        $query = $this->getModel()::query();
        $query->with(['translations','permissions']);
        // Apply searching
        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }
        // Retrieve paginated results
        return $query->where('guard_name', $currentGuard)
            ->where('name', '<>','Super Seller')
            ->latest()
            ->forSeller($authSeller->id)
            ->get();
    }
    public function store($requestData)
    {
        $authSeller = Auth::guard('sellerApi')->user();
        $currentGuard = Auth::getDefaultDriver();
        $role = $this->getModel()::create([
            'name' => $requestData->name,
            'guard_name' => $currentGuard,
            'status' => $requestData->status,
            'seller_id' => $authSeller->id,
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
        $role->status = $requestData->status;
        $this->roleTranslationRepository->store($requestData, $role->id);
        $role->syncPermissions($requestData->permissions);
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
    public function getOneRole($id)
    {
        $authSeller = Auth::guard('sellerApi')->user();
        $currentGuard = Auth::getDefaultDriver();
        return $this->getModel()::with(['translations','permissions'])
            ->where('guard_name', $currentGuard)
            ->where('id', $id)
            ->forSeller($authSeller->id)
            ->first();
    }
    public function changeStatus($requestData, $id)
    {
        $authSeller = Auth::guard('sellerApi')->user();
        $role = $this->getModel()::where('id', $id)
            ->where('name', '<>', 'Super Admin')
            ->forSeller($authSeller->id)
            ->first();
        if(!$role){
            return false;
        }
        // change status
        $role->status = $requestData->status;
        $role->save();

        return $role;
    }
    private function getModel() : String
    {
        return Role::class;
    }
}
