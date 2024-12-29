<?php

namespace App\Http\Controllers\ApiControllers\permissions_roles_controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/// was UserController
class UserRoleAndPermissionController extends Controller
{
    // Get all roles and permissions for a user
    public function getRolesPermissions($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    public function getRolesAndPermissions($id)
    {
        $user = User::findOrFail($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // // Get all user roles with their permissions
        // $rolesWithPermissions = $user->roles->map(function ($role) {
        //     return [
        //         'role' => $role->id,
        //         'role_permission_ids' => $role->permissions->pluck('id'),
        //     ];
        // });

        // // Get additional permissions assigned directly to the user
        // $directPermissions = $user->permissions->pluck('id')->diff(
        //     $rolesWithPermissions->pluck('permissions')->flatten()
        // );

        // return response()->json([
        //     'role_with_permission_ids' => $rolesWithPermissions,
        //     'direct_permission_ids' => $directPermissions,
        // ]);

        // Get role IDs
        $roleIds = $user->roles->pluck('id');

        // Get all permissions assigned to roles
        $rolesPermissions = $user->roles->flatMap(function ($role) {
            return $role->permissions->pluck('id');
        })->unique();

        // Get additional permissions assigned directly to the user
        $directPermissions = $user->permissions->pluck('id')
            // ->diff($rolesPermissions)
            ->values()->toArray();


        return response()->json([
            'role_ids' => $roleIds,
            'direct_permission_ids' => $directPermissions,
        ]);
    }


    // Assign roles to a user
    public function assignRoles(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role_ids' => 'required|array',
            'role_ids.*' => 'int|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }


        $roles = Role::whereIn('id', $request->role_ids)->get();
        $user->syncRoles($roles);

        return response()->json([
            'status' => true,
            'message' => 'تم تعيين الأدوار بنجاح'
        ]);
    }

    // Assign permissions to a user
    public function assignPermissions(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'int|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $permissions = Permission::whereIn('id', $request->permission_ids)->get();
        $user->syncPermissions($permissions);

        return response()->json([
            'status' => true,
            'message' => 'تم تعيين الصلاحيات بنجاح'
        ]);
    }

    // Remove a specific role from a user
    public function removeRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->removeRole($request->role);

        return response()->json(['message' => 'Role removed successfully']);
    }

    // Remove a specific permission from a user
    public function removePermission(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->revokePermissionTo($request->permission);

        return response()->json(['message' => 'Permission removed successfully']);
    }
}
