<?php

namespace App\Http\Controllers\ApiControllers\permissions_roles_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // Get all roles
    public function index()
    {
        return response()->json(Role::all());
    }

    // Create a new role or update an existing role by name
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        $role = Role::updateOrCreate(
            ['name' => $request->name], // Match on name
            ['guard_name' => 'web']
        );

        return response()->json($role, 201);
    }

    // // Show a specific role
    // public function show($id)
    // {
    //     $role = Role::findOrFail($id);
    //     return response()->json($role);
    // }

    // // Update a role
    // public function update(Request $request, $id)
    // {
    //     $role = Role::findOrFail($id);
    //     $request->validate(['name' => 'required|unique:roles,name,' . $id]);
    //     $role->update(['name' => $request->name, 'guard_name' => 'web']);

    //     return response()->json($role);
    // }

    // // Delete a role
    // public function destroy($id)
    // {
    //     $role = Role::findOrFail($id);
    //     $role->delete();

    //     return response()->json(['message' => 'تم حذف الدور بنجاح']);
    // }

    // Assign permissions to a role (send all the selected with the old one)
    public function assignPermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::whereIn('name', $request->permissions)->get();
        $role->syncPermissions($permissions);

        return response()->json(['message' => 'تم تعيين صلاحيات الدور بنجاح']);
    }
}
