<?php
namespace  App\Repositories\Seller;

use App\Helpers\FileUpload;
use App\Http\Requests\Seller\UpdateAdminSellerRequest;
use App\Models\Role;
use App\Models\Seller;
use Illuminate\Support\Facades\Hash;

class AdminListRepository
{
    use FileUpload;
    public function getAllAdminList()
    {
        return $this->getModel()::where('parent_id',auth('sellerApi')->user()->id)
        ->orderBy('id','desc')
        ->get();
    }
    public function createAdmin($data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['parent_id'] = auth('sellerApi')->user()->id;
        $data['currency_id'] = auth('sellerApi')->user()->currency_id;
        $data['approval_status'] = 'approved';
        $adminSeller = $this->getModel()::create($data);
        #make assign role to admin
        #Assign role to the seller
        $role = Role::where('name', $data['role'])->where('guard_name','sellerApi')
        ->where('status','active')
        ->first();
        $adminSeller->assignRole($role);
        $adminSeller->syncPermissions($role->permissions);
        $adminSeller->load('roles', 'permissions');
        return $adminSeller;
    }
    public function updateAdmin($id, array $request)
    {
        $adminSeller = $this->getModelById($id);
        if (isset($request['logo'])) {
            // Save the new logo using the trait method
            $adminSeller->logo = $request['logo'];
        }
        // Update the other fields
        $adminSeller->update($request);
        #check role and assign for admin
        $role = Role::where('name', $request['role'])->first();
        $adminSeller->assignRole($role);
        if (isset($request['permissions']))
            $adminSeller->syncPermissions($request['permissions']);
        return $adminSeller;
    }
    public function getModelById($id){
        return $this->getModel()::with(['roles','permissions.translations'])->where("id",$id)
            ->where('parent_id',auth('sellerApi')->user()->id)
            ->first();
    }
    private function getModel()
    {
        return Seller::class;
    }
}
