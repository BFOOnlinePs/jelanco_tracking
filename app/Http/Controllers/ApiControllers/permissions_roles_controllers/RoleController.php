<?php

namespace App\Http\Controllers\ApiControllers\permissions_roles_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // Get all roles
    public function index(Request $request)
    {
        if ($request->has('with_permissions') && $request->with_permissions) {
            // Get roles with their permissions, ordered by id
            $roles = Role::with('permissions')->orderBy('id')->get()->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                        ];
                    }),
                ];
            });
        } else {
            // Get roles without permissions, ordered by id
            $roles = Role::orderBy('id')->get(['id', 'name']);
        }

        return response()->json($roles);
    }



    public function getAllRolesWithPermissions()
    {
        $roles = Role::with('permissions')->get();

        return response()->json(['roles' => $roles]);
    }


    // Create a new role
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
        ], [
            'name.unique' => 'الدور :input موجود مسبقا',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة الدور بنجاح',
            'role' => $role
        ], 201);
    }


    // Update a role
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $id
        ], [
            'name.unique' => 'الدور :input موجود مسبقا',
            'name.required' => 'الرجاء كتابة الاسم'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'الدور غير موجود',
            ], 404);
        }

        $role->update([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الدور بنجاح',
            'role' => $role
        ]);
    }


    // // Delete a role
    // public function destroy($id)
    // {
    //     $role = Role::findOrFail($id);
    //     $role->delete();

    //     return response()->json(['message' => 'تم حذف الدور بنجاح']);
    // }


    // Assign permissions to a role (send all the selected with the old permissions)
    public function assignPermissions(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'الدور غير موجود'
            ]);
        }

        $permissions = Permission::whereIn('id', $request->permissionIds)->get();
        $role->syncPermissions($permissions);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث صلاحيات الدور بنجاح'
        ]);
    }
}
