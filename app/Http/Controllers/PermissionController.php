<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index()
    {
        $data = Role::with('permissions')->get();
        return view('project.permissions.index',['data'=>$data]);
    }

    public function add()
    {
        $permissions = Permission::get();
        return view('project.permissions.add' , ['permissions'=>$permissions]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $check_role = Role::where('name',$request->name)->first();
        if ($check_role){
            return redirect()->back()->with('fail', 'اسم الدور موجود مسبقا');
        }
        else{
            $role = Role::create(['name' => $request->input('name')]);
        }

        if ($request->has('permissions')) {
            $permissions = $request->input('permissions'); // Array of permission IDs
            $permissions = array_map('intval', $request->input('permissions'));
            $role->syncPermissions($permissions); // Pass array directly
        }

        return redirect()->back()->with('success', 'تم انشاء الصلاحية بنجاح .');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $assignedPermissions = $role->permissions->pluck('id')->toArray();
        return view('project.permissions.edit', ['role'=>$role, 'permissions'=>$permissions, 'assignedPermissions'=>$assignedPermissions]);
    }

    public function update(Request $request)
    {
        $role = Role::findOrFail($request->id);

        $check_role = Role::where('name', $request->name)->where('id', '!=', $request->id)->first();
        if ($check_role) {
            return redirect()->back()->with('fail', 'اسم الدور موجود مسبقا');
        } else {
            $role->name = $request->input('name');
            $role->save();
        }

        if ($request->has('permissions')) {
            $permissions = $request->input('permissions'); // Array of permission IDs
            $permissions = array_map('intval', $permissions);
            $role->syncPermissions($permissions); // Pass array directly
        }

        return redirect()->route('permissions.index')->with('success', 'تم تحديث الصلاحية بنجاح .');
    }
}
