<?php

namespace App\Http\Controllers;

use App\Services\Admin\RoleAndPermissionService;
use Illuminate\Http\Request;
use mysql_xdevapi\Collection;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class RoleAndPermissionController extends Controller
{

    // set the model - the guard name

    public function __construct( public RoleAndPermissionService $service )
    {
        $this->service->setGuard('admin');
    }




    /*
     *  create role :
     *  1- create role
     *  2- create | get - the permissions
     *  3- assign this permission for the role
    */
    public function addRole(Request $request)
    {
        // 1- create role
        $this->service->addRole($request);

    }
    /*
     *  edit role :
     *  1- edit role name.
     *  2- delete all permissions For This Role
     *  3- Add The New Permissions To The Role
     */

    /*
     *  delete role :
     *  1- Edit Role Name.
     *  2- Delete All Permissions For This Role
     *  3- Add The New Permissions To The Role
     */

    // Assign Role To The Model WithThe Permissions
    /*
     *  1- get role with permissions
     *  2- get model
     *  3- assign the role with permissions to the model
     */

    // remove role and permissions from the model


    public function addRoleWithPermissions (string $roleName, array $permissions) {
        // Create Role
        $role = $this->addRole($roleName);

        // Create Permissions
        $this->createPermissions($permissions);
        // Assign Permissions To The Role
       $role->givePermissionTo($permissions);
//
//        $this->model->assignRole($role);
//        $this->model->givePermissionTo($permissions);

        return $this->model;
    }


    /**
     * @param array $permissions
     * @return void
     */
    public function createPermissions(array $permissions): void
    {
        foreach ($permissions as $permission) {
            if (!Permission::where(['name' => $permission, 'guard_name' => $this->guard_name])->first()){
                Permission::create([
                    'guard_name' => $this->guard_name,
                    'name' => $permission
                ]);
            }
        }
    }

    public function deleteRole ($roleName) {

    }
    public function deletePermission ($roleName) {

    }

}
