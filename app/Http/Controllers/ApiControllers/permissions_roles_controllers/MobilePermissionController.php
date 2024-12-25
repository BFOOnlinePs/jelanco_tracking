<?php

namespace App\Http\Controllers\ApiControllers\permissions_roles_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class MobilePermissionController extends Controller
{
    // Get all permissions
    public function index()
    {
        return response()->json(Permission::all());
    }

    // Create a new permission
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name',
        ], [
            'name.unique' => 'الصلاحية :input موجودة مسبقا',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة الصلاحية بنجاح',
            'permission' => $permission
        ], 201);
    }

    // // Show a specific permission
    // public function show($id)
    // {
    //     $permission = Permission::findOrFail($id);
    //     return response()->json($permission);
    // }

    // Update a permission
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $request->validate(['name' => 'required|unique:permissions,name,' . $id]);
        $permission->update(['name' => $request->name]);

        return response()->json($permission);
    }

    // Delete a permission
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
