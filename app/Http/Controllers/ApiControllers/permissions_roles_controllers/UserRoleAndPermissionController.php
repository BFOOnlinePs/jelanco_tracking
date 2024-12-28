<?php

namespace App\Http\Controllers\ApiControllers\permissions_roles_controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
        $directPermissions = $user->permissions->pluck('id')->diff($rolesPermissions);

        return response()->json([
            'role_ids' => $roleIds,
            'direct_permission_ids' => $directPermissions,
        ]);
    }


    // Assign roles to a user
    public function assignRoles(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::whereIn('name', $request->roles)->get();
        $user->syncRoles($roles);

        return response()->json(['message' => 'Roles assigned successfully']);
    }

    // Assign permissions to a user
    public function assignPermissions(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $permissions = Permission::whereIn('name', $request->permissions)->get();
        $user->syncPermissions($permissions);

        return response()->json(['message' => 'Permissions assigned successfully']);
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
