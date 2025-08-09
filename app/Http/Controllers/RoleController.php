<?php

namespace App\Http\Controllers;

use App\Services\Admin\RoleAndPermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends RoleAndPermissionService
{
    public function index()
    {
        return view('admin.roles.index')->with([
           'roles' => Role::with('permissions')->orderBy('guard_name')->get()
        ]);
    }

    public function create()
    {
        return view('admin.roles.create')->with([
            'permissions' => Permission::where('guard_name', 'vendor')->get(['name', 'id'])
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $data = $request->validate([
           "name" => ['required', Rule::unique('roles', 'name')],
           "guard_name" => ['required'],
            "permissions" => ['required', Rule::exists('permissions', 'id')]
        ]);

        $role = Role::create([
            'guard_name' => $data['guard_name'],
            'name' => $data['name']
        ]);

        $role->givePermissionTo($data['permissions']);

        DB::commit();

        return redirect()->route('dashboard.role.index');
    }

    public function destroy($id)
    {
        $role = Role::with('permissions')->where('id', $id)->first();
        $permissions = $role->permissions;
        $role->revokePermissionTo($permissions);
        $role->destroy($id);
        return redirect()->route('dashboard.role.index');
    }
}
