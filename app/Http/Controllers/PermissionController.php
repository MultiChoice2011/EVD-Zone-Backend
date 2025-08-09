<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index()
    {
        return view('admin.permissions.index')->with([
            'permissions' => Permission::orderBy('guard_name')->get()
        ]);
    }
}
