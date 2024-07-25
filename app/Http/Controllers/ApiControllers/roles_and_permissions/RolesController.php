<?php

namespace App\Http\Controllers\ApiControllers\roles_and_permissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function get_roles()
    {
        $data = Role::get();
        return response()->json($data);
    }

    public function create(Request $request)
    {
        $role = new Role();
        $role->name = $request->name;
        $role->guard_name = 'sanctum';
        $role->givePermissionTo($request->permissions);
        if ($role->save()){
            return response()->json([
                'success' => true,
                'message' => 'تم اضافة الدور بنجاح'
            ]);
        }
    }
}
